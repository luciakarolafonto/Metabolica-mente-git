@extends('emails.layout')

@section('oggetto', 'Prenotazione confermata')

@section('corpo')

    <p style="margin:0 0 16px;">
        Ciao <strong>{{ $user->name }}</strong>,
    </p>

    <p style="margin:0 0 22px;">
        la tua prenotazione è confermata. Ecco il riepilogo:
        <strong>presentalo alla trainer {{ config('asd.trainer') }}
        nel giorno dell'appuntamento</strong>.
    </p>

    {{-- Riquadro con il codice --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#0c2f54;border-radius:14px;border:2px solid #c89538;margin:0 0 22px;">
        <tr>
            <td align="center" style="padding:22px 18px;">
                <div style="color:#cbd5e1;font-size:11px;text-transform:uppercase;letter-spacing:2px;">
                    Codice prenotazione
                </div>
                <div style="color:#f0cc7a;font-family:'Courier New',monospace;font-size:26px;font-weight:bold;letter-spacing:3px;margin:8px 0;">
                    {{ $prenotazione->codice }}
                </div>
                <div style="color:#ffffff;font-size:13px;">
                    Intestata a <strong>{{ $user->full_name }}</strong>
                </div>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 22px;">
        <tr>
            <td style="padding:9px 0;border-bottom:1px solid #eee;width:150px;color:#6e7a86;font-size:13px;">Appuntamento</td>
            <td style="padding:9px 0;border-bottom:1px solid #eee;"><strong>{{ $evento->titolo }}</strong></td>
        </tr>
        <tr>
            <td style="padding:9px 0;border-bottom:1px solid #eee;color:#6e7a86;font-size:13px;">Quando</td>
            <td style="padding:9px 0;border-bottom:1px solid #eee;">{{ $evento->quando() }}</td>
        </tr>
        <tr>
            <td style="padding:9px 0;border-bottom:1px solid #eee;color:#6e7a86;font-size:13px;">Dove</td>
            <td style="padding:9px 0;border-bottom:1px solid #eee;">
                {{ $evento->luogo }}
                @if ($evento->ritrovo)
                    <br><span style="color:#6e7a86;font-size:13px;">{{ $evento->ritrovo }}</span>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:9px 0;border-bottom:1px solid #eee;color:#6e7a86;font-size:13px;">Posti</td>
            <td style="padding:9px 0;border-bottom:1px solid #eee;">{{ $prenotazione->posti }}</td>
        </tr>
        <tr>
            <td style="padding:9px 0;border-bottom:1px solid #eee;color:#6e7a86;font-size:13px;">Importo</td>
            <td style="padding:9px 0;border-bottom:1px solid #eee;">
                @if ($prenotazione->isGratuita())
                    <strong style="color:#15803d;">Gratuito</strong>
                    @if ($prenotazione->coupon)
                        <span style="color:#6e7a86;font-size:13px;">(coupon {{ $prenotazione->coupon->code }})</span>
                    @endif
                @else
                    <strong>{{ number_format((float) $prenotazione->importo, 2, ',', '.') }} &euro;</strong>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:9px 0;color:#6e7a86;font-size:13px;">Pagamento</td>
            <td style="padding:9px 0;"><strong>{{ $prenotazione->etichettaMetodo() }}</strong></td>
        </tr>
    </table>

    @unless ($prenotazione->isGratuita())
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
               style="background:#fdf6e6;border-left:4px solid #c89538;border-radius:10px;margin:0 0 22px;">
            <tr>
                <td style="padding:14px 16px;font-size:13px;color:#5c4a1f;">
                    Il pagamento <strong>non è ancora stato effettuato</strong>: hai scelto
                    <strong>{{ $prenotazione->etichettaMetodo() }}</strong>.
                    La trainer lo sa già e ti aspetta.
                </td>
            </tr>
        </table>
    @endunless

    <p style="margin:0 0 10px;font-weight:bold;">Cosa portare con te</p>
    <ul style="margin:0 0 20px;padding-left:20px;color:#3c4a58;">
        @foreach (config('asd.equipment') as $cosa)
            <li>{{ $cosa['testo'] }}</li>
        @endforeach
    </ul>

    <p style="margin:0 0 8px;" align="center">
        <a href="{{ route('eventi.show', $evento) }}"
           style="display:inline-block;background:#c89538;color:#ffffff;text-decoration:none;font-weight:bold;padding:13px 30px;border-radius:999px;">
            Vedi l'appuntamento sul sito
        </a>
    </p>

    <p style="margin:22px 0 0;color:#3c4a58;">
        Ci vediamo al ritrovo!<br>
        <strong>{{ config('asd.trainer_full') }}</strong>
    </p>

@endsection
