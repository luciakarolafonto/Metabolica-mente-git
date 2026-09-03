@extends('emails.layout')

@section('oggetto', 'Il tuo coupon di prova gratuita')

@section('corpo')

    <p style="margin:0 0 16px;">
        Caro/a <strong>{{ $user->full_name }}</strong>,
    </p>

    @if ($coupon->isDiProva())
        <p style="margin:0 0 16px;">
            questo è il tuo <strong>coupon gratuito di prova</strong> per una lezione di
            Camminata Metabolica con la trainer {{ config('asd.trainer') }}.
            Lo trovi allegato a questa email in due formati: un <strong>PDF</strong> da stampare
            e un'<strong>immagine</strong> da tenere sul telefono.
        </p>
    @else
        <p style="margin:0 0 16px;">
            questo è il tuo coupon per <strong>{{ $coupon->evento?->titolo }}</strong>
            del {{ $coupon->evento?->inizia_il->format('d/m/Y') }}
            presso {{ $coupon->evento?->luogo }}:
            <strong>{{ $coupon->descrizioneVantaggio() }}</strong>.
            Lo trovi allegato in <strong>PDF</strong> da stampare e in
            <strong>immagine</strong> da tenere sul telefono.
        </p>
    @endif

    <p style="margin:0 0 22px;">
        Non dimenticare di portarlo con te e di <strong>presentarlo alla trainer
        {{ config('asd.trainer') }} nel giorno dell'appuntamento</strong>,
        prima che la lezione cominci.
    </p>

    {{-- Il biglietto vero e proprio, mostrato dentro il messaggio.
         embedData lo allega e lo visualizza allo stesso tempo, cosi'
         si vede anche in Gmail senza scaricare niente. --}}
    @isset($message)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 22px;">
            <tr>
                <td align="center">
                    <img src="{{ $message->embedData($immagine, 'coupon.png', 'image/png') }}"
                         alt="Coupon intestato a {{ $user->full_name }}, codice {{ $coupon->code }}"
                         width="560"
                         style="display:block;width:100%;max-width:560px;height:auto;border-radius:12px;border:1px solid #e8e2d6;">
                </td>
            </tr>
        </table>
    @endisset

    {{-- Riquadro con il codice, ben leggibile anche se le immagini sono bloccate --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#0c2f54;border-radius:14px;border:2px solid #c89538;margin:0 0 22px;">
        <tr>
            <td align="center" style="padding:22px 18px;">
                <div style="color:#cbd5e1;font-size:11px;text-transform:uppercase;letter-spacing:2px;">
                    Codice personale
                </div>
                <div style="color:#f0cc7a;font-family:'Courier New',monospace;font-size:23px;font-weight:bold;letter-spacing:2px;margin:8px 0;">
                    {{ $coupon->code }}
                </div>
                <div style="color:#ffffff;font-size:13px;">
                    Intestato a <strong>{{ $user->full_name }}</strong>
                </div>
            </td>
        </tr>
    </table>

    {{-- Validità --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 22px;">
        <tr>
            <td width="50%" style="padding:12px;background:#f4f1ea;border-radius:12px;" valign="top">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:#6e7a86;">Riscattato il</div>
                <div style="font-size:17px;font-weight:bold;color:#12202e;">{{ $coupon->issued_at->format('d/m/Y') }}</div>
            </td>
            <td width="12"></td>
            <td width="50%" style="padding:12px;background:#eef7e6;border-radius:12px;" valign="top">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:1.5px;color:#6e7a86;">Valido fino al</div>
                <div style="font-size:17px;font-weight:bold;color:#4e8b2a;">{{ $coupon->expires_at->format('d/m/Y') }}</div>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 10px;font-weight:bold;">Cosa portare con te</p>
    <ul style="margin:0 0 20px;padding-left:20px;color:#3c4a58;">
        @foreach (config('asd.equipment') as $cosa)
            <li>{{ $cosa['testo'] }}</li>
        @endforeach
    </ul>

    <p style="margin:0 0 22px;color:#3c4a58;">
        Cuffie wireless sanificate e fascia elastica F-Band le mettiamo noi:
        tu pensa solo a presentarti.
    </p>

    {{-- Nota importante --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
           style="background:#eef7e6;border-left:4px solid #7dbe3c;border-radius:10px;margin:0 0 22px;">
        <tr>
            <td style="padding:14px 16px;font-size:13px;color:#33502a;">
                Il coupon è <strong>nominativo</strong>, vale
                <strong>{{ config('asd.coupon.value') }} &euro;</strong> e si può usare
                <strong>una sola volta</strong> entro il {{ $coupon->expires_at->format('d/m/Y') }}.
                Non è cedibile ad altre persone.
            </td>
        </tr>
    </table>

    <p style="margin:0 0 8px;" align="center">
        <a href="{{ route('coupon.index') }}"
           style="display:inline-block;background:#c89538;color:#ffffff;text-decoration:none;font-weight:bold;padding:13px 30px;border-radius:999px;">
            Vedi il coupon sul sito
        </a>
    </p>

    <p style="margin:22px 0 0;color:#3c4a58;">
        Ci vediamo al ritrovo!<br>
        <strong>{{ config('asd.trainer_full') }}</strong>
    </p>

@endsection
