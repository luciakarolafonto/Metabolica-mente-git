<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Evento extends Model
{
    use HasFactory;

    protected $table = 'eventi';

    public const BOZZA      = 'bozza';
    public const PUBBLICATO = 'pubblicato';
    public const ANNULLATO  = 'annullato';

    protected $fillable = [
        'titolo', 'slug', 'sommario', 'descrizione',
        'luogo', 'ritrovo',
        'inizia_il', 'finisce_il',
        'posti', 'prezzo', 'stato',
        'coupon_attivo', 'coupon_titolo', 'coupon_valore', 'coupon_scadenza',
    ];

    protected function casts(): array
    {
        return [
            'inizia_il'       => 'datetime',
            'finisce_il'      => 'datetime',
            'coupon_scadenza' => 'date',
            'coupon_attivo'   => 'boolean',
            'prezzo'          => 'decimal:2',
            'coupon_valore'   => 'decimal:2',
            'posti'           => 'integer',
        ];
    }

    // ------------------------------------------------------------------
    //  Legami con le altre tabelle
    // ------------------------------------------------------------------

    public function prenotazioni(): HasMany
    {
        return $this->hasMany(Prenotazione::class);
    }

    /**
     * Solo le prenotazioni ancora valide (non annullate).
     */
    public function prenotazioniAttive(): HasMany
    {
        return $this->prenotazioni()->where('stato', Prenotazione::CONFERMATA);
    }

    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }

    // ------------------------------------------------------------------
    //  Filtri comodi
    // ------------------------------------------------------------------

    public function scopePubblicati(Builder $q): Builder
    {
        return $q->where('stato', self::PUBBLICATO);
    }

    public function scopeInProgramma(Builder $q): Builder
    {
        return $q->where('inizia_il', '>=', now()->startOfDay())->orderBy('inizia_il');
    }

    public function scopePassati(Builder $q): Builder
    {
        return $q->where('inizia_il', '<', now()->startOfDay())->orderByDesc('inizia_il');
    }

    // ------------------------------------------------------------------
    //  Domande che ci facciamo spesso
    // ------------------------------------------------------------------

    public function isGratuito(): bool
    {
        return (float) $this->prezzo <= 0;
    }

    public function isAnnullato(): bool
    {
        return $this->stato === self::ANNULLATO;
    }

    public function isPassato(): bool
    {
        return $this->inizia_il->isPast();
    }

    public function postiOccupati(): int
    {
        return (int) $this->prenotazioniAttive()->sum('posti');
    }

    /**
     * null = posti illimitati.
     */
    public function postiRimasti(): ?int
    {
        if ($this->posti === null) {
            return null;
        }

        return max(0, $this->posti - $this->postiOccupati());
    }

    public function isEsaurito(): bool
    {
        $rimasti = $this->postiRimasti();

        return $rimasti !== null && $rimasti <= 0;
    }

    /**
     * Si puo' ancora prenotare? Serve che sia pubblicato, non annullato,
     * non passato e con posti liberi.
     */
    public function isPrenotabile(): bool
    {
        return $this->stato === self::PUBBLICATO
            && ! $this->isAnnullato()
            && ! $this->isPassato()
            && ! $this->isEsaurito();
    }

    public function motivoNonPrenotabile(): ?string
    {
        return match (true) {
            $this->isAnnullato()               => 'Questo appuntamento è stato annullato.',
            $this->isPassato()                 => 'Questo appuntamento è già passato.',
            $this->isEsaurito()                => 'I posti per questo appuntamento sono esauriti.',
            $this->stato !== self::PUBBLICATO  => 'Questo appuntamento non è ancora aperto alle prenotazioni.',
            default                            => null,
        };
    }

    /**
     * Il coupon dell'evento si puo' ritirare solo se attivo,
     * l'evento non e' annullato e non e' ancora passato.
     */
    public function couponRitirabile(): bool
    {
        return $this->coupon_attivo && ! $this->isAnnullato() && ! $this->isPassato();
    }

    public function scadenzaCoupon(): \Illuminate\Support\Carbon
    {
        return $this->coupon_scadenza
            ? $this->coupon_scadenza->copy()->endOfDay()
            : $this->inizia_il->copy()->endOfDay();
    }

    public function prenotazioneDi(?User $user): ?Prenotazione
    {
        if (! $user) {
            return null;
        }

        return $this->prenotazioni()->where('user_id', $user->id)->first();
    }

    public function couponDi(?User $user): ?Coupon
    {
        if (! $user) {
            return null;
        }

        return $this->coupons()->where('user_id', $user->id)->first();
    }

    // ------------------------------------------------------------------
    //  Varie
    // ------------------------------------------------------------------

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Crea uno slug leggibile e unico a partire dal titolo,
     * es. "Camminata al tramonto" -> camminata-al-tramonto-2
     */
    public static function slugDa(string $titolo, ?int $ignoraId = null): string
    {
        $base = Str::slug($titolo) ?: 'evento';
        $slug = $base;
        $n = 2;

        while (static::where('slug', $slug)->when($ignoraId, fn ($q) => $q->where('id', '!=', $ignoraId))->exists()) {
            $slug = $base.'-'.$n++;
        }

        return $slug;
    }

    public function quando(): string
    {
        $giorni = ['domenica', 'lunedì', 'martedì', 'mercoledì', 'giovedì', 'venerdì', 'sabato'];
        $mesi = ['', 'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno',
                 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre'];

        $d = $this->inizia_il;

        return sprintf(
            '%s %d %s, ore %s',
            $giorni[(int) $d->format('w')],
            (int) $d->format('j'),
            $mesi[(int) $d->format('n')],
            $d->format('H:i')
        );
    }
}
