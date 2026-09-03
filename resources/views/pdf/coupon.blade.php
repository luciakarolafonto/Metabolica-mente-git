{{--
    Il PDF del coupon.
    Dentro c'e' la stessa identica immagine del biglietto che viene
    allegata alla mail, piu' le istruzioni e le condizioni d'uso.
    dompdf capisce solo HTML/CSS semplice: niente flexbox, niente grid.
--}}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Coupon di prova gratuita — {{ $coupon->user->full_name }}</title>
    <style>
        @page { margin: 26px 30px; }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #12202e;
            font-size: 11px;
            line-height: 1.55;
        }

        .biglietto { width: 100%; }

        h1 {
            font-size: 15px;
            color: #0c2f54;
            margin: 22px 0 6px;
        }

        .riquadro {
            border: 1px solid #e0d9ca;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 12px;
        }

        .verde {
            background: #eef7e6;
            border-color: #cbe6b3;
        }

        .etichetta {
            color: #6e7a86;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        ul { margin: 4px 0 0 16px; padding: 0; }
        li { margin-bottom: 3px; }

        .piede {
            margin-top: 16px;
            border-top: 1px solid #e0d9ca;
            padding-top: 10px;
            color: #6e7a86;
            font-size: 9px;
            text-align: center;
        }
    </style>
</head>
<body>

    <img src="{{ $immagine }}" class="biglietto" alt="Coupon di prova gratuita">

    <h1>Come si usa</h1>

    <div class="riquadro verde">
        Presenta questo coupon alla trainer <strong>{{ config('asd.trainer') }}</strong>
        <strong>nel giorno dell'appuntamento</strong>, prima dell'inizio della lezione,
        stampato oppure direttamente dallo schermo del telefono.
        È intestato a <strong>{{ $coupon->user->full_name }}</strong>, riporta il codice
        <strong>{{ $coupon->code }}</strong> e non è cedibile ad altre persone.
    </div>

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td width="49%" valign="top">
                <div class="riquadro">
                    <div class="etichetta">Cosa portare con te</div>
                    <ul>
                        @foreach (config('asd.equipment') as $cosa)
                            <li>{{ $cosa['testo'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </td>
            <td width="2%"></td>
            <td width="49%" valign="top">
                <div class="riquadro">
                    <div class="etichetta">Cosa forniamo noi</div>
                    <ul>
                        @foreach (config('asd.provided') as $cosa)
                            <li>{{ $cosa['testo'] }}</li>
                        @endforeach
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <div class="riquadro">
        <div class="etichetta">Condizioni</div>
        <ul>
            @if ($coupon->isDiProva())
                <li>Vale per <strong>una sola lezione di prova</strong>, del valore di {{ config('asd.coupon.value') }} euro.</li>
            @elseif ($coupon->evento)
                <li>
                    Vale per l'appuntamento <strong>{{ $coupon->evento->titolo }}</strong>
                    del {{ $coupon->evento->inizia_il->format('d/m/Y') }}
                    presso {{ $coupon->evento->luogo }}.
                </li>
            @endif
            <li>Intestato a <strong>{{ $coupon->user->full_name }}</strong>, non cedibile ad altre persone.</li>
            <li>Utilizzabile <strong>una volta sola</strong>, entro il <strong>{{ $coupon->expires_at->format('d/m/Y') }}</strong>.</li>
            <li>Non è convertibile in denaro e non è cumulabile con altre promozioni.</li>
            <li>In caso di maltempo la lezione viene rimandata e il coupon resta valido.</li>
        </ul>
    </div>

    <div class="piede">
        {{ config('asd.name') }} — {{ config('asd.trainer_full') }}
        &nbsp;•&nbsp; &#64;{{ config('asd.instagram') }}
        &nbsp;•&nbsp; {{ config('asd.email') }}
        @if (config('asd.location'))
            &nbsp;•&nbsp; Ritrovo: {{ config('asd.location') }}
        @endif
    </div>

</body>
</html>
