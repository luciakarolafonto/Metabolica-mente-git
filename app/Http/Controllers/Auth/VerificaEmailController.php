<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Gestisce la conferma dell'indirizzo email:
 *  - avviso()   la pagina "controlla la posta"
 *  - conferma() il click sul link ricevuto per mail
 *  - rinvia()   il pulsante "non mi e' arrivata niente"
 */
class VerificaEmailController extends Controller
{
    public function avviso(Request $request): RedirectResponse|View
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('coupon.index');
        }

        return view('auth.verifica-email', [
            // Scorciatoia per lo sviluppo: finche' le mail non partono davvero
            // mostriamo il link di conferma direttamente in pagina, altrimenti
            // ci si resta bloccati. Sparisce da sola appena si configura Gmail.
            'linkSviluppo' => $this->linkPerLoSviluppo($request),
        ]);
    }

    /**
     * Restituisce il link di conferma solo se siamo in locale E le mail
     * finiscono nel file di log. In tutti gli altri casi torna null.
     * Il link e' comunque quello dell'utente collegato, non di altri.
     */
    private function linkPerLoSviluppo(Request $request): ?string
    {
        if (! app()->environment('local') || config('mail.default') !== 'log') {
            return null;
        }

        return \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id'   => $request->user()->getKey(),
                'hash' => sha1($request->user()->getEmailForVerification()),
            ]
        );
    }

    /**
     * EmailVerificationRequest controlla da solo che la firma del link
     * sia valida, non scaduta e che corrisponda all'utente collegato.
     */
    public function conferma(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('coupon.index')
                ->with('info', 'Il tuo indirizzo era già confermato.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect()->route('coupon.index')
            ->with('successo', 'Email confermata! Ora puoi ritirare il tuo coupon di prova gratuita.');
    }

    public function rinvia(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('coupon.index');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('successo', 'Ti abbiamo rimandato la mail di conferma.');
    }
}
