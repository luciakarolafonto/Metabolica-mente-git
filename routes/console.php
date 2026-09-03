<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Cose che il sito fa da solo
|--------------------------------------------------------------------------
|
| Ogni mattina alle 8:00 parte il promemoria "la camminata è oggi" a chi
| ha una prenotazione attiva per un appuntamento di oggi.
|
| ATTENZIONE: perché funzioni davvero deve girare lo scheduler di Laravel.
|   - Mentre sviluppi:  php artisan schedule:work   (lascialo aperto)
|   - Su un server:     una riga di cron ogni minuto che lancia
|                       php /percorso/artisan schedule:run
|   - Su Windows:       una attività in Utilità di pianificazione che lancia
|                       php artisan schedule:run ogni minuto
|
| Per provarlo subito, senza aspettare:
|   php artisan asd:promemoria --prova
|
*/

Schedule::command('asd:promemoria')
    ->dailyAt('08:00')
    ->timezone('Europe/Rome')
    ->description('Promemoria "la camminata è oggi"');
