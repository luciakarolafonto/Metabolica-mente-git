<?php

namespace App\Mail;

use App\Models\Coupon;
use App\Services\CouponTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * La mail che consegna il coupon, con il biglietto allegato
 * sia in PDF (da stampare) sia in PNG (da tenere sul telefono).
 */
class CouponMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * L'immagine del biglietto viene disegnata una volta sola e poi
     * riusata sia dentro il messaggio sia come allegato.
     */
    private ?string $png = null;

    public function __construct(public Coupon $coupon)
    {
    }

    private function immagine(): string
    {
        return $this->png ??= app(CouponTicket::class)->png($this->coupon);
    }

    public function envelope(): Envelope
    {
        $oggetto = $this->coupon->isDiProva()
            ? 'Il tuo coupon di prova gratuita'
            : 'Il tuo coupon per '.$this->coupon->evento?->titolo;

        return new Envelope(
            subject: $oggetto.' - '.config('asd.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.coupon',
            with: [
                'coupon'   => $this->coupon,
                'user'     => $this->coupon->user,
                'immagine' => $this->immagine(),
            ],
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $ticket = app(CouponTicket::class);

        return [
            Attachment::fromData(fn () => $ticket->pdf($this->coupon), $ticket->nomeFile($this->coupon, 'pdf'))
                ->withMime('application/pdf'),

            Attachment::fromData(fn () => $this->immagine(), $ticket->nomeFile($this->coupon, 'png'))
                ->withMime('image/png'),
        ];
    }
}
