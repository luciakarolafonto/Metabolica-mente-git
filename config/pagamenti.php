<?php

/*
|--------------------------------------------------------------------------
| Pagamenti
|--------------------------------------------------------------------------
|
| Oggi il denaro NON passa dal sito: la persona dichiara come pagherà e
| paga al ritrovo (o con bonifico). Il sito tiene il registro.
|
| Quando l'associazione avrà un account Stripe basterà mettere le chiavi
| nel .env e portare PAGAMENTI_ONLINE a true: la scelta "carta online"
| comparirà da sola nel modulo di prenotazione, senza toccare le pagine.
|
*/

return [

    /*
     | Interruttore generale del pagamento con carta sul sito.
     | Resta false finché Stripe non è collegato e provato.
     */
    'online_attivo' => (bool) env('PAGAMENTI_ONLINE', false),

    /*
     | I modi di pagare che il sito propone.
     | 'online' => true significa "si paga sul sito", e quella voce compare
     | solo se online_attivo è true.
     */
    'metodi' => [
        'contanti' => [
            'etichetta'   => 'Contanti al ritrovo',
            'descrizione' => 'Porti l\'importo in contanti e paghi alla trainer prima della lezione.',
            'icona'       => 'bi-cash-coin',
            'online'      => false,
        ],
        'carta' => [
            'etichetta'   => 'Carta al ritrovo (POS)',
            'descrizione' => 'Paghi con bancomat o carta direttamente alla trainer, con il POS.',
            'icona'       => 'bi-credit-card',
            'online'      => false,
        ],
        'bonifico' => [
            'etichetta'   => 'Bonifico bancario',
            'descrizione' => 'Fai il bonifico prima della lezione usando i dati che trovi qui sotto.',
            'icona'       => 'bi-bank',
            'online'      => false,
        ],
        'carta_online' => [
            'etichetta'   => 'Carta di credito sul sito',
            'descrizione' => 'Paghi subito online, in modo sicuro. Ricevi la conferma per email.',
            'icona'       => 'bi-credit-card-2-front',
            'online'      => true,
        ],
    ],

    /*
     | Dati per il bonifico. Se l'IBAN è vuoto, la voce "bonifico" non
     | viene proposta: sarebbe inutile senza le coordinate.
     */
    'bonifico' => [
        'intestatario' => env('BONIFICO_INTESTATARIO', ''),
        'iban'         => env('BONIFICO_IBAN', ''),
        'banca'        => env('BONIFICO_BANCA', ''),
    ],

    /*
     | Stripe. Le chiavi si prendono da dashboard.stripe.com e si scrivono
     | nel .env, MAI dentro questo file (che finisce su git).
     */
    'stripe' => [
        'chiave_pubblica' => env('STRIPE_KEY', ''),
        'chiave_segreta'  => env('STRIPE_SECRET', ''),
        'webhook_secret'  => env('STRIPE_WEBHOOK_SECRET', ''),
        'valuta'          => 'eur',
    ],

];
