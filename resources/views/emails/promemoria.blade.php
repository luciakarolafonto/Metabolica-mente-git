@extends('emails.layout')

@section('oggetto', 'La camminata è oggi')

@section('corpo')

    <p style="margin:0 0 16px;">
        Ciao <strong>{{ $user->name }}</strong>,
    </p>

    <p style="margin:0 0 22px;font-size:17px;">
        <strong>è oggi!</strong> Ti aspettiamo per
        <strong>{{ $evento->titolo }}</strong>.
    </p>

    {{-- Riquadro con ora e luogo --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#0c2f54;border-radius:14px;border:2px solid #c89538;margin:0 0 22px;">
        <tr>
            <td align="center" style="padding:24px 18px;">
                <div style="color:#cbd5e1;font-size:11px;text-transform:uppercase;letter-spacing:2px;">
                    Oggi alle
                </div>
                <div style="color:#f0cc7a;font-size:38px;font-weight:bold;line-height:1.1;margin:6px 0;">
                    {{ $evento->inizia_il->format('H:i') }}
                </div>
                <div style="color:#ffffff;font-size:15px;">
                    {{ $evento->luogo }}
                </div>
                @if ($evento->ritrovo)
                    <div style="color:#cbd5e1;font-size:13px;margin-top:4px;">
                        {{ $evento->ritrovo }}
                    </div>
                @endif
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#f4f1ea;border-radius:12px;margin:0 0 22px;">
        <tr>
            <td style="padding:16px;font-size:14px;">
                <strong>Il tuo codice:</strong>
                <span style="font-family:'Courier New',monospace;font-size:18px;font-weight:bold;letter-spacing:2px;">
                    {{ $prenotazione->codice }}
                </span><br>
                <span style="color:#6e7a86;font-size:13px;">
                    Mostralo alla trainer {{ config('asd.trainer') }} prima di iniziare.
                </span>
            </td>
        </tr>
    </table>

    @unless ($prenotazione->isGratuita() || $prenotazione->pagato)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="background:#fdf6e6;border-left:4px solid #c89538;border-radius:10px;margin:0 0 22px;">
            <tr>
                <td style="padding:14px 16px;font-size:13px;color:#5c4a1f;">
                    Ricordati il pagamento: <strong>{{ number_format((float) $prenotazione->importo, 2, ',', '.') }} &euro;</strong>,
                    hai scelto <strong>{{ $prenotazione->etichettaMetodo() }}</strong>.
                </td>
            </tr>
        </table>
    @endunless

    <p style="margin:0 0 10px;font-weight:bold;">Un ultimo controllo prima di uscire</p>
    <ul style="margin:0 0 20px;padding-left:20px;color:#3c4a58;">
        @foreach (config('asd.equipment') as $cosa)
            <li>{{ $cosa['testo'] }}</li>
        @endforeach
    </ul>

    <p style="margin:0 0 22px;color:#3c4a58;">
        A cuffie e fascia F-Band pensiamo noi. Arriva una decina di minuti prima,
        così facciamo con calma.
    </p>

    <p style="margin:0;color:#3c4a58;">
        A tra poco!<br>
        <strong>{{ config('asd.trainer_full') }}</strong>
    </p>

@endsection
