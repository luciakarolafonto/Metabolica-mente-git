<?php

namespace App\Mail;

use App\Models\Prenotazione;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Conferma della prenotazione a un appuntamento, con codice,
 * importo e metodo di pagamento dichiarato.
 */
class PrenotazioneMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Prenotazione $prenotazione)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Prenotazione confermata - '.$this->prenotazione->evento->titolo,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prenotazione',
            with: [
                'prenotazione' => $this->prenotazione,
                'evento'       => $this->prenotazione->evento,
                'user'         => $this->prenotazione->user,
            ],
        );
    }
}
