<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegistrazioneController extends Controller
{
    public function mostra(): View
    {
        return view('auth.registrazione');
    }

    public function registra(Request $request): RedirectResponse
    {
        $dati = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'surname'  => ['required', 'string', 'max:100'],
            'email'    => ['required', 'string', 'email', 'max:180', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:40'],
            // 'confirmed' controlla da solo il campo password_confirmation.
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'privacy'  => ['accepted'],
        ]);

        $user = User::create([
            'name'     => $dati['name'],
            'surname'  => $dati['surname'],
            'email'    => $dati['email'],
            'phone'    => $dati['phone'] ?? null,
            'password' => $dati['password'], // il cast 'hashed' del model la cifra
        ]);

        // Fa partire la mail di conferma indirizzo.
        // Se il server di posta non risponde non deve saltare la
        // registrazione: l'account resta creato e dalla pagina successiva
        // si puo' chiedere di rimandare il messaggio.
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::error('Mail di conferma non inviata a '.$user->email.': '.$e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}
