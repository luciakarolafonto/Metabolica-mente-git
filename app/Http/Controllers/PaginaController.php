<?php

namespace App\Http\Controllers;

use App\Mail\ContattoMail;
use App\Models\Evento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

/**
 * Le pagine pubbliche del sito vetrina.
 */
class PaginaController extends Controller
{
    public function home(): View
    {
        return view('pages.home', [
            // I prossimi appuntamenti pubblicati. Se non ce ne sono,
            // la home ripiega sugli orari fissi scritti in config/asd.php.
            'eventi' => Evento::pubblicati()->inProgramma()->limit(3)->get(),
        ]);
    }

    public function metodo(): View
    {
        return view('pages.metodo');
    }

    public function chiSiamo(): View
    {
        return view('pages.chi-siamo');
    }

    public function contatti(): View
    {
        return view('pages.contatti');
    }

    public function inviaContatto(Request $request): RedirectResponse
    {
        $dati = $request->validate([
            'nome'      => ['required', 'string', 'max:120'],
            'email'     => ['required', 'email', 'max:180'],
            'telefono'  => ['nullable', 'string', 'max:40'],
            'messaggio' => ['required', 'string', 'min:10', 'max:2000'],
            // Campo invisibile: se e' pieno chi ha scritto e' un robot.
            'website'   => ['nullable', 'size:0'],
        ]);

        try {
            Mail::to(config('asd.email'))->send(new ContattoMail($dati));
        } catch (\Throwable $e) {
            Log::error('Invio messaggio di contatto fallito: '.$e->getMessage());

            return back()
                ->withInput()
                ->with('errore', 'Non siamo riusciti a inviare il messaggio. Riprova tra poco oppure scrivici su Instagram.');
        }

        return redirect()
            ->route('contatti')
            ->with('successo', 'Messaggio inviato! Ti risponderemo il prima possibile.');
    }
}
