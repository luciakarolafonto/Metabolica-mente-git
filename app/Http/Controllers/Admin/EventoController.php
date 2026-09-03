<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use App\Models\Prenotazione;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Gestione degli appuntamenti da parte della trainer:
 * crea, modifica, pubblica, annulla e vede chi si e' prenotato.
 */
class EventoController extends Controller
{
    public function index(): View
    {
        return view('admin.eventi.index', [
            'eventi' => Evento::withCount('prenotazioniAttive')->orderByDesc('inizia_il')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.eventi.form', ['evento' => new Evento()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dati = $this->valida($request);

        $dati['slug'] = Evento::slugDa($dati['titolo']);

        $evento = Evento::create($dati);

        return redirect()
            ->route('admin.eventi.index')
            ->with('successo', 'Appuntamento "'.$evento->titolo.'" creato.');
    }

    public function edit(Evento $evento): View
    {
        return view('admin.eventi.form', ['evento' => $evento]);
    }

    public function update(Request $request, Evento $evento): RedirectResponse
    {
        $dati = $this->valida($request);

        $dati['slug'] = Evento::slugDa($dati['titolo'], $evento->id);

        $evento->update($dati);

        return redirect()
            ->route('admin.eventi.index')
            ->with('successo', 'Appuntamento aggiornato.');
    }

    public function destroy(Evento $evento): RedirectResponse
    {
        $quante = $evento->prenotazioniAttive()->count();

        if ($quante > 0) {
            return back()->with(
                'errore',
                "Non puoi eliminare un appuntamento con {$quante} prenotazioni attive. ".
                'Se non si fa più, mettilo su "annullato": le persone lo vedranno e capiranno.'
            );
        }

        $titolo = $evento->titolo;
        $evento->delete();

        return redirect()
            ->route('admin.eventi.index')
            ->with('successo', 'Appuntamento "'.$titolo.'" eliminato.');
    }

    /**
     * Elenco di chi si e' prenotato, con il metodo di pagamento scelto.
     */
    public function partecipanti(Evento $evento): View
    {
        $prenotazioni = $evento->prenotazioni()
            ->with(['user', 'coupon'])
            ->orderBy('stato')
            ->orderBy('created_at')
            ->get();

        return view('admin.eventi.partecipanti', [
            'evento'       => $evento,
            'prenotazioni' => $prenotazioni,
            'incassoAtteso' => $prenotazioni
                ->where('stato', Prenotazione::CONFERMATA)
                ->sum(fn ($p) => (float) $p->importo),
            'incassato' => $prenotazioni
                ->where('stato', Prenotazione::CONFERMATA)
                ->where('pagato', true)
                ->sum(fn ($p) => (float) $p->importo),
        ]);
    }

    /**
     * Segna una prenotazione come pagata (o torna indietro se ci si sbaglia).
     */
    public function segnaPagamento(Request $request, Prenotazione $prenotazione): RedirectResponse
    {
        $pagato = $request->boolean('pagato');

        // Il registro dei pagamenti e la prenotazione restano allineati:
        // se ne occupa il modello Pagamento.
        $pagamento = $prenotazione->pagamento;

        if ($pagamento) {
            $pagato
                ? $pagamento->segnaPagato($request->user())
                : $pagamento->segnaDaPagare();
        } else {
            $prenotazione->update([
                'pagato'    => $pagato,
                'pagato_il' => $pagato ? now() : null,
            ]);
        }

        return back()->with(
            'successo',
            $pagato
                ? 'Segnata come pagata: '.$prenotazione->user->full_name
                : 'Segnata come da pagare: '.$prenotazione->user->full_name
        );
    }

    /**
     * @return array<string,mixed>
     */
    private function valida(Request $request): array
    {
        $dati = $request->validate([
            'titolo'      => ['required', 'string', 'max:150'],
            'sommario'    => ['nullable', 'string', 'max:255'],
            'descrizione' => ['nullable', 'string', 'max:5000'],
            'luogo'       => ['required', 'string', 'max:150'],
            'ritrovo'     => ['nullable', 'string', 'max:200'],
            'inizia_il'   => ['required', 'date'],
            'finisce_il'  => ['nullable', 'date', 'after:inizia_il'],
            'posti'       => ['nullable', 'integer', 'min:1', 'max:500'],
            'prezzo'      => ['required', 'numeric', 'min:0', 'max:9999'],
            'stato'       => ['required', Rule::in([Evento::BOZZA, Evento::PUBBLICATO, Evento::ANNULLATO])],

            'coupon_attivo'   => ['nullable', 'boolean'],
            'coupon_titolo'   => ['nullable', 'string', 'max:120'],
            'coupon_valore'   => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'coupon_scadenza' => ['nullable', 'date'],
        ], [], [
            'inizia_il'  => 'data e ora di inizio',
            'finisce_il' => 'data e ora di fine',
        ]);

        // La casella di spunta non arriva quando e' vuota: la forziamo noi.
        $dati['coupon_attivo'] = $request->boolean('coupon_attivo');

        return $dati;
    }
}
