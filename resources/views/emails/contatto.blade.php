@extends('emails.layout')

@section('oggetto', 'Nuovo messaggio dal sito')

@section('corpo')

    <p style="margin:0 0 18px;font-weight:bold;">Nuovo messaggio dal form contatti del sito.</p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 18px;">
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #eee;width:120px;color:#6e7a86;font-size:13px;">Nome</td>
            <td style="padding:8px 0;border-bottom:1px solid #eee;"><strong>{{ $dati['nome'] }}</strong></td>
        </tr>
        <tr>
            <td style="padding:8px 0;border-bottom:1px solid #eee;color:#6e7a86;font-size:13px;">Email</td>
            <td style="padding:8px 0;border-bottom:1px solid #eee;">
                <a href="mailto:{{ $dati['email'] }}" style="color:#1d5a9e;">{{ $dati['email'] }}</a>
            </td>
        </tr>
        @if (! empty($dati['telefono']))
            <tr>
                <td style="padding:8px 0;border-bottom:1px solid #eee;color:#6e7a86;font-size:13px;">Telefono</td>
                <td style="padding:8px 0;border-bottom:1px solid #eee;">{{ $dati['telefono'] }}</td>
            </tr>
        @endif
    </table>

    <p style="margin:0 0 8px;color:#6e7a86;font-size:13px;">Messaggio</p>
    <div style="background:#f4f1ea;border-radius:12px;padding:16px;white-space:pre-line;">{{ $dati['messaggio'] }}</div>

@endsection
