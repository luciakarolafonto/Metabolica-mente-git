{{--
    Struttura comune di tutte le email dell'associazione.
    Nelle email si usano tabelle e stili "in linea": e' l'unico modo
    per essere sicuri che Gmail, Outlook e gli altri le mostrino bene.
--}}
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('oggetto', config('asd.name'))</title>
</head>
<body style="margin:0;padding:0;background:#faf7f1;font-family:Arial,Helvetica,sans-serif;color:#12202e;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#faf7f1;padding:28px 12px;">
    <tr>
        <td align="center">

            <table role="presentation" width="600" cellpadding="0" cellspacing="0"
                   style="max-width:600px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e8e2d6;">

                {{-- Intestazione --}}
                <tr>
                    <td style="background:#0c2f54;padding:26px 30px;border-bottom:4px solid #c89538;" align="center">
                        @isset($message)
                            <img src="{{ $message->embed(public_path('img/logo.jpg')) }}"
                                 width="72" height="72" alt="{{ config('asd.name') }}"
                                 style="display:block;border-radius:50%;border:2px solid #c89538;background:#fff;margin:0 auto 12px;">
                        @endisset
                        <div style="color:#f0cc7a;font-size:20px;font-weight:bold;letter-spacing:.5px;">
                            {{ config('asd.name') }}
                        </div>
                        <div style="color:#ffffff;font-size:12px;text-transform:uppercase;letter-spacing:2px;margin-top:4px;">
                            {{ config('asd.payoff') }}
                        </div>
                    </td>
                </tr>

                {{-- Contenuto --}}
                <tr>
                    <td style="padding:30px;font-size:15px;line-height:1.65;color:#12202e;">
                        @yield('corpo')
                    </td>
                </tr>

                {{-- Pie' di pagina --}}
                <tr>
                    <td style="background:#f4f1ea;padding:20px 30px;font-size:12px;color:#6e7a86;line-height:1.6;" align="center">
                        {{ config('asd.name') }} &bull; Trainer {{ config('asd.trainer_full') }}<br>
                        <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" style="color:#1d5a9e;text-decoration:none;">
                            &#64;{{ config('asd.instagram') }}
                        </a>
                        &nbsp;&bull;&nbsp;
                        <a href="mailto:{{ config('asd.email') }}" style="color:#1d5a9e;text-decoration:none;">
                            {{ config('asd.email') }}
                        </a>
                    </td>
                </tr>

            </table>

            <div style="font-size:11px;color:#9aa4ad;margin-top:14px;">
                Ricevi questo messaggio perche' ti sei registrato sul sito di {{ config('asd.name') }}.
            </div>

        </td>
    </tr>
</table>

</body>
</html>
