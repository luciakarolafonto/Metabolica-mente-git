<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AccessoController extends Controller
{
    public function mostra(): View
    {
        return view('auth.accesso');
    }

    public function entra(Request $request): RedirectResponse
    {
        $credenziali = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credenziali, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email o password non corrette.',
            ]);
        }

        // Cambia l'identificativo di sessione: difesa contro il session fixation.
        $request->session()->regenerate();

        return redirect()->intended(route('coupon.index'));
    }

    public function esci(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('successo', 'Sei uscito dal tuo account. A presto!');
    }
}
