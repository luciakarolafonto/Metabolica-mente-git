@extends('emails.layout')

@section('oggetto', 'Conferma la tua email')

@section('corpo')

    <p style="margin:0 0 16px;">
        Ciao <strong>{{ $user->name }}</strong>,
    </p>

    <p style="margin:0 0 16px;">
        benvenuto in {{ config('asd.name') }}! Manca solo un passaggio:
        conferma che questo indirizzo email è davvero tuo.
    </p>

    <p style="margin:0 0 26px;" align="center">
        <a href="{{ $url }}"
           style="display:inline-block;background:#c89538;color:#ffffff;text-decoration:none;font-weight:bold;padding:14px 34px;border-radius:999px;font-size:15px;">
            Conferma il mio indirizzo
        </a>
    </p>

    <p style="margin:0 0 16px;color:#3c4a58;">
        Appena confermi, dalla tua area personale potrai ritirare il
        <strong>coupon per la lezione di prova gratuita</strong>
        (valore {{ config('asd.coupon.value') }} &euro;, valido {{ config('asd.coupon.days') }} giorni).
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#f4f1ea;border-radius:10px;margin:0 0 18px;">
        <tr>
            <td style="padding:14px 16px;font-size:12px;color:#6e7a86;word-break:break-all;">
                Se il pulsante non funziona, copia e incolla questo indirizzo nel browser:<br>
                <span style="color:#1d5a9e;">{{ $url }}</span>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-size:13px;color:#6e7a86;">
        Se non ti sei registrato tu, puoi ignorare questo messaggio: non verrà creato nulla.
    </p>

@endsection
