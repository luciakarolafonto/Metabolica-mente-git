<?php

namespace App\Console\Commands;

use App\Mail\PromemoriaMail;
use App\Models\Evento;
use App\Models\Prenotazione;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Manda la mail "la camminata è oggi" a chi ha una prenotazione attiva
 * per un appuntamento che si svolge nella giornata indicata.
 *
 *   php artisan asd:promemoria                  -> per oggi
 *   php artisan asd:promemoria --data=2026-09-20
 *   php artisan asd:promemoria --prova          -> non manda niente, mostra solo chi riceverebbe
 *
 * Viene lanciato in automatico ogni mattina (vedi routes/console.php).
 */
class InviaPromemoria extends Command
{
    protected $signature = 'asd:promemoria
                            {--data= : Giorno da controllare (AAAA-MM-GG). Se manca, oggi}
                            {--prova : Mostra solo chi riceverebbe la mail, senza inviarla}';

    protected $description = 'Manda il promemoria "la camminata è oggi" a chi si è prenotato';

    public function handle(): int
    {
        $giorno = $this->option('data')
            ? Carbon::parse($this->option('data'))
            : Carbon::today();

        $soloProva = (bool) $this->option('prova');

        $eventi = Evento::query()
            ->whereIn('stato', [Evento::PUBBLICATO])
            ->whereDate('inizia_il', $giorno->toDateString())
            ->get();

        if ($eventi->isEmpty()) {
            $this->info('Nessun appuntamento in programma per il '.$giorno->format('d/m/Y').'.');

            return self::SUCCESS;
        }

        $inviate = 0;
        $saltate = 0;

        foreach ($eventi as $evento) {
            $this->newLine();
            $this->line("<fg=yellow>{$evento->titolo}</> — {$evento->quando()}");

            $prenotazioni = $evento->prenotazioni()
                ->where('stato', Prenotazione::CONFERMATA)
                ->whereNull('promemoria_inviato_il')
                ->with('user')
                ->get();

            if ($prenotazioni->isEmpty()) {
                $this->line('  <fg=gray>nessuno da avvisare</>');
                continue;
            }

            foreach ($prenotazioni as $prenotazione) {
                $destinatario = $prenotazione->user->email;

                if ($soloProva) {
                    $this->line("  <fg=gray>[prova]</> avviserei {$destinatario}");
                    $saltate++;
                    continue;
                }

                try {
                    Mail::to($destinatario)->send(new PromemoriaMail($prenotazione));

                    $prenotazione->update(['promemoria_inviato_il' => now()]);

                    $this->line("  <fg=green>inviata</> a {$destinatario}");
                    $inviate++;
                } catch (\Throwable $e) {
                    Log::error('Promemoria non inviato a '.$destinatario.': '.$e->getMessage());
                    $this->line("  <fg=red>errore</> con {$destinatario}: ".$e->getMessage());
                    $saltate++;
                }
            }
        }

        $this->newLine();

        if ($soloProva) {
            $this->info("Prova: avrei mandato {$saltate} promemoria. Rilancia senza --prova per inviarli davvero.");
        } else {
            $this->info("Promemoria inviati: {$inviate}".($saltate ? ", non riusciti: {$saltate}" : '.'));
        }

        return self::SUCCESS;
    }
}
