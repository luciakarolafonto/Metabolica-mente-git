<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Mail con il link per scegliere una nuova password.
 */
class ReimpostaPassword extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage())
            ->subject('Reimposta la password - '.config('asd.name'))
            ->view('emails.password', [
                'user'    => $notifiable,
                'url'     => $url,
                'minuti'  => config('auth.passwords.users.expire', 60),
            ]);
    }
}
