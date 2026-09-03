<?php

namespace App\Models;

use App\Notifications\VerificaEmail;
use App\Notifications\ReimpostaPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'surname',
        'email',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_staff'          => 'boolean',
        ];
    }

    /**
     * Il coupon gratuito di benvenuto: uno solo per persona.
     * (I coupon legati agli eventi stanno invece in coupons().)
     */
    public function coupon(): HasOne
    {
        return $this->hasOne(Coupon::class)->where('tipo', Coupon::TIPO_PROVA);
    }

    /**
     * Tutti i coupon della persona: quello di prova e quelli degli eventi.
     */
    public function coupons(): HasMany
    {
        return $this->hasMany(Coupon::class)->latest();
    }

    public function prenotazioni(): HasMany
    {
        return $this->hasMany(Prenotazione::class)->latest();
    }

    public function pagamenti(): HasMany
    {
        return $this->hasMany(Pagamento::class)->latest();
    }

    /**
     * Nome e cognome insieme, comodo per le viste e per il biglietto.
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->name.' '.$this->surname);
    }

    /**
     * Sovrascriviamo le due notifiche di Laravel per mandare
     * le nostre mail in italiano e con la grafica dell'associazione.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerificaEmail());
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ReimpostaPassword($token));
    }
}
