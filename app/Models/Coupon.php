<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodice;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupon extends Model
{
    use HasFactory, GeneraCodice;

    public const ACTIVE  = 'active';
    public const USED    = 'used';
    public const EXPIRED = 'expired';

    public const TIPO_PROVA  = 'prova';
    public const TIPO_EVENTO = 'evento';

    protected $fillable = [
        'user_id',
        'code',
        'tipo',
        'evento_id',
        'ambito',
        'titolo',
        'valore',
        'status',
        'issued_at',
        'expires_at',
        'used_at',
        'used_by',
    ];

    protected function casts(): array
    {
        return [
            'issued_at'  => 'datetime',
            'expires_at' => 'datetime',
            'used_at'    => 'datetime',
            'valore'     => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    /**
     * La prenotazione a cui il coupon e' stato applicato, se c'e'.
     */
    public function prenotazione(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Prenotazione::class);
    }

    // ------------------------------------------------------------------
    //  Creazione
    // ------------------------------------------------------------------

    /**
     * Il coupon gratuito di benvenuto: uno solo per persona.
     */
    public static function creaDiProva(User $user): self
    {
        return static::create([
            'user_id'    => $user->id,
            'code'       => static::generaCodice(),
            'tipo'       => self::TIPO_PROVA,
            'ambito'     => self::TIPO_PROVA,
            'titolo'     => 'Coupon di prova',
            'valore'     => config('asd.coupon.value'),
            'status'     => self::ACTIVE,
            'issued_at'  => now(),
            'expires_at' => now()->addDays(config('asd.coupon.days'))->endOfDay(),
        ]);
    }

    /**
     * Il coupon legato a un evento: uno solo per persona per evento.
     */
    public static function creaPerEvento(User $user, Evento $evento): self
    {
        return static::create([
            'user_id'    => $user->id,
            'code'       => static::generaCodice(),
            'tipo'       => self::TIPO_EVENTO,
            'evento_id'  => $evento->id,
            'ambito'     => 'evento-'.$evento->id,
            'titolo'     => $evento->coupon_titolo ?: ('Coupon '.$evento->titolo),
            'valore'     => $evento->coupon_valore ?? 0,
            'status'     => self::ACTIVE,
            'issued_at'  => now(),
            'expires_at' => $evento->scadenzaCoupon(),
        ]);
    }

    // ------------------------------------------------------------------
    //  Stato
    // ------------------------------------------------------------------

    public function isDiProva(): bool
    {
        return $this->tipo === self::TIPO_PROVA;
    }

    /**
     * Applica il coupon a un importo.
     * Valore 0 (o mancante) vuol dire ingresso omaggio: si azzera tutto.
     * Un valore maggiore di zero e' invece uno sconto in euro.
     */
    public function applicaSconto(float $importo): float
    {
        $valore = (float) $this->valore;

        if ($valore <= 0) {
            return 0.0;
        }

        return max(0.0, round($importo - $valore, 2));
    }

    /**
     * Come si descrive il vantaggio del coupon, in parole.
     */
    public function descrizioneVantaggio(): string
    {
        $valore = (float) $this->valore;

        if ($this->isDiProva()) {
            return 'Una lezione di prova gratuita';
        }

        return $valore > 0
            ? 'Sconto di '.rtrim(rtrim(number_format($valore, 2, ',', ''), '0'), ',').' €'
            : 'Ingresso omaggio';
    }

    public function isScaduto(): bool
    {
        return now()->greaterThan($this->expires_at);
    }

    public function isUsato(): bool
    {
        return $this->status === self::USED;
    }

    /**
     * Valido = mai usato e non ancora scaduto.
     * Se e' scaduto ne approfittiamo per aggiornare lo stato sul database.
     */
    public function isValido(): bool
    {
        if ($this->isUsato()) {
            return false;
        }

        if ($this->isScaduto()) {
            if ($this->status !== self::EXPIRED) {
                $this->update(['status' => self::EXPIRED]);
            }

            return false;
        }

        return true;
    }

    /**
     * Quanti giorni mancano alla scadenza (0 se gia' scaduto).
     */
    public function giorniRimanenti(): int
    {
        return max(0, (int) now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false));
    }

    public function etichettaStato(): string
    {
        return match (true) {
            $this->isUsato()   => 'Già utilizzato',
            $this->isScaduto() => 'Scaduto',
            default            => 'Valido',
        };
    }

    /**
     * Sottotitolo mostrato sul biglietto.
     */
    public function sottotitolo(): string
    {
        return $this->isDiProva()
            ? 'CAMMINATA METABOLICA'
            : mb_strtoupper($this->evento?->titolo ?? 'EVENTO');
    }
}
