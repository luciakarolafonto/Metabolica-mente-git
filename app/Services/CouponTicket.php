<?php

namespace App\Services;

use App\Models\Coupon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

/**
 * Disegna il biglietto-coupon personalizzato.
 *
 * png()  -> immagine PNG (comoda da salvare sul telefono)
 * pdf()  -> PDF con dentro la stessa immagine + le condizioni d'uso
 *
 * L'immagine e' disegnata con la libreria GD di PHP: si parte da una tela
 * vuota e ci si mette sopra rettangoli, cerchi, il logo e il testo.
 */
class CouponTicket
{
    private const W = 1400;
    private const H = 830;

    /** @var \GdImage */
    private $img;

    /** @var array<string,int> */
    private array $c = [];

    /** Testo della pillola in alto a destra, cambia fra prova ed evento. */
    private string $etichetta = 'PROVA GRATUITA';

    /**
     * Il biglietto in PNG. Una volta disegnato viene tenuto da parte in
     * storage/app/coupon/: disegnarlo costa qualche secondo, e il
     * contenuto non cambia mai una volta emesso il coupon.
     */
    public function png(Coupon $coupon): string
    {
        return $this->daCache($coupon, 'png', fn () => $this->disegnaPng($coupon));
    }

    private function disegnaPng(Coupon $coupon): string
    {
        $this->etichetta = $coupon->isDiProva() ? 'PROVA GRATUITA' : 'COUPON EVENTO';

        $this->img = imagecreatetruecolor(self::W, self::H);

        $this->registraColori();
        $this->cornice();
        $this->intestazione();
        $this->corpo($coupon);
        $this->illustrazione();
        $this->piede();

        ob_start();
        imagepng($this->img, null, 6);
        $binario = (string) ob_get_clean();

        imagedestroy($this->img);

        return $binario;
    }

    /**
     * Il biglietto in PDF.
     *
     * Dentro al PDF NON mettiamo il PNG grande: dompdf ci mette quasi un
     * minuto a digerirlo. Con un JPEG rimpicciolito il risultato a video
     * e in stampa e' identico e il tempo scende a pochi secondi.
     */
    public function pdf(Coupon $coupon): string
    {
        return $this->daCache($coupon, 'pdf', function () use ($coupon) {
            $immagine = 'data:image/jpeg;base64,'.base64_encode(
                $this->versioneLeggera($this->png($coupon))
            );

            return Pdf::loadView('pdf.coupon', [
                'coupon'   => $coupon,
                'immagine' => $immagine,
            ])->setPaper('a4', 'portrait')->output();
        });
    }

    /**
     * Rimpicciolisce il biglietto e lo converte in JPEG.
     */
    private function versioneLeggera(string $png, int $larghezza = 1000): string
    {
        $sorgente = imagecreatefromstring($png);

        if (! $sorgente) {
            return $png;
        }

        $altezza = (int) round(imagesy($sorgente) * $larghezza / imagesx($sorgente));

        $piccola = imagecreatetruecolor($larghezza, $altezza);
        imagecopyresampled(
            $piccola, $sorgente,
            0, 0, 0, 0,
            $larghezza, $altezza,
            imagesx($sorgente), imagesy($sorgente)
        );

        ob_start();
        imagejpeg($piccola, null, 82);
        $jpeg = (string) ob_get_clean();

        imagedestroy($sorgente);
        imagedestroy($piccola);

        return $jpeg;
    }

    /**
     * Restituisce il file dalla cartella di appoggio, generandolo solo
     * la prima volta. Il nome contiene il codice del coupon, che e' unico.
     *
     * @param  callable():string  $genera
     */
    private function daCache(Coupon $coupon, string $estensione, callable $genera): string
    {
        $cartella = storage_path('app/coupon');

        if (! is_dir($cartella)) {
            @mkdir($cartella, 0755, true);
        }

        $codice = preg_replace('/[^A-Za-z0-9]/', '', $coupon->code);
        $file = $cartella.DIRECTORY_SEPARATOR."{$coupon->id}-{$codice}.{$estensione}";

        if (is_file($file)) {
            $contenuto = @file_get_contents($file);
            if ($contenuto !== false && $contenuto !== '') {
                return $contenuto;
            }
        }

        $contenuto = $genera();

        @file_put_contents($file, $contenuto);

        return $contenuto;
    }

