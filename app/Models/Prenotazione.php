<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prenotazione extends Model
{
    use HasFactory, GeneraCodice;

    protected $table = 'prenotazioni';

    public const CONFERMATA = 'confermata';
    public const ANNULLATA  = 'annullata';

    /**
     * I modi in cui si puo' pagare. La scelta serve soprattutto alla
     * trainer, che cosi' sa in anticipo chi paga come.
     */
    public const METODI = [
        'contanti' => 'Contanti al ritrovo',
        'carta'    => 'Carta (POS al ritrovo)',
        'bonifico' => 'Bonifico bancario',
    ];

    protected $fillable = [
        'evento_id', 'user_id', 'codice', 'posti', 'importo',
        'metodo', 'pagato', 'pagato_il', 'stato', 'annullata_il',
        'coupon_id', 'note', 'promemoria_inviato_il',
    ];

    protected function casts(): array
    {
        return [
            'importo'               => 'decimal:2',
            'posti'                 => 'integer',
            'pagato'                => 'boolean',
            'pagato_il'             => 'datetime',
            'annullata_il'          => 'datetime',
            'promemoria_inviato_il' => 'datetime',
        ];
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function pagamento(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Pagamento::class);
    }

    public function isAnnullata(): bool
    {
        return $this->stato === self::ANNULLATA;
    }

    public function isGratuita(): bool
    {
        return (float) $this->importo <= 0;
    }

    public function etichettaMetodo(): string
    {
        return self::METODI[$this->metodo] ?? $this->metodo;
    }

    public function etichettaStato(): string
    {
        return match (true) {
            $this->isAnnullata() => 'Annullata',
            $this->pagato        => 'Pagata',
            $this->isGratuita()  => 'Confermata',
            default              => 'Da pagare',
        };
    }
}
