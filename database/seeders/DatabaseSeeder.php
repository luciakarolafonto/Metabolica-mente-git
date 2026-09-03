<?php

namespace Database\Seeders;

use App\Models\Evento;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Crea l'utente della trainer (quello che gestisce appuntamenti e
     * convalida i coupon) e un appuntamento d'esempio in bozza.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'trainer@metabolicamente.it'],
            [
                'name'              => config('asd.trainer'),
                'surname'           => 'Trainer',
                'password'          => 'CambiaQuesta123',
                'is_staff'          => true,
                'email_verified_at' => now(),
            ]
        );

        // Appuntamento di esempio: resta in BOZZA, quindi non e' visibile
        // sul sito. Serve solo per provare la gestione. Modificalo o
        // eliminalo quando crei quelli veri.
        Evento::updateOrCreate(
            ['slug' => 'camminata-di-esempio'],
            [
                'titolo'      => 'Camminata di esempio',
                'sommario'    => 'Appuntamento finto, serve solo per fare pratica con la gestione.',
                'descrizione' => "Questo appuntamento è in bozza: non lo vede nessuno tranne lo staff.\n\n"
                    ."Modificalo o eliminalo quando inserisci le date vere.",
                'luogo'          => config('asd.location'),
                'ritrovo'        => 'Ritrovo 15 minuti prima all\'ingresso',
                'inizia_il'      => now()->addWeek()->setTime(18, 30),
                'finisce_il'     => now()->addWeek()->setTime(19, 30),
                'posti'          => 20,
                'prezzo'         => 10,
                'stato'          => Evento::BOZZA,
                'coupon_attivo'  => true,
                'coupon_titolo'  => 'Coupon camminata di esempio',
                'coupon_valore'  => 5,
            ]
        );
    }
}