    /**
     * Nome file suggerito, es. coupon-metabolica-mente-mario-rossi.png
     */
    public function nomeFile(Coupon $coupon, string $estensione): string
    {
        $slug = Str::slug($coupon->user->full_name ?: 'coupon');

        return "coupon-metabolica-mente-{$slug}.{$estensione}";
    }

    // ------------------------------------------------------------------
    //  Disegno
    // ------------------------------------------------------------------

    private function registraColori(): void
    {
        $palette = [
            'navy'        => [12, 47, 84],
            'navyDark'    => [7, 30, 56],
            'blu'         => [29, 90, 158],
            'oro'         => [200, 149, 56],
            'oroChiaro'   => [240, 204, 122],
            'crema'       => [250, 247, 241],
            'bianco'      => [255, 255, 255],
            'inchiostro'  => [18, 32, 46],
            'grigio'      => [110, 122, 134],
            'verde'       => [125, 190, 60],
            'verdeScuro'  => [78, 139, 42],
            'verdeChiaro' => [162, 209, 106],
            'cielo'       => [206, 233, 249],
        ];

        foreach ($palette as $nome => $rgb) {
            $this->c[$nome] = imagecolorallocate($this->img, $rgb[0], $rgb[1], $rgb[2]);
        }
    }

    private function cornice(): void
    {
        // Bordo oro tutto attorno, poi il fondo color crema dentro.
        imagefilledrectangle($this->img, 0, 0, self::W, self::H, $this->c['oro']);
        imagefilledrectangle($this->img, 13, 13, self::W - 14, self::H - 14, $this->c['crema']);

        // Filo sottile blu appena dentro il bordo oro.
        imagesetthickness($this->img, 2);
        imagerectangle($this->img, 22, 22, self::W - 23, self::H - 23, $this->c['navy']);
        imagesetthickness($this->img, 1);
    }

    private function intestazione(): void
    {
        imagefilledrectangle($this->img, 26, 26, self::W - 27, 196, $this->c['navy']);
        imagefilledrectangle($this->img, 26, 190, self::W - 27, 196, $this->c['oro']);

        $logo = public_path('img/logo.jpg');
        if (is_file($logo)) {
            $this->incolla($logo, 52, 44, 136, 136);
            // Il file del logo e' quadrato con lo sfondo bianco: ritagliamo
            // gli angoli ridipingendoli del colore della fascia.
            $this->mascheraCerchio(52, 44, 136, $this->c['navy']);
        }

        $this->testo(config('asd.name'), 214, 108, 32, 'oro', 'bold');
        $this->testo(config('asd.payoff'), 216, 152, 19, 'bianco');

        // Etichetta in alto a destra.
        $etichetta = $this->etichetta;
        $largh = $this->larghezzaTesto($etichetta, 19, 'bold');
        $x2 = self::W - 60;
        $x1 = $x2 - $largh - 56;
        $this->rettangoloArrotondato($x1, 78, $x2, 138, 30, $this->c['oro']);
        $this->testo($etichetta, $x1 + 28, 116, 19, 'navy', 'bold');
    }

    private function corpo(Coupon $coupon): void
    {
        $x = 62;

        // Titolo grande: si rimpicciolisce da solo se il testo e' lungo.
        $this->testoAdattato(mb_strtoupper($coupon->titolo), $x, 278, 44, 26, 700, 'navy');
        $this->testoAdattato($coupon->sottotitolo(), $x, 328, 29, 17, 700, 'oro');

        $valore = (float) $coupon->valore;
        $riga = $coupon->isDiProva()
            ? 'Una lezione di prova gratuita - valore '.rtrim(rtrim(number_format($valore, 2, ',', ''), '0'), ',').' euro'
            : ($valore > 0
                ? 'Vale '.rtrim(rtrim(number_format($valore, 2, ',', ''), '0'), ',').' euro su questo appuntamento'
                : 'Ingresso omaggio per questo appuntamento');

        $this->testo($riga, $x, 370, 19, 'grigio');

        // Intestatario. Il nome si rimpicciolisce da solo se e' molto lungo,
        // cosi' non finisce mai sopra il disegno del prato.
        $this->testo('INTESTATO A', $x, 426, 15, 'oro', 'bold');
        $this->testoAdattato(mb_strtoupper($coupon->user->full_name), $x, 472, 36, 18, 700, 'inchiostro');

        // Riquadro tratteggiato con il codice
        $this->rettangoloTratteggiato($x, 500, $x + 580, 580, $this->c['navy']);
        $this->testo('CODICE PERSONALE', $x + 24, 528, 13, 'grigio', 'bold');
        $this->testo($coupon->code, $x + 24, 566, 27, 'navy', 'mono');

        // Date di validita'
        $this->testo('RISCATTATO IL', $x, 622, 13, 'grigio', 'bold');
        $this->testo($coupon->issued_at->format('d/m/Y'), $x, 654, 23, 'inchiostro', 'bold');

        $this->testo('VALIDO FINO AL', $x + 240, 622, 13, 'grigio', 'bold');
        $this->testo($coupon->expires_at->format('d/m/Y'), $x + 240, 654, 23, 'verdeScuro', 'bold');

        $this->testo('UTILIZZI', $x + 500, 622, 13, 'grigio', 'bold');
        $this->testo('1 - non cedibile', $x + 500, 654, 23, 'inchiostro', 'bold');
    }

