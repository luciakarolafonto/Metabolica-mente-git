<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

/**
 * La mail "conferma il tuo indirizzo" che parte subito dopo la registrazione.
 * Sostituisce quella standard di Laravel, che e' in inglese e senza grafica.
 */
class VerificaEmail extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Conferma la tua email - '.config('asd.name'))
            ->view('emails.verifica', [
                'user' => $notifiable,
                'url'  => $this->linkDiVerifica($notifiable),
            ]);
    }

    /**
     * Link "firmato" e con scadenza: se qualcuno prova a modificarlo a mano
     * Laravel se ne accorge e lo rifiuta.
     */
    private function linkDiVerifica(object $notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id'   => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
