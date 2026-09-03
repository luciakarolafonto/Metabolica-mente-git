@extends('layouts.app')

@section('titolo', 'Camminata metabolica nei parchi')

@section('contenuto')

    {{-- ============================== HERO ============================== --}}
    <section class="mm-hero text-center text-lg-start">
        @include('partials.prato')

        <div class="container">
            <div class="row align-items-center g-5">

                <div class="col-lg-7">
                    <div class="mm-pillola mb-3">
                        <span class="spinner-grow spinner-grow-sm text-success"></span>
                        A.S.D. Metabolica Mente &#10022; Trainer {{ config('asd.trainer') }}
                    </div>

                    <h1 class="display-4 fw-extrabold text-navy mb-3">
                        Risveglia <span class="text-gold">corpo</span>
                        e <span class="text-verde">mente</span> camminando nel verde.
                    </h1>

                    <p class="lead text-secondary mb-4">
                        L'allenamento all'aperto che unisce <strong>tecnica posturale</strong>,
                        <strong>cuffie wireless a 130 BPM</strong> ed esercizi con la fascia F-Band
                        per ritrovare tono, energia e lucidità.
                    </p>

                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-2 mb-4">
                        <span class="mm-pillola"><i class="bi bi-headphones text-navy"></i> Cuffie wireless incluse</span>
                        <span class="mm-pillola"><x-icona nome="fascia" :dim="17" class="text-gold" /> Fascia posturale F-Band</span>
                        <span class="mm-pillola"><i class="bi bi-tree text-success"></i> 100% parchi e natura</span>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3">
                        @auth
                            <a href="{{ route('coupon.index') }}" class="btn btn-mm-blu py-3 px-4">
                                <i class="bi bi-ticket-perforated me-2"></i> Vai al mio coupon
                            </a>
                        @else
                            <a href="{{ route('registrazione') }}" class="btn btn-mm-blu py-3 px-4">
                                <i class="bi bi-ticket-perforated me-2"></i> Ricevi il coupon di prova gratuito
                            </a>
                        @endauth
                        <a href="{{ route('metodo') }}" class="btn btn-mm-contorno py-3 px-4">
                            Scopri come funziona
                        </a>
                    </div>

                    <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-4 mt-4 text-muted small">
                        <div><i class="bi bi-check-circle-fill text-success me-1"></i> 1&ordf; prova 100% gratuita</div>
                        <div><i class="bi bi-calendar-check text-gold me-1"></i> Valido {{ config('asd.coupon.days') }} giorni</div>
                        <div><i class="bi bi-credit-card-2-front text-cielo me-1"></i> Nessun dato di pagamento</div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="p-4 rounded-4 shadow-lg text-white"
                         style="background: linear-gradient(145deg, var(--mm-navy), var(--mm-navy-light)); border: 2px solid var(--mm-gold);">

                        <div class="d-flex justify-content-between align-items-center border-bottom border-light border-opacity-50 pb-3 mb-3">
                            <h5 class="m-0 fw-bold text-white">Camminata metabolica</h5>
                            <span class="badge bg-gold text-white fw-bold">VALORE {{ config('asd.coupon.value') }}&euro;</span>
                        </div>

                        <p class="small text-light mb-3">
                            Musica a frequenza guidata e voce della trainer in cuffia, per liberare
                            la mente dallo stress e riattivare il metabolismo.
                        </p>

                        <div class="row text-center g-2 my-3">
                            <div class="col-4">
                                <div class="mm-riquadro-scuro">
                                    <span class="mm-numerone text-warning">60'</span><br>
                                    <small>Durata</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mm-riquadro-scuro">
                                    <span class="mm-numerone text-success">130</span><br>
                                    <small>BPM</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="mm-riquadro-scuro">
                                    <span class="mm-numerone text-info">0</span><br>
                                    <small>Traumi</small>
                                </div>
                            </div>
                        </div>

                        @auth
                            <a href="{{ route('coupon.index') }}" class="btn btn-mm-oro w-100 py-2 mt-2">
                                Il mio coupon personale
                            </a>
                        @else
                            <a href="{{ route('registrazione') }}" class="btn btn-mm-oro w-100 py-2 mt-2">
                                Registrati e richiedi il coupon
                            </a>
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================== COS'È ============================== --}}
    <section id="cose" class="py-5 bg-white">
        <div class="container py-4">

            <div class="text-center mm-max-700 mx-auto mb-5 mm-anim">
                <span class="mm-occhiello">Metodo guidato</span>
                <h2 class="display-5 fw-bold text-navy mt-3">
                    Cos'è la <span class="text-gold">camminata metabolica</span>?
                </h2>
                <p class="text-secondary">
                    Non è una passeggiata e non è una corsa faticosa. È un percorso allenante
                    di 60 minuti, pensato per riattivare corpo e mente insieme.
                </p>
            </div>

            <div class="row g-4">
                @php
                    $pilastri = [
                        ['bi-headphones', 'mm-bollo-blu', 'Cuffie wireless immersive',
                         'Ascolti la voce guida e una base musicale a 130 BPM che detta il ritmo del passo, isolandoti dai rumori esterni.'],
                        ['fascia', 'mm-bollo-oro', 'Fascia elastica F-Band',
                         'Uno strumento posturale che apre la gabbia toracica, scarica la cervicale e allena la schiena a stare eretta.'],
                        ['bi-tree', 'mm-bollo-verde', 'Natura e parco',
                         'Allenarsi all\'aria aperta stimola endorfine e serotonina, abbassa il cortisolo e amplifica i benefici dell\'ossigenazione.'],
                    ];
                @endphp

                @foreach ($pilastri as $i => $p)
                    <div class="col-md-4 mm-anim">
                        <div class="mm-card h-100">
                            <div class="mm-icona-cerchio {{ $p[1] }}">
                                <x-icona :nome="$p[0]" :dim="30" />
                            </div>
                            <h4 class="fw-bold text-navy">{{ $i + 1 }}. {{ $p[2] }}</h4>
                            <p class="text-muted small mb-0">{{ $p[3] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-5 mm-anim">
                <a href="{{ route('metodo') }}" class="btn btn-mm-contorno">
                    Come è fatta una lezione <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>

        </div>
    </section>

    {{-- onda di passaggio --}}
    <div class="mm-onda" aria-hidden="true">
        <svg viewBox="0 0 1440 54" preserveAspectRatio="none">
            <path d="M0 30 Q 240 0 480 26 T 960 26 T 1440 8 L1440 54 L0 54 Z" fill="#f2f8ea"></path>
        </svg>
    </div>

    {{-- ============================== BENEFICI ============================== --}}
    <section id="benefici" class="py-5 mm-sezione-verde">
        <div class="container py-4">

            <div class="text-center mb-5 mm-anim">
                <span class="mm-occhiello mm-occhiello-oro">I benefici</span>
                <h2 class="display-5 fw-bold text-navy mt-3">Cosa cambia, davvero</h2>
                <p class="text-secondary mm-max-700 mx-auto">
                    Un allenamento sicuro ed efficace, adatto a ogni età e a chi non si muove da anni.
                    Non promettiamo miracoli in una settimana: promettiamo costanza e compagnia.
                </p>
            </div>

            <div class="row g-4">
                @php
                    $benefici = [
                        ['bi-fire', 'mm-bollo-corallo', 'Riattivazione metabolica',
                         'Il ritmo costante e prolungato è quello che il corpo usa davvero per bruciare, senza affaticare cuore e articolazioni.'],
                        ['bi-shield-check', 'mm-bollo-blu', 'Meno dolori alla schiena',
                         'La F-Band distende la colonna e tonifica la muscolatura posturale profonda, quella che la vita da scrivania spegne.'],
                        ['bi-emoji-smile', 'mm-bollo-oro', 'Meno stress e cortisolo',
                         'Un\'ora all\'aperto, con la musica giusta e senza telefono, è la cosa più vicina a un reset mentale.'],
                        ['bi-moon-stars', 'mm-bollo-cielo', 'Sonno migliore',
                         'Il movimento aerobico serale aiuta a scaricare la tensione accumulata durante la giornata.'],
                        ['bi-heart-pulse', 'mm-bollo-corallo', 'Gambe e glutei tonici',
                         'Cambi di passo ed esercizi mirati fanno il lavoro, senza pesi, senza salti e senza palestra.'],
                        ['bi-people', 'mm-bollo-verde', 'Un gruppo che ti aspetta',
                         'La costanza arriva più facile quando qualcuno si accorge se non ci sei.'],
                    ];
                @endphp

                @foreach ($benefici as $b)
                    <div class="col-md-6 col-lg-4 mm-anim">
                        <div class="mm-card h-100">
                            <div class="mm-icona-cerchio {{ $b[1] }}">
                                <i class="bi {{ $b[0] }}"></i>
                            </div>
                            <h5 class="fw-bold text-navy">{{ $b[2] }}</h5>
                            <p class="text-muted small mb-0">{{ $b[3] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <div class="mm-onda" aria-hidden="true">
        <svg viewBox="0 0 1440 54" preserveAspectRatio="none">
            <path d="M0 24 Q 240 54 480 28 T 960 28 T 1440 46 L1440 0 L0 0 Z" fill="#e9f4dd"></path>
        </svg>
    </div>

    {{-- ============================== COUPON ============================== --}}
    @php
        $haCoupon = auth()->check() && auth()->user()->coupon;
    @endphp

    @unless ($haCoupon)
        <section id="coupon" class="py-5 bg-white">
            <div class="container py-4">

                <div class="text-center mb-5 mm-anim">
                    <span class="mm-occhiello mm-occhiello-oro">1&ordf; lezione omaggio</span>
                    <h2 class="display-5 fw-bold text-navy mt-3">
                        Il tuo coupon personale di <span class="text-gold">prova gratuita</span>
                    </h2>
                    <p class="text-secondary mm-max-700 mx-auto">
                        Nominativo, con un codice univoco, valido {{ config('asd.coupon.days') }} giorni
                        e utilizzabile una sola volta. Te lo mandiamo anche per email, in PDF e in immagine.
                    </p>
                </div>

                <div class="row justify-content-center g-4 align-items-center">

                    <div class="col-lg-7 mm-anim">
                        {{-- Anteprima d'esempio: il biglietto vero, col nome della persona,
                             si vede solo nell'area personale dopo la registrazione. --}}
                        <div class="mm-biglietto">
                            <div class="d-flex justify-content-between align-items-center border-bottom border-light border-opacity-50 pb-3 mb-4 flex-wrap gap-2">
                                <div>
                                    <h4 class="m-0 fw-bold">METABOLICA MENTE A.S.D.</h4>
                                    <small class="text-warning text-uppercase">Camminata metabolica nei parchi</small>
                                </div>
                                <span class="mm-timbro">Prova gratuita</span>
                            </div>

                            <span class="small text-light text-uppercase fw-bold">Intestato a</span>
                            <h3 class="display-6 fw-bold text-white mb-3">
                                {{ auth()->check() ? auth()->user()->full_name : 'Il tuo nome' }}
                            </h3>

                            <div class="mm-riquadro-chiaro border border-warning mb-3">
                                <span class="small text-warning text-uppercase fw-bold d-block">Codice univoco</span>
                                <span class="mm-codice">#XXXXXX</span>
                            </div>

                            <div class="row g-2 small">
                                <div class="col-6">
                                    <div class="mm-riquadro-scuro">
                                        <span class="text-light d-block">Riscattato il</span>
                                        <strong>il giorno del ritiro</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mm-riquadro-scuro">
                                        <span class="text-warning d-block">Scadenza</span>
                                        <strong>{{ config('asd.coupon.days') }} giorni dopo</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 small text-light">
                                <i class="bi bi-info-circle-fill text-warning me-1"></i>
                                Anteprima d'esempio. Il coupon vero, con il tuo nome e il tuo codice,
                                lo ritiri dall'area personale dopo la registrazione.
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5 mm-anim">
                        <div class="mm-card">
                            <h3 class="h4 mb-4">Come si ottiene</h3>

                            @php
                                $passi = [
                                    ['bi-person-plus', 'mm-bollo-blu', 'Registrati', 'Nome, cognome, email e password. Un minuto.'],
                                    ['bi-envelope-check', 'mm-bollo-oro', 'Conferma l\'email', 'Ti arriva un link: un clic e l\'account è attivo.'],
                                    ['bi-download', 'mm-bollo-cielo', 'Ritira il coupon', 'Lo scarichi in PDF o immagine e lo ricevi per email.'],
                                    ['bi-person-walking', 'mm-bollo-verde', 'Vieni a camminare', 'Mostralo alla trainer ' . config('asd.trainer') . ' nel giorno dell\'appuntamento.'],
                                ];
                            @endphp

                            @foreach ($passi as $i => $p)
                                <div class="d-flex gap-3 mb-3 align-items-center">
                                    <span class="mm-bollo {{ $p[1] }}"><i class="bi {{ $p[0] }}"></i></span>
                                    <div>
                                        <strong class="text-navy d-block">{{ $i + 1 }}. {{ $p[2] }}</strong>
                                        <span class="text-muted small">{{ $p[3] }}</span>
                                    </div>
                                </div>
                            @endforeach

                            <div class="d-grid mt-4">
                                @auth
                                    <a href="{{ route('coupon.index') }}" class="btn btn-mm-oro py-2">
                                        Vai alla mia area personale
                                    </a>
                                @else
                                    <a href="{{ route('registrazione') }}" class="btn btn-mm-oro py-2">
                                        Registrati e ritira il coupon
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    @endunless

    {{-- ============================== APPUNTAMENTI ============================== --}}
    @if ($eventi->isNotEmpty() || count(config('asd.appuntamenti')))
        <section id="appuntamenti" class="py-5" style="background: var(--mm-cream)">
            <div class="container py-4">

                <div class="text-center mb-5 mm-anim">
                    <span class="mm-occhiello">Dove e quando</span>
                    <h2 class="display-5 fw-bold text-navy mt-3">I prossimi appuntamenti</h2>
                    <p class="text-secondary">
                        Scegli la data più comoda: puoi prenotare il tuo posto in un minuto.
                    </p>
                </div>

                <div class="row g-4 justify-content-center">

                    {{-- Appuntamenti veri, presi dal database --}}
                    @foreach ($eventi as $evento)
                        <div class="col-md-6 col-lg-4 mm-anim">
                            @include('eventi.scheda', ['evento' => $evento])
                        </div>
                    @endforeach

                    {{-- Se non ce ne sono ancora, si mostrano gli orari fissi di config --}}
                    @if ($eventi->isEmpty())
                        @foreach (config('asd.appuntamenti') as $a)
                            <div class="col-md-6 col-lg-5 mm-anim">
                                <div class="mm-card h-100">
                                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                        <span class="badge bg-navy px-3 py-2 rounded-pill">
                                            <i class="bi bi-calendar-week me-1"></i> {{ $a['giorno'] }}
                                        </span>
                                        <span class="badge bg-gold text-white px-3 py-2 rounded-pill">
                                            <i class="bi bi-clock me-1"></i> {{ $a['orario'] }}
                                        </span>
                                    </div>
                                    <h4 class="fw-bold text-navy">{{ $a['luogo'] }}</h4>
                                    @if (! empty($a['ritrovo']))
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $a['ritrovo'] }}
                                        </p>
                                    @endif
                                    @if (! empty($a['descrizione']))
                                        <p class="text-secondary small mb-0">{{ $a['descrizione'] }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                </div>

                <div class="text-center mt-5 mm-anim">
                    <a href="{{ route('eventi.index') }}" class="btn btn-mm-contorno">
                        Vedi tutti gli appuntamenti <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <p class="text-center text-muted small mt-4 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    Le variazioni e i rinvii per maltempo li pubblichiamo su
                    <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" target="_blank" rel="noopener">Instagram</a>.
                </p>

            </div>
        </section>
    @endif

    {{-- ============================== TRAINER ============================== --}}
    <section id="trainer" class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-center g-5">

                <div class="col-lg-6 mm-anim">
                    <span class="mm-occhiello mm-occhiello-oro">La tua guida</span>
                    <h2 class="display-5 fw-bold text-navy mt-3">{{ config('asd.trainer_full') }}</h2>

                    <p class="lead text-secondary">
                        È lei che ti aspetta al ritrovo, ti sistema la fascia la prima volta,
                        rallenta se hai bisogno di rallentare e ti spinge un po' quando vede che puoi.
                    </p>

                    <ul class="mm-lista-icone mb-4">
                        <li>
                            <span class="mm-bollo mm-bollo-verde"><i class="bi bi-patch-check-fill"></i></span>
                            Guida di persona ogni camminata, dall'inizio alla fine
                        </li>
                        <li>
                            <span class="mm-bollo mm-bollo-corallo"><i class="bi bi-heart-fill"></i></span>
                            Cura della postura e attenzione uno per uno
                        </li>
                        <li>
                            <span class="mm-bollo mm-bollo-blu"><i class="bi bi-people-fill"></i></span>
                            Gruppi piccoli, nessun confronto e nessun giudizio
                        </li>
                    </ul>

                    <div class="d-flex flex-wrap gap-3">
                        <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" target="_blank" rel="noopener"
                           class="btn btn-outline-danger rounded-pill px-4">
                            <i class="bi bi-instagram me-1"></i> Instagram
                        </a>
                        @if (config('asd.whatsapp'))
                            <a href="https://wa.me/{{ config('asd.whatsapp') }}" target="_blank" rel="noopener"
                               class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-whatsapp me-1"></i> WhatsApp
                            </a>
                        @endif
                        <a href="{{ route('contatti') }}" class="btn btn-mm-contorno">Scrivici</a>
                    </div>
                </div>

                <div class="col-lg-6 text-center mm-anim">
                    <div class="p-5 rounded-4 border border-warning shadow-lg text-white"
                         style="background: linear-gradient(145deg, var(--mm-navy), var(--mm-navy-light))">
                        <img src="{{ asset('img/logo.jpg') }}" alt="{{ config('asd.name') }}"
                             width="140" height="140" class="rounded-circle bg-white mb-3"
                             style="border:4px solid var(--mm-gold)">
                        <h3 class="fw-bold text-white">{{ config('asd.payoff') }}</h3>
                        <p class="text-warning mb-0">Ti aspettiamo per la tua prova!</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================== RECENSIONI ============================== --}}
    {{-- Compare solo quando in config/asd.php ci sono recensioni vere. --}}
    @if (count(config('asd.recensioni')))
        <section id="recensioni" class="py-5 mm-sezione-verde">
            <div class="container py-4">

                <div class="text-center mb-5 mm-anim">
                    <span class="mm-occhiello">Dicono di noi</span>
                    <h2 class="display-5 fw-bold text-navy mt-3">Le voci di chi cammina con noi</h2>
                </div>

                <div class="row g-4">
                    @foreach (config('asd.recensioni') as $r)
                        <div class="col-md-6 col-lg-4 mm-anim">
                            <div class="mm-card h-100">
                                <i class="bi bi-quote fs-1 text-gold"></i>
                                <p class="text-secondary">{{ $r['testo'] }}</p>
                                <div class="fw-bold text-navy">{{ $r['nome'] }}</div>
                                @if (! empty($r['da']))
                                    <div class="small text-muted">{{ $r['da'] }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif

    {{-- ============================== CHIUSURA ============================== --}}
    <section class="py-5" style="background: linear-gradient(135deg, var(--mm-navy) 0%, var(--mm-navy-light) 100%)">
        <div class="container py-4 text-center text-white">
            <h2 class="display-6 fw-bold text-white mb-3">Provala una volta, poi decidi</h2>
            <p class="text-light mm-max-700 mx-auto mb-4">
                La prima lezione è offerta dall'associazione e non c'è nessun impegno a continuare.
                Cuffie e fascia le portiamo noi.
            </p>
            @auth
                <a href="{{ route('coupon.index') }}" class="btn btn-mm-oro btn-lg">Vai al mio coupon</a>
            @else
                <a href="{{ route('registrazione') }}" class="btn btn-mm-oro btn-lg">
                    <i class="bi bi-stars me-1"></i> Ricevi il coupon gratuito
                </a>
            @endauth
        </div>
    </section>

@endsection