    /**
     * Il "prato": cielo, sole, colline, fili d'erba, fiorellini
     * e - se il file c'e' - l'uccellino della pagina Instagram.
     */
    private function illustrazione(): void
    {
        $x1 = 800;
        $y1 = 246;
        $x2 = self::W - 62;
        $y2 = 676;

        $pw = $x2 - $x1;   // larghezza del riquadro
        $ph = $y2 - $y1;   // altezza del riquadro

        // Il disegno viene fatto su una tela a parte, grande quanto il
        // riquadro: cosi' colline e sole non possono sbordare fuori.
        $panel = imagecreatetruecolor($pw, $ph);

        $cielo      = imagecolorallocate($panel, 206, 233, 249);
        $oro        = imagecolorallocate($panel, 200, 149, 56);
        $oroChiaro  = imagecolorallocate($panel, 240, 204, 122);
        $verde      = imagecolorallocate($panel, 125, 190, 60);
        $verdeScuro = imagecolorallocate($panel, 78, 139, 42);
        $verdeChiar = imagecolorallocate($panel, 162, 209, 106);
        $bianco     = imagecolorallocate($panel, 255, 255, 255);

        imagefilledrectangle($panel, 0, 0, $pw, $ph, $cielo);

        // Cielo sfumato: righe sempre piu' chiare andando verso il basso.
        $orizzonte = 230;
        for ($y = 0; $y < $orizzonte; $y++) {
            $t = $y / $orizzonte;
            $col = imagecolorallocate(
                $panel,
                (int) (206 + (238 - 206) * $t),
                (int) (233 + (247 - 233) * $t),
                (int) (249 + (252 - 249) * $t)
            );
            imageline($panel, 0, $y, $pw, $y, $col);
        }

        // Sole con i raggi
        $sx = $pw - 110;
        $sy = 92;
        imagesetthickness($panel, 5);
        for ($a = 0; $a < 360; $a += 30) {
            $r = deg2rad($a);
            imageline(
                $panel,
                (int) ($sx + cos($r) * 48),
                (int) ($sy + sin($r) * 48),
                (int) ($sx + cos($r) * 70),
                (int) ($sy + sin($r) * 70),
                $oroChiaro
            );
        }
        imagesetthickness($panel, 1);
        imagefilledellipse($panel, $sx, $sy, 84, 84, $oroChiaro);
        imagefilledellipse($panel, $sx, $sy, 62, 62, $oro);

        // Colline
        imagefilledellipse($panel, 120, $orizzonte + 40, 460, 250, $verdeChiar);
        imagefilledellipse($panel, $pw - 60, $orizzonte + 60, 420, 230, $verde);

        // Prato e striscia scura in basso
        imagefilledrectangle($panel, 0, $orizzonte + 70, $pw, $ph, $verde);
        imagefilledrectangle($panel, 0, $ph - 62, $pw, $ph, $verdeScuro);

        // Fili d'erba sulla linea di separazione
        imagesetthickness($panel, 3);
        for ($i = 14; $i < $pw - 10; $i += 17) {
            $h = 12 + (($i % 5) * 5);
            imageline($panel, $i, $ph - 62, $i + 5, $ph - 62 - $h, $verdeChiar);
        }
        imagesetthickness($panel, 1);

        // Fiorellini, tutti sopra la striscia scura
        foreach ([[70, 86], [168, 108], [268, 80], [372, 112], [452, 92]] as $f) {
            imagefilledellipse($panel, $f[0], $ph - $f[1], 12, 12, $bianco);
            imagefilledellipse($panel, $f[0], $ph - $f[1], 5, 5, $oro);
        }

        imagecopy($this->img, $panel, $x1, $y1, 0, 0, $pw, $ph);
        imagedestroy($panel);

        // Angoli arrotondati: si ridipinge fuori dal raggio con il colore del foglio.
        $this->arrotondaAngoli($x1, $y1, $x2, $y2, 26, $this->c['crema']);

        // L'uccellino, se e' stato salvato in public/img/uccellino.png
        $uccellino = $this->primoFileEsistente([
            public_path('img/uccellino.png'),
            public_path('img/uccellino.jpg'),
        ]);

        if ($uccellino) {
            $this->incolla($uccellino, $x1 + 34, $y1 + 40, 180, 180);
        }

        // Frase dell'associazione sulla striscia scura
        $this->testo(config('asd.claim'), $x1 + 28, $y2 - 22, 17, 'bianco', 'bold');
    }

