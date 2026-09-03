@extends('emails.layout')

@section('oggetto', 'Reimposta la password')

@section('corpo')

    <p style="margin:0 0 16px;">
        Ciao <strong>{{ $user->name }}</strong>,
    </p>

    <p style="margin:0 0 22px;">
        hai chiesto di reimpostare la password del tuo account.
        Clicca sul pulsante qui sotto per sceglierne una nuova.
    </p>

    <p style="margin:0 0 26px;" align="center">
        <a href="{{ $url }}"
           style="display:inline-block;background:#0c2f54;color:#ffffff;text-decoration:none;font-weight:bold;padding:14px 34px;border-radius:999px;font-size:15px;">
            Scegli una nuova password
        </a>
    </p>

    <p style="margin:0 0 16px;color:#3c4a58;">
        Il link resta valido per <strong>{{ $minuti }} minuti</strong>.
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
        Se non hai chiesto tu il cambio password, ignora questo messaggio:
        la tua password attuale resta valida.
    </p>

@endsection
