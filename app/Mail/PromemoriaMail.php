<?php

namespace App\Mail;

use App\Models\Prenotazione;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * "La camminata e' oggi": promemoria che parte la mattina stessa
 * a chi ha una prenotazione attiva.
 */
class PromemoriaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Prenotazione $prenotazione)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'È oggi! '.$this->prenotazione->evento->titolo
                .' alle '.$this->prenotazione->evento->inizia_il->format('H:i'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.promemoria',
            with: [
                'prenotazione' => $this->prenotazione,
                'evento'       => $this->prenotazione->evento,
                'user'         => $this->prenotazione->user,
            ],
        );
    }
}
