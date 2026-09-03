<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as RegolaPassword;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * "Password dimenticata": invio del link e scelta della nuova password.
 */
class PasswordController extends Controller
{
    public function mostraRichiesta(): View
    {
        return view('auth.password-dimenticata');
    }

    public function inviaLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $esito = Password::sendResetLink($request->only('email'));

        if ($esito !== Password::RESET_LINK_SENT) {
            throw ValidationException::withMessages([
                'email' => 'Non troviamo nessun account con questo indirizzo.',
            ]);
        }

        return back()->with('successo', 'Ti abbiamo inviato il link per reimpostare la password.');
    }

    public function mostraReset(Request $request, string $token): View
    {
        return view('auth.password-reset', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function salvaNuova(Request $request): RedirectResponse
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', RegolaPassword::min(8)->letters()->numbers()],
        ]);

        $esito = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password'       => $request->password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($esito !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages([
                'email' => 'Il link non è più valido. Richiedine uno nuovo.',
            ]);
        }

        return redirect()->route('accesso')->with('successo', 'Password aggiornata. Ora puoi accedere.');
    }
}