    private function piede(): void
    {
        $y1 = 700;
        $y2 = self::H - 27;
        imagefilledrectangle($this->img, 26, $y1, self::W - 27, $y2, $this->c['verdeScuro']);

        $this->testo(
            'Presentalo alla trainer '.config('asd.trainer').' nel giorno dell\'appuntamento, prima della lezione.',
            62,
            $y1 + 38,
            19,
            'bianco',
            'bold'
        );

        $porta = 'Porta con te: '.implode('   -   ', array_column(config('asd.equipment'), 'testo'));
        $this->testoAdattato($porta, 62, $y1 + 72, 16, 11, self::W - 130, 'bianco', 'regular');

        $noi = 'Cuffie wireless e fascia F-Band le forniamo noi.';
        $this->testo($noi, 62, $y1 + 96, 14, 'oroChiaro', 'bold');
    }

    // ------------------------------------------------------------------
    //  Utilita' di disegno
    // ------------------------------------------------------------------

    private function testo(string $testo, int $x, int $y, int $dim, string $colore, string $stile = 'regular'): void
    {
        imagettftext($this->img, $dim, 0, $x, $y, $this->c[$colore], $this->font($stile), $testo);
    }

    /**
     * Scrive il testo rimpicciolendo il carattere finche' non sta
     * dentro la larghezza massima consentita.
     */
    private function testoAdattato(
        string $testo,
        int $x,
        int $y,
        int $dimMax,
        int $dimMin,
        int $larghezzaMax,
        string $colore,
        string $stile = 'bold'
    ): void {
        $dim = $dimMax;

        while ($dim > $dimMin && $this->larghezzaTesto($testo, $dim, $stile) > $larghezzaMax) {
            $dim--;
        }

        $this->testo($testo, $x, $y, $dim, $colore, $stile);
    }

    private function larghezzaTesto(string $testo, int $dim, string $stile = 'regular'): int
    {
        $box = imagettfbbox($dim, 0, $this->font($stile), $testo);

        return (int) abs($box[2] - $box[0]);
    }

    /**
     * Cerca un font vero sul computer: prima quelli di Windows,
     * poi quelli che arrivano insieme a dompdf. Cosi' funziona ovunque.
     */
    private function font(string $stile): string
    {
        $vendor = base_path('vendor/dompdf/dompdf/lib/fonts');

        $candidati = match ($stile) {
            'bold' => [
                'C:/Windows/Fonts/arialbd.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
                $vendor.'/DejaVuSans-Bold.ttf',
            ],
            'mono' => [
                'C:/Windows/Fonts/consolab.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSansMono-Bold.ttf',
                $vendor.'/DejaVuSansMono-Bold.ttf',
            ],
            default => [
                'C:/Windows/Fonts/arial.ttf',
                '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
                $vendor.'/DejaVuSans.ttf',
            ],
        };

        $trovato = $this->primoFileEsistente($candidati);

        if (! $trovato) {
            throw new \RuntimeException(
                'Nessun font TrueType trovato per disegnare il coupon. '.
                'Controlla che esista C:/Windows/Fonts/arial.ttf oppure la cartella vendor/dompdf.'
            );
        }

        return $trovato;
    }

    /**
     * @param  array<int,string>  $percorsi
     */
    private function primoFileEsistente(array $percorsi): ?string
    {
        foreach ($percorsi as $p) {
            if (is_file($p)) {
                return $p;
            }
        }

        return null;
    }

