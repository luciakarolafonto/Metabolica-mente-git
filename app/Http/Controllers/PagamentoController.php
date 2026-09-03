<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use App\Services\GestorePagamenti;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * La sezione pagamenti vista dal cliente: l'elenco dei suoi pagamenti
 * e la pagina con le istruzioni per quello che deve ancora saldare.
 */
class PagamentoController extends Controller
{
    public function __construct(private GestorePagamenti $gestore)
    {
    }

    public function index(Request $request): View
    {
        $pagamenti = $request->user()
            ->pagamenti()
            ->with(['prenotazione.evento'])
            ->get();

        return view('pagamenti.index', [
            'pagamenti' => $pagamenti,
            'daSaldare' => $pagamenti->where('stato', Pagamento::IN_ATTESA)
                ->sum(fn ($p) => (float) $p->importo),
        ]);
    }

    public function show(Request $request, Pagamento $pagamento): View
    {
        $this->assicuratiCheSiaSuo($request, $pagamento);

        return view('pagamenti.show', [
            'pagamento' => $pagamento->load('prenotazione.evento'),
            'causale'   => $this->gestore->causaleBonifico($pagamento),
            'bonifico'  => config('pagamenti.bonifico'),
        ]);
    }

    /**
     * Cambia il metodo di pagamento scelto, finché non è stato saldato.
     */
    public function cambiaMetodo(Request $request, Pagamento $pagamento): RedirectResponse
    {
        $this->assicuratiCheSiaSuo($request, $pagamento);

        if (! $pagamento->isInAttesa()) {
            return back()->with('info', 'Questo pagamento non è più modificabile.');
        }

        $dati = $request->validate([
            'metodo' => ['required', 'string'],
        ]);

        if (! $this->gestore->metodoValido($dati['metodo'])) {
            return back()->with('errore', 'Quel metodo di pagamento non è disponibile.');
        }

        $pagamento->update(['metodo' => $dati['metodo']]);

        $pagamento->prenotazione?->update(['metodo' => $dati['metodo']]);

        return back()->with('successo', 'Metodo aggiornato: '.$pagamento->etichettaMetodo().'. La trainer lo vedrà.');
    }

    /**
     * Avvia il pagamento con carta sul sito.
     * Finché Stripe non è collegato, questa rotta esiste ma dice
     * chiaramente che non è ancora attiva: non finge nulla.
     */
    public function paga(Request $request, Pagamento $pagamento): RedirectResponse
    {
        $this->assicuratiCheSiaSuo($request, $pagamento);

        if (! $pagamento->isInAttesa()) {
            return back()->with('info', 'Questo pagamento risulta già saldato.');
        }

        try {
            $url = $this->gestore->avviaPagamentoOnline($pagamento);
        } catch (\Throwable $e) {
            Log::warning('Pagamento online non disponibile: '.$e->getMessage());

            return back()->with(
                'errore',
                'Il pagamento con carta sul sito non è ancora attivo. '.
                'Puoi pagare al ritrovo scegliendo contanti o carta con il POS.'
            );
        }

        return redirect()->away($url);
    }

    /**
     * Nessuno può vedere il pagamento di un altro.
     */
    private function assicuratiCheSiaSuo(Request $request, Pagamento $pagamento): void
    {
        abort_unless($pagamento->user_id === $request->user()->id, 403, 'Questo pagamento non è tuo.');
    }
}
