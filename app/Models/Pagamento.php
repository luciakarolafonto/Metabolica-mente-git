<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pagamento extends Model
{
    use HasFactory, GeneraCodice;

    protected $table = 'pagamenti';

    public const IN_ATTESA  = 'in_attesa';
    public const PAGATO     = 'pagato';
    public const ANNULLATO  = 'annullato';
    public const RIMBORSATO = 'rimborsato';

    protected $fillable = [
        'prenotazione_id', 'user_id', 'codice', 'importo',
        'metodo', 'stato', 'pagato_il', 'registrato_da',
        'fornitore', 'riferimento_esterno', 'dati_fornitore', 'note',
    ];

    protected function casts(): array
    {
        return [
            'importo'   => 'decimal:2',
            'pagato_il' => 'datetime',
        ];
    }

    public function prenotazione(): BelongsTo
    {
        return $this->belongsTo(Prenotazione::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ------------------------------------------------------------------
    //  Stato
    // ------------------------------------------------------------------

    public function isPagato(): bool
    {
        return $this->stato === self::PAGATO;
    }

    public function isInAttesa(): bool
    {
        return $this->stato === self::IN_ATTESA;
    }

    public function isGratuito(): bool
    {
        return (float) $this->importo <= 0;
    }

    /**
     * Il pagamento si fa sul sito oppure di persona?
     */
    public function isOnline(): bool
    {
        return (bool) (config("pagamenti.metodi.{$this->metodo}.online") ?? false);
    }

    public function etichettaMetodo(): string
    {
        return config("pagamenti.metodi.{$this->metodo}.etichetta", $this->metodo);
    }

    public function istruzioniMetodo(): string
    {
        return config("pagamenti.metodi.{$this->metodo}.descrizione", '');
    }

    public function iconaMetodo(): string
    {
        return config("pagamenti.metodi.{$this->metodo}.icona", 'bi-wallet2');
    }

    public function etichettaStato(): string
    {
        return match ($this->stato) {
            self::PAGATO     => 'Pagato',
            self::ANNULLATO  => 'Annullato',
            self::RIMBORSATO => 'Rimborsato',
            default          => $this->isGratuito() ? 'Nulla da pagare' : 'Da pagare',
        };
    }

    public function importoLeggibile(): string
    {
        return number_format((float) $this->importo, 2, ',', '.').' €';
    }

    // ------------------------------------------------------------------
    //  Azioni
    // ------------------------------------------------------------------

    /**
     * Segna il pagamento come incassato e allinea la prenotazione.
     */
    public function segnaPagato(?User $daChi = null, ?string $riferimento = null): void
    {
        $this->update([
            'stato'               => self::PAGATO,
            'pagato_il'           => now(),
            'registrato_da'       => $daChi?->id,
            'riferimento_esterno' => $riferimento ?: $this->riferimento_esterno,
        ]);

        $this->prenotazione?->update([
            'pagato'    => true,
            'pagato_il' => now(),
        ]);
    }

    /**
     * Torna indietro: capita se la trainer si sbaglia a segnare.
     */
    public function segnaDaPagare(): void
    {
        $this->update([
            'stato'         => self::IN_ATTESA,
            'pagato_il'     => null,
            'registrato_da' => null,
        ]);

        $this->prenotazione?->update([
            'pagato'    => false,
            'pagato_il' => null,
        ]);
    }
}
