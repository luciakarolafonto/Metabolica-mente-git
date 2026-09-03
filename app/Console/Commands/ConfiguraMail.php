<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Configura l'invio delle email con Gmail senza dover aprire il file .env.
 *
 *   php artisan asd:mail
 *
 * Il comando chiede l'indirizzo Gmail e la "password per le app",
 * le scrive nel .env al posto giusto e manda una mail di prova.
 * La password non viene mostrata a schermo mentre la digiti.
 */
class ConfiguraMail extends Command
{
    protected $signature = 'asd:mail {--prova= : Manda solo una mail di prova a questo indirizzo, senza cambiare niente}';

    protected $description = 'Configura Gmail per l\'invio delle email e manda una mail di prova';

    public function handle(): int
    {
        // Modalità "manda solo una prova", utile quando è già tutto configurato.
        if ($indirizzo = $this->option('prova')) {
            return $this->inviaProva($indirizzo);
        }

        $this->intestazione();

        $email = trim((string) $this->ask('1) Qual è l\'indirizzo Gmail dell\'associazione?'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Questo non sembra un indirizzo email valido. Riprova.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('2) Ora incolla la "password per le app" di Google (16 lettere).');
        $this->line('   <fg=gray>Mentre la incolli non si vede: è normale, sta funzionando.</>');
        $this->line('   <fg=gray>Gli spazi li tolgo io, non preoccuparti.</>');

        // secret() nasconde quello che viene digitato.
        $password = str_replace(' ', '', (string) $this->secret('   Password per le app'));

        if (strlen($password) < 12) {
            $this->error('La password sembra troppo corta: quella di Google è di 16 lettere.');
            $this->line('Non è la password normale di Gmail, ma quella creata su myaccount.google.com/apppasswords');

            return self::FAILURE;
        }

        $this->scriviEnv([
            'MAIL_MAILER'       => 'smtp',
            'MAIL_HOST'         => 'smtp.gmail.com',
            'MAIL_PORT'         => '587',
            'MAIL_SCHEME'       => 'smtp',
            'MAIL_USERNAME'     => $email,
            'MAIL_PASSWORD'     => $password,
            'MAIL_FROM_ADDRESS' => '"'.$email.'"',
        ]);

        $this->newLine();
        $this->info('Configurazione salvata nel file .env.');

        // Ricarica la configurazione appena scritta, senza riavviare.
        $this->call('config:clear');

        $this->newLine();
        $destinatario = trim((string) $this->ask(
            '3) A quale indirizzo mando la mail di prova?',
            $email
        ));

        return $this->inviaProva($destinatario);
    }

    private function inviaProva(string $destinatario): int
    {
        $this->newLine();
        $this->line("Invio una mail di prova a {$destinatario}...");

        try {
            Mail::raw(
                "Se stai leggendo questo messaggio, l'invio delle email del sito funziona.\n\n"
                ."Da adesso i clienti che si registrano ricevono davvero la mail di conferma\n"
                ."e il loro coupon di prova gratuita.\n\n"
                .config('asd.name'),
                function ($m) use ($destinatario) {
                    $m->to($destinatario)->subject('Prova invio — '.config('asd.name'));
                }
            );
        } catch (\Throwable $e) {
            $this->newLine();
            $this->error('Invio NON riuscito.');
            $this->line('<fg=gray>'.$e->getMessage().'</>');
            $this->newLine();
            $this->spiegaErrore($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();

        if (config('mail.default') === 'log') {
            $this->warn('Attenzione: le mail stanno ancora andando nel file di log, non su internet.');
            $this->line('Rilancia "php artisan asd:mail" per configurare Gmail.');

            return self::SUCCESS;
        }

        $this->info('Mail inviata! Controlla la posta di '.$destinatario.' (guarda anche nello spam).');
        $this->line('Se è arrivata, il sito è a posto: non devi fare altro.');

        return self::SUCCESS;
    }

    private function spiegaErrore(string $messaggio): void
    {
        $testo = strtolower($messaggio);

        if (str_contains($testo, 'username and password not accepted') || str_contains($testo, '535')) {
            $this->line('Google ha rifiutato le credenziali. Le cause sono sempre queste due:');
            $this->line(' - hai usato la password normale di Gmail invece della "password per le app";');
            $this->line(' - la password per le app è stata copiata male (deve essere di 16 lettere).');
            $this->line('Rilancia "php artisan asd:mail" e riprova.');

            return;
        }

        if (str_contains($testo, 'timed out') || str_contains($testo, 'connection could not be established')) {
            $this->line('Non si riesce a raggiungere il server di Google.');
            $this->line('Controlla la connessione a internet, oppure un antivirus/firewall che blocca la porta 587.');

            return;
        }

        $this->line('Copia il messaggio qui sopra e mandamelo: ti dico cosa significa.');
    }

    /**
     * Sostituisce le righe indicate dentro il file .env, lasciando
     * tutto il resto (commenti compresi) esattamente com'era.
     *
     * @param  array<string,string>  $valori
     */
    private function scriviEnv(array $valori): void
    {
        $percorso = base_path('.env');
        $contenuto = file_get_contents($percorso);

        foreach ($valori as $chiave => $valore) {
            $riga = $chiave.'='.$valore;

            if (preg_match('/^'.preg_quote($chiave, '/').'=.*$/m', $contenuto)) {
                $contenuto = preg_replace('/^'.preg_quote($chiave, '/').'=.*$/m', $riga, $contenuto);
            } else {
                $contenuto .= PHP_EOL.$riga;
            }
        }

        file_put_contents($percorso, $contenuto);
    }

    private function intestazione(): void
    {
        $this->newLine();
        $this->line('<fg=yellow>=====================================================</>');
        $this->line('<fg=yellow> Configurazione invio email — '.config('asd.name').'</>');
        $this->line('<fg=yellow>=====================================================</>');
        $this->newLine();
        $this->line('Prima di continuare ti serve la "password per le app" di Google.');
        $this->line('Se non ce l\'hai: vai su <fg=cyan>myaccount.google.com/apppasswords</>,');
        $this->line('crea una password chiamandola "sito metabolica" e copiala.');
        $this->newLine();
    }
}
