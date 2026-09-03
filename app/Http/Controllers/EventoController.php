<?php

namespace App\Http\Controllers;

use App\Mail\CouponMail;
use App\Mail\PrenotazioneMail;
use App\Models\Coupon;
use App\Models\Evento;
use App\Models\Prenotazione;
use App\Services\GestorePagamenti;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gli appuntamenti visti dal lato pubblico: elenco, dettaglio,
 * prenotazione e ritiro del coupon dedicato all'evento.
 */
class EventoController extends Controller
{
    public function __construct(private GestorePagamenti $pagamenti)
    {
    }

    public function index(): View
    {
        return view('eventi.index', [
            'inProgramma' => Evento::pubblicati()->inProgramma()->withCount('prenotazioniAttive')->get(),
            'passati'     => Evento::pubblicati()->passati()->limit(6)->get(),
        ]);
    }

    public function show(Evento $evento): View
    {
        abort_if($evento->stato === Evento::BOZZA && ! optional(auth()->user())->is_staff, 404);

        $user = auth()->user();

        return view('eventi.show', [
            'evento'       => $evento,
            'prenotazione' => $evento->prenotazioneDi($user),
            'coupon'       => $evento->couponDi($user),
            'metodi'       => $this->pagamenti->metodiDisponibili(),
        ]);
    }

    /**
     * Crea la prenotazione. Il metodo di pagamento viene solo dichiarato:
     * il denaro non passa dal sito, serve alla trainer per sapere in
     * anticipo chi paga in contanti, chi con carta e chi con bonifico.
     */
    public function prenota(Request $request, Evento $evento): RedirectResponse
    {
        $user = $request->user();

        if ($motivo = $evento->motivoNonPrenotabile()) {
            return back()->with('errore', $motivo);
        }

        if ($evento->prenotazioneDi($user)) {
            return back()->with('info', 'Sei già prenotato per questo appuntamento.');
        }

        $rimasti = $evento->postiRimasti();

        $metodiAmmessi = array_keys($this->pagamenti->metodiDisponibili());

        $dati = $request->validate([
            'posti'  => ['required', 'integer', 'min:1', 'max:'.($rimasti !== null ? min($rimasti, 10) : 10)],
            'metodo' => ['required', Rule::in($metodiAmmessi)],
            'note'   => ['nullable', 'string', 'max:500'],
            'usa_coupon' => ['nullable', 'boolean'],
        ], [
            'posti.max'  => 'Non ci sono abbastanza posti liberi per questa richiesta.',
            'metodo.in'  => 'Scegli uno dei metodi di pagamento proposti.',
        ]);

        $coupon = null;
        $importo = (float) $evento->prezzo * (int) $dati['posti'];

        if (! empty($dati['usa_coupon'])) {
            $coupon = $evento->couponDi($user);

            if ($coupon && $coupon->isValido()) {
                $importo = $coupon->applicaSconto($importo);
            } else {
                $coupon = null;
            }
        }

        // Transazione: o si salva tutto, o non si salva niente.
        $prenotazione = DB::transaction(function () use ($evento, $user, $dati, $importo, $coupon) {
            $p = Prenotazione::create([
                'evento_id' => $evento->id,
                'user_id'   => $user->id,
                'codice'    => Prenotazione::generaCodice('codice'),
                'posti'     => $dati['posti'],
                'importo'   => $importo,
                'metodo'    => $dati['metodo'],
                'stato'     => Prenotazione::CONFERMATA,
                'pagato'    => $importo <= 0,
                'pagato_il' => $importo <= 0 ? now() : null,
                'coupon_id' => $coupon?->id,
                'note'      => $dati['note'] ?? null,
            ]);

            if ($coupon) {
                $coupon->update([
                    'status'  => Coupon::USED,
                    'used_at' => now(),
                ]);
            }

            // La riga di pagamento nasce insieme alla prenotazione.
            $this->pagamenti->creaPer($p, $dati['metodo']);

            return $p;
        });

        $this->inviaMailPrenotazione($prenotazione);

        // Se c'e' qualcosa da pagare, si passa dalla pagina del pagamento,
        // dove ci sono le istruzioni per il metodo scelto.
        if ((float) $prenotazione->importo > 0) {
            return redirect()
                ->route('pagamenti.show', $prenotazione->pagamento)
                ->with('successo', 'Prenotazione confermata! Il tuo codice è '.$prenotazione->codice.'.');
        }

        return redirect()
            ->route('eventi.show', $evento)
            ->with('successo', 'Prenotazione confermata! Il tuo codice è '.$prenotazione->codice.'.');
    }

    public function annulla(Request $request, Evento $evento): RedirectResponse
    {
        $prenotazione = $evento->prenotazioneDi($request->user());

        if (! $prenotazione || $prenotazione->isAnnullata()) {
            return back()->with('info', 'Non risulta nessuna prenotazione attiva da annullare.');
        }

        DB::transaction(function () use ($prenotazione) {
            $prenotazione->update([
                'stato'        => Prenotazione::ANNULLATA,
                'annullata_il' => now(),
            ]);

            // Se aveva usato un coupon e non e' scaduto, glielo restituiamo.
            $coupon = $prenotazione->coupon;
            if ($coupon && ! $coupon->isScaduto()) {
                $coupon->update(['status' => Coupon::ACTIVE, 'used_at' => null]);
            }

            // Il pagamento in attesa non ha piu' motivo di esistere.
            $pagamento = $prenotazione->pagamento;
            if ($pagamento && $pagamento->isInAttesa()) {
                $pagamento->update(['stato' => \App\Models\Pagamento::ANNULLATO]);
            }
        });

        return back()->with('successo', 'Prenotazione annullata. Se cambi idea puoi rifarla, posti permettendo.');
    }

    /**
     * Ritiro del coupon dedicato all'evento: uno solo per persona.
     */
    public function ritiraCoupon(Request $request, Evento $evento): RedirectResponse
    {
        $user = $request->user();

        if (! $evento->couponRitirabile()) {
            return back()->with('errore', 'Per questo appuntamento non è previsto nessun coupon.');
        }

        if ($evento->couponDi($user)) {
            return back()->with('info', 'Hai già ritirato il coupon di questo appuntamento.');
        }

        $coupon = Coupon::creaPerEvento($user, $evento);

        try {
            Mail::to($user->email)->send(new CouponMail($coupon));
            $messaggio = 'Coupon ritirato! Te lo abbiamo mandato anche via email.';
        } catch (\Throwable $e) {
            Log::error('Invio coupon evento fallito: '.$e->getMessage());
            $messaggio = 'Coupon ritirato! Puoi scaricarlo dalla tua area personale.';
        }

        return back()->with('successo', $messaggio);
    }

    private function inviaMailPrenotazione(Prenotazione $prenotazione): void
    {
        try {
            Mail::to($prenotazione->user->email)->send(new PrenotazioneMail($prenotazione));
        } catch (\Throwable $e) {
            Log::error('Invio conferma prenotazione fallito: '.$e->getMessage());
        }
    }
}
