<footer class="mm-footer mm-no-stampa">
    <div class="container">

        <div class="row g-4 pb-4 border-bottom border-light border-opacity-25">

            <div class="col-lg-5">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img src="{{ asset('img/logo.jpg') }}" alt="Logo {{ config('asd.name') }}"
                         width="58" height="58" class="rounded-circle border border-2 border-warning bg-white">
                    <h4 class="m-0">METABOLICA MENTE A.S.D.</h4>
                </div>
                <p class="small mb-3">
                    Associazione Sportiva Dilettantistica per il benessere, la postura e la
                    camminata metabolica nei parchi. {{ config('asd.claim') }}
                </p>
                <div class="d-flex gap-2">
                    <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" target="_blank" rel="noopener"
                       class="btn btn-outline-light btn-sm rounded-circle" title="Instagram" aria-label="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="mailto:{{ config('asd.email') }}"
                       class="btn btn-outline-light btn-sm rounded-circle" title="Email" aria-label="Email">
                        <i class="bi bi-envelope"></i>
                    </a>
                    @if (config('asd.whatsapp'))
                        <a href="https://wa.me/{{ config('asd.whatsapp') }}" target="_blank" rel="noopener"
                           class="btn btn-outline-light btn-sm rounded-circle" title="WhatsApp" aria-label="WhatsApp">
                            <i class="bi bi-whatsapp"></i>
                        </a>
                    @endif
                </div>
            </div>

            <div class="col-6 col-lg-3">
                <h5>Il sito</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('home') }}">Home</a></li>
                    <li class="mb-2"><a href="{{ route('metodo') }}">La camminata</a></li>
                    <li class="mb-2"><a href="{{ route('eventi.index') }}">Appuntamenti</a></li>
                    <li class="mb-2"><a href="{{ route('chi-siamo') }}">Chi siamo</a></li>
                    <li class="mb-2"><a href="{{ route('contatti') }}">Contatti</a></li>
                    @guest
                        <li class="mb-2"><a href="{{ route('registrazione') }}">Registrati</a></li>
                        <li class="mb-2"><a href="{{ route('accesso') }}">Accedi</a></li>
                    @else
                        <li class="mb-2"><a href="{{ route('coupon.index') }}">Il mio coupon</a></li>
                    @endguest
                </ul>
            </div>

            <div class="col-6 col-lg-4">
                <h5>Contatti</h5>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2">
                        <i class="bi bi-person-badge me-1 text-warning"></i>
                        Trainer {{ config('asd.trainer_full') }}
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope me-1 text-warning"></i>
                        <a href="mailto:{{ config('asd.email') }}">{{ config('asd.email') }}</a>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-instagram me-1 text-warning"></i>
                        <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" target="_blank" rel="noopener">
                            &#64;{{ config('asd.instagram') }}
                        </a>
                    </li>
                    @if (config('asd.location'))
                        <li class="mb-2">
                            <i class="bi bi-geo-alt me-1 text-warning"></i>
                            Ritrovo: {{ config('asd.location') }}
                        </li>
                    @endif
                </ul>
            </div>

        </div>

        <div class="mm-footer-basso text-center">
            &copy; {{ date('Y') }} {{ config('asd.name') }} &bull; Tutti i diritti riservati.
        </div>

    </div>
</footer>