    private function incolla(string $percorso, int $x, int $y, int $largh, int $alt): void
    {
        $info = @getimagesize($percorso);
        if (! $info) {
            return;
        }

        $sorgente = match ($info['mime']) {
            'image/jpeg' => @imagecreatefromjpeg($percorso),
            'image/png'  => @imagecreatefrompng($percorso),
            'image/gif'  => @imagecreatefromgif($percorso),
            'image/webp' => @imagecreatefromwebp($percorso),
            default      => null,
        };

        if (! $sorgente) {
            return;
        }

        imagealphablending($this->img, true);
        imagecopyresampled(
            $this->img,
            $sorgente,
            $x,
            $y,
            0,
            0,
            $largh,
            $alt,
            imagesx($sorgente),
            imagesy($sorgente)
        );
        imagedestroy($sorgente);
    }

    private function rettangoloArrotondato(int $x1, int $y1, int $x2, int $y2, int $r, int $colore): void
    {
        imagefilledrectangle($this->img, $x1 + $r, $y1, $x2 - $r, $y2, $colore);
        imagefilledrectangle($this->img, $x1, $y1 + $r, $x2, $y2 - $r, $colore);

        $d = $r * 2;
        imagefilledellipse($this->img, $x1 + $r, $y1 + $r, $d, $d, $colore);
        imagefilledellipse($this->img, $x2 - $r, $y1 + $r, $d, $d, $colore);
        imagefilledellipse($this->img, $x1 + $r, $y2 - $r, $d, $d, $colore);
        imagefilledellipse($this->img, $x2 - $r, $y2 - $r, $d, $d, $colore);
    }

    /**
     * Ridipinge i quattro angoli di un rettangolo con il colore dello sfondo,
     * in modo che sembri avere gli angoli arrotondati.
     */
    private function arrotondaAngoli(int $x1, int $y1, int $x2, int $y2, int $r, int $sfondo): void
    {
        $angoli = [
            [$x1, $y1, $x1 + $r, $y1 + $r],
            [$x2 - $r, $y1, $x2, $y1 + $r],
            [$x1, $y2 - $r, $x1 + $r, $y2],
            [$x2 - $r, $y2 - $r, $x2, $y2],
        ];

        foreach ($angoli as [$ax1, $ay1, $ax2, $ay2]) {
            // Il centro del cerchio e' l'angolo interno del quadratino.
            $cx = ($ax1 === $x1) ? $ax2 : $ax1;
            $cy = ($ay1 === $y1) ? $ay2 : $ay1;

            for ($x = $ax1; $x <= $ax2; $x++) {
                for ($y = $ay1; $y <= $ay2; $y++) {
                    if (($x - $cx) ** 2 + ($y - $cy) ** 2 > $r ** 2) {
                        imagesetpixel($this->img, $x, $y, $sfondo);
                    }
                }
            }
        }
    }

    /**
     * Ritaglia un'immagine quadrata in tondo, ridipingendo di $sfondo
     * tutto quello che sta fuori dal cerchio inscritto.
     */
    private function mascheraCerchio(int $x, int $y, int $dim, int $sfondo): void
    {
        $r  = $dim / 2;
        $cx = $x + $r;
        $cy = $y + $r;

        for ($i = $x; $i < $x + $dim; $i++) {
            for ($j = $y; $j < $y + $dim; $j++) {
                if (($i - $cx) ** 2 + ($j - $cy) ** 2 > $r ** 2) {
                    imagesetpixel($this->img, $i, $j, $sfondo);
                }
            }
        }
    }

    private function rettangoloTratteggiato(int $x1, int $y1, int $x2, int $y2, int $colore): void
    {
        imagesetthickness($this->img, 3);
        imagesetstyle($this->img, array_merge(
            array_fill(0, 10, $colore),
            array_fill(0, 8, IMG_COLOR_TRANSPARENT)
        ));

        imageline($this->img, $x1, $y1, $x2, $y1, IMG_COLOR_STYLED);
        imageline($this->img, $x1, $y2, $x2, $y2, IMG_COLOR_STYLED);
        imageline($this->img, $x1, $y1, $x1, $y2, IMG_COLOR_STYLED);
        imageline($this->img, $x2, $y1, $x2, $y2, IMG_COLOR_STYLED);

        imagesetthickness($this->img, 1);
    }
}
