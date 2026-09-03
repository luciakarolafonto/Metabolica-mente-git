<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

/**
 * Comando comodo per dare (o togliere) i permessi da staff a un utente.
 *
 *   php artisan asd:staff daniela@email.it
 *   php artisan asd:staff daniela@email.it --togli
 */
class RendiStaff extends Command
{
    protected $signature = 'asd:staff {email : Email dell\'utente} {--togli : Toglie i permessi invece di darli}';

    protected $description = 'Abilita un utente a convalidare i coupon (area staff)';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('Nessun utente registrato con questa email.');

            return self::FAILURE;
        }

        $abilita = ! $this->option('togli');

        $user->update(['is_staff' => $abilita]);

        $this->info($abilita
            ? "{$user->full_name} ora può convalidare i coupon."
            : "{$user->full_name} non fa più parte dello staff.");

        return self::SUCCESS;
    }
}
