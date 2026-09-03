<?php

namespace App\Models\Concerns;

/**
 * Genera i codici brevi da mostrare alla trainer, tipo #2CFR78.
 * Lo usano sia i coupon sia le prenotazioni.
 */
trait GeneraCodice
{
    /**
     * Alfabeto senza i caratteri che si confondono leggendoli ad alta voce
     * o scrivendoli a mano: niente 0/O e niente 1/I/L.
     */
    private const ALFABETO = '23456789ABCDEFGHJKMNPQRSTUVWXYZ';

    /**
     * @param  string  $colonna  nome della colonna che contiene il codice
     */
    public static function generaCodice(string $colonna = 'code'): string
    {
        do {
            $codice = '#';
            for ($i = 0; $i < 6; $i++) {
                $codice .= self::ALFABETO[random_int(0, strlen(self::ALFABETO) - 1)];
            }
        } while (static::where($colonna, $codice)->exists());

        return $codice;
    }

    /**
     * Rende confrontabile quello che scrive la trainer: toglie spazi e
     * trattini, mette in maiuscolo e rimette il cancelletto davanti.
     * Cosi' "2cfr78", "#2CFR78" e "2 cfr 78" trovano la stessa cosa.
     */
    public static function normalizzaCodice(string $codice): string
    {
        $pulito = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $codice));

        return $pulito === '' ? '' : '#'.$pulito;
    }
}
