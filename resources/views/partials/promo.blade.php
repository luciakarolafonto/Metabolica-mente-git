{{-- Striscia in alto: sparisce per chi ha gia' ritirato il coupon --}}

@if (! auth()->check() || ! auth()->user()->coupon)
    <div class="mm-promo text-center mm-no-stampa">
        <div class="container d-flex justify-content-center align-items-center flex-wrap gap-2">
            <span class="badge bg-gold text-white fw-bold px-2 py-1 rounded-pill text-uppercase" style="letter-spacing:1px">
                Offerta benvenuto
            </span>
            <span>
                <i class="bi bi-gift-fill text-warning"></i>
                <strong>1&ordf; lezione di prova gratuita</strong>, cuffie wireless incluse &mdash;
                coupon valido {{ config('asd.coupon.days') }} giorni.
            </span>
            <a href="{{ auth()->check() ? route('coupon.index') : route('registrazione') }}" class="text-decoration-underline">
                Richiedilo ora &rarr;
            </a>
        </div>
    </div>
@endif
