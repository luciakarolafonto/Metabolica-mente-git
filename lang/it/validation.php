<?php

/*
|--------------------------------------------------------------------------
| Messaggi di errore dei form, in italiano
|--------------------------------------------------------------------------
|
| Laravel scrive i messaggi in inglese: qui li traduciamo.
| Sotto, in 'attributes', diamo anche un nome leggibile ai campi
| (cosi' l'utente legge "Il campo cognome e' obbligatorio", non "surname").
|
*/

return [

    'accepted'    => 'Devi accettare :attribute per continuare.',
    'after'       => 'Il campo :attribute deve contenere una data successiva al :date.',
    'alpha'       => 'Il campo :attribute può contenere solo lettere.',
    'alpha_num'   => 'Il campo :attribute può contenere solo lettere e numeri.',
    'before'      => 'Il campo :attribute deve contenere una data precedente al :date.',
    'confirmed'   => 'Le due :attribute non coincidono.',
    'date'        => 'Il campo :attribute non contiene una data valida.',
    'different'   => 'I campi :attribute e :other devono essere diversi.',
    'email'       => 'Il campo :attribute deve contenere un indirizzo email valido.',
    'exists'      => 'Il valore inserito in :attribute non esiste.',
    'filled'      => 'Il campo :attribute non può essere vuoto.',
    'image'       => 'Il campo :attribute deve contenere un\'immagine.',
    'in'          => 'Il valore selezionato in :attribute non è valido.',
    'integer'     => 'Il campo :attribute deve contenere un numero intero.',
    'max'         => [
        'array'   => 'Il campo :attribute non può contenere più di :max elementi.',
        'file'    => 'Il campo :attribute non può superare i :max kilobyte.',
        'numeric' => 'Il campo :attribute non può essere maggiore di :max.',
        'string'  => 'Il campo :attribute non può superare i :max caratteri.',
    ],
    'min'         => [
        'array'   => 'Il campo :attribute deve contenere almeno :min elementi.',
        'file'    => 'Il campo :attribute deve essere di almeno :min kilobyte.',
        'numeric' => 'Il campo :attribute deve essere almeno :min.',
        'string'  => 'Il campo :attribute deve contenere almeno :min caratteri.',
    ],
    'numeric'     => 'Il campo :attribute deve contenere un numero.',
    'present'     => 'Il campo :attribute deve essere presente.',
    'regex'       => 'Il formato del campo :attribute non è valido.',
    'required'    => 'Il campo :attribute è obbligatorio.',
    'same'        => 'I campi :attribute e :other devono coincidere.',
    'size'        => [
        'array'   => 'Il campo :attribute deve contenere :size elementi.',
        'file'    => 'Il campo :attribute deve essere di :size kilobyte.',
        'numeric' => 'Il campo :attribute deve essere :size.',
        'string'  => 'Il campo :attribute deve contenere :size caratteri.',
    ],
    'string'      => 'Il campo :attribute deve contenere del testo.',
    'unique'      => 'Questo/a :attribute è già stato/a registrato/a.',
    'uploaded'    => 'Il caricamento di :attribute non è riuscito.',
    'url'         => 'Il formato del campo :attribute non è valido.',

    // Regole della classe Password::min()
    'password'    => [
        'letters'       => 'La :attribute deve contenere almeno una lettera.',
        'mixed'         => 'La :attribute deve contenere almeno una maiuscola e una minuscola.',
        'numbers'       => 'La :attribute deve contenere almeno un numero.',
        'symbols'       => 'La :attribute deve contenere almeno un simbolo.',
        'uncompromised' => 'Questa :attribute è comparsa in una fuga di dati: scegline un\'altra.',
    ],

    'custom' => [
        'privacy' => [
            'accepted' => 'Devi accettare il trattamento dei dati per registrarti.',
        ],
        'website' => [
            'size' => 'Controllo antispam non superato. Riprova.',
        ],
    ],

    'attributes' => [
        'name'                  => 'nome',
        'surname'               => 'cognome',
        'email'                 => 'email',
        'phone'                 => 'telefono',
        'password'              => 'password',
        'password_confirmation' => 'conferma password',
        'privacy'               => 'il trattamento dei dati',
        'nome'                  => 'nome',
        'telefono'              => 'telefono',
        'messaggio'             => 'messaggio',
        'code'                  => 'codice',
    ],

];
