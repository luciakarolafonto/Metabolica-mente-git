<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Messaggio inviato dal form "Contatti" del sito all'associazione.
 */
class ContattoMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{nome:string,email:string,telefono:?string,messaggio:string}  $dati
     */
    public function __construct(public array $dati)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuovo messaggio dal sito - '.$this->dati['nome'],
            replyTo: [$this->dati['email']],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contatto',
            with: ['dati' => $this->dati],
        );
    }
}
