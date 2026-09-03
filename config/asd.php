<?php

/*
|--------------------------------------------------------------------------
| Dati dell'associazione
|--------------------------------------------------------------------------
|
| Tutti i dati "di contenuto" stanno qui e arrivano dal file .env.
| Cosi' per cambiare il nome della trainer, il telefono o la durata del
| coupon non serve toccare le pagine: si modifica il .env e basta.
|
*/

return [

    'name'          => env('APP_NAME', 'Metabolica Mente A.S.D.'),
    'payoff'        => 'Muoviti. Respira. Rigenerati.',
    'claim'         => 'Connetti il corpo alla tua mente!',

    'trainer'       => env('ASD_TRAINER', 'Daniela'),
    'trainer_full'  => env('ASD_TRAINER_FULL', 'Daniela — la Dani'),

    'instagram'     => env('ASD_INSTAGRAM', 'ladani_di_camminata_metabolica'),
    'email'         => env('MAIL_FROM_ADDRESS', 'info@metabolicamente.it'),
    'phone'         => env('ASD_PHONE', ''),

    // Numero WhatsApp in formato internazionale SENZA + e senza spazi,
    // es. 393401234567. Se lo lasci vuoto, il pulsante WhatsApp sparisce.
    'whatsapp'      => env('ASD_WHATSAPP', ''),

    'location'      => env('ASD_LOCATION', 'Boa Gialla Agribeach'),
    'city'          => env('ASD_CITY', ''),

    'coupon' => [
        'value' => (int) env('ASD_COUPON_VALUE', 15),
        'days'  => (int) env('ASD_COUPON_DAYS', 30),
    ],

    /*
    | Cosa deve portare la persona, e cosa mette l'associazione.
    | 'icona' accetta due cose:
    |   - un nome di icona Bootstrap, es. 'bi-headphones'
    |   - uno dei disegni fatti in casa: scarpa, maglietta, borraccia,
    |     asciugamano, fascia  (vedi resources/views/components/icona.blade.php)
    */
    'equipment' => [
        ['icona' => 'scarpa',      'testo' => 'Scarpe da ginnastica chiuse'],
        ['icona' => 'maglietta',   'testo' => 'Abbigliamento comodo e traspirante'],
        ['icona' => 'borraccia',   'testo' => 'Bottiglietta d\'acqua'],
        ['icona' => 'asciugamano', 'testo' => 'Un asciugamano piccolo'],
    ],

    'provided' => [
        ['icona' => 'bi-headphones', 'testo' => 'Cuffie wireless sanificate'],
        ['icona' => 'fascia',        'testo' => 'Fascia elastica F-Band'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dove e quando si cammina
    |--------------------------------------------------------------------------
    | Aggiungi, togli o modifica gli appuntamenti: la sezione "Parchi e orari"
    | del sito si aggiorna da sola. Se lasci l'elenco vuoto, sparisce.
    */
    'appuntamenti' => [
        [
            'giorno'      => 'Ogni domenica',
            'orario'      => '18:30 - 19:30',
            'luogo'       => 'Boa Gialla Agribeach',
            'ritrovo'     => 'Ritrovo 15 minuti prima all\'ingresso',
            'descrizione' => 'La camminata del tramonto, all\'aperto e sul verde.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Recensioni
    |--------------------------------------------------------------------------
    | ATTENZIONE: qui vanno SOLO recensioni vere, scritte da persone vere.
    | L'elenco parte vuoto di proposito e finche' resta vuoto la sezione
    | "Dicono di noi" non compare sul sito.
    |
    | Per aggiungerne una, copia questo schema:
    |   ['nome' => 'Maria C.', 'testo' => 'La sua frase...', 'da' => 'Instagram'],
    */
    'recensioni' => [
        //
    ],
];
