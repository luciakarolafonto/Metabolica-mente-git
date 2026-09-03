@extends('layouts.app')

@section('titolo', 'La camminata metabolica')
@section('descrizione', 'Come funziona una lezione di camminata metabolica: ritmo a 130 BPM in cuffia, fascia elastica F-Band, esercizi di postura e respirazione. Adatta a tutti i livelli.')

@section('contenuto')

    {{-- ============================== INTESTAZIONE ============================== --}}
    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello">{{ config('asd.payoff') }}</span>
            <h1 class="display-4 fw-extrabold text-navy mt-3 mb-3">
                La camminata metabolica,<br>spiegata bene
            </h1>
            <p class="lead text-secondary mm-max-700 mx-auto mb-0">
                Cosa succede davvero in quell'ora, perché funziona e perché puoi farla
                anche se non ti alleni da dieci anni.
            </p>
        </div>
    </section>

    {{-- ============================== I TRE PILASTRI ============================== --}}
    <section class="py-5 bg-white">
        <div class="container py-4">

            <div class="text-center mb-5">
                <span class="mm-occhiello mm-occhiello-oro">Il metodo</span>
                <h2 class="display-5 fw-bold text-navy mt-3">Tre elementi, un solo risultato</h2>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="mm-card h-100">
                        <div class="fs-1 text-navy mb-3"><i class="bi bi-headphones"></i></div>
                        <h4>Il ritmo: 130 BPM</h4>
                        <p class="text-muted small mb-0">
                            130 battiti al minuto è la cadenza in cui il passo diventa automatico
                            e il respiro resta regolare. La musica in cuffia la tiene costante al
                            posto tuo: non devi contare niente, ti lasci trasportare e il corpo
                            lavora da solo.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mm-card h-100">
                        <div class="fs-1 text-gold mb-3"><i class="bi bi-activity"></i></div>
                        <h4>Lo strumento: la F-Band</h4>
                        <p class="text-muted small mb-0">
                            Una fascia elastica che accompagna il movimento delle braccia.
                            Serve ad allungare il tratto cervicale, aprire il torace e tenere
                            le scapole al loro posto: la parte del corpo che la vita da
                            scrivania rovina per prima.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="mm-card h-100">
                        <div class="fs-1 text-success mb-3"><i class="bi bi-tree"></i></div>
                        <h4>Il luogo: l'aperto</h4>
                        <p class="text-muted small mb-0">
                            Si cammina nel verde, non su un tapis roulant davanti a uno specchio.
                            Luce naturale, aria vera e terreno che cambia sotto i piedi: il corpo
                            si adatta di continuo e lavora molto più di quanto sembri.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ============================== LA LEZIONE ============================== --}}
    <section class="py-5" style="background: var(--mm-cream)">
        <div class="container py-4">
            <div class="row g-5 align-items-start">

                <div class="col-lg-6">
                    <span class="mm-occhiello">Un'ora insieme</span>
                    <h2 class="display-6 fw-bold text-navy mt-3 mb-4">Com'è fatta una lezione</h2>

                    @php
                        $fasi = [
                            ['bi-people', 'Accoglienza e cuffie', '10 minuti prima: ci si trova, si prendono cuffie e F-Band, si controlla che l\'audio arrivi bene a tutti.'],
                            ['bi-arrow-repeat', 'Riscaldamento', 'Mobilità di caviglie, anche e spalle. Serve a far capire al corpo che si comincia.'],
                            ['bi-person-walking', 'Camminata attiva', 'Il cuore della lezione: ritmo costante a 130 BPM, con variazioni di andatura e di braccia guidate dalla trainer.'],
                            ['bi-activity', 'Esercizi con la fascia', 'Soste brevi lungo il percorso per lavorare su postura, spalle e tonificazione.'],
                            ['bi-wind', 'Defaticamento e respiro', 'Si rallenta, si allunga, si respira. Si torna a casa stanchi nel modo giusto.'],
                        ];
                    @endphp

                    @foreach ($fasi as $i => $f)
                        <div class="d-flex gap-3 mb-4">
                            <div class="flex-shrink-0 rounded-circle bg-navy text-warning d-flex align-items-center justify-content-center"
                                 style="width:46px;height:46px">
                                <i class="bi {{ $f[0] }}"></i>
                            </div>
                            <div>
                                <h3 class="h5 mb-1 text-navy">{{ $i + 1 }}. {{ $f[1] }}</h3>
                                <p class="text-muted small mb-0">{{ $f[2] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="col-lg-6">
                    <div class="mm-card">
                        <h3 class="h4 mb-3">Cosa serve portare</h3>

                        <p class="mm-aiuto mb-2 text-uppercase fw-bold">Porti tu</p>
                        <ul class="mm-lista-icone">
                            @foreach (config('asd.equipment') as $cosa)
                                <li>
                                    <span class="mm-bollo mm-bollo-oro"><x-icona :nome="$cosa['icona']" :dim="20" /></span>
                                    {{ $cosa['testo'] }}
                                </li>
                            @endforeach
                        </ul>

                        <p class="mm-aiuto mb-2 mt-4 text-uppercase fw-bold">Mettiamo noi</p>
                        <ul class="mm-lista-icone mb-0">
                            @foreach (config('asd.provided') as $cosa)
                                <li>
                                    <span class="mm-bollo mm-bollo-verde"><x-icona :nome="$cosa['icona']" :dim="20" /></span>
                                    {{ $cosa['testo'] }}
                                </li>
                            @endforeach
                        </ul>

                        <div class="mm-nota mt-4">
                            Non serve comprare niente per la prima lezione: cuffie e fascia
                            te le consegna la trainer al ritrovo.
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================== FAQ ============================== --}}
    <section id="faq" class="py-5 bg-white">
        <div class="container py-4">

            <div class="text-center mb-5">
                <span class="mm-occhiello mm-occhiello-oro">Dubbi</span>
                <h2 class="display-5 fw-bold text-navy mt-3">Domande frequenti</h2>
            </div>

            @php
                $faq = [
                    ['Non mi alleno da anni: posso venire?', 'Sì, ed è esattamente il caso per cui il metodo è nato. Si cammina, non si corre: puoi rallentare quando vuoi e la trainer ti tiene d\'occhio.'],
                    ['C\'è un\'età massima o minima?', 'No. Le camminate sono aperte a tutte le età. Per i minorenni serve la presenza o l\'autorizzazione di un genitore.'],
                    ['Devo comprare cuffie o fascia?', 'No. Cuffie sanificate e fascia F-Band sono fornite dall\'associazione a ogni lezione, comprese quelle di prova.'],
                    ['E se piove?', 'Se il tempo non permette di camminare in sicurezza la lezione viene rimandata e lo comunichiamo su Instagram e via email. Il coupon resta valido.'],
                    ['Come funziona il coupon di prova?', 'È personale e nominativo, vale una lezione intera (' . config('asd.coupon.value') . ' euro), dura ' . config('asd.coupon.days') . ' giorni dal ritiro e si usa una volta sola. Lo mostri alla trainer prima di iniziare.'],
                    ['Devo lasciare i dati della carta?', 'No, mai. Per registrarti servono solo nome, cognome, email e una password. Non chiediamo nessun dato di pagamento.'],
                ];
            @endphp

            <div class="accordion mm-max-800 mx-auto" id="faqAccordion">
                @foreach ($faq as $i => $d)
                    <div class="accordion-item rounded-3 mb-2 border">
                        <h3 class="accordion-header">
                            <button class="accordion-button fw-bold text-navy {{ $i === 0 ? '' : 'collapsed' }}"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                                {{ $d[0] }}
                            </button>
                        </h3>
                        <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i === 0 ? 'show' : '' }}"
                             data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-secondary small">{{ $d[1] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- ============================== CHIUSURA ============================== --}}
    <section class="py-5" style="background: linear-gradient(135deg, var(--mm-navy) 0%, var(--mm-navy-light) 100%)">
        <div class="container py-4 text-center text-white">
            <h2 class="display-6 fw-bold text-white mb-3">Provala una volta, poi decidi</h2>
            <p class="text-light mm-max-700 mx-auto mb-4">
                La prima lezione è gratuita e senza impegno. Registrati, ritira il tuo
                coupon personale e presentati al ritrovo.
            </p>
            @auth
                <a href="{{ route('coupon.index') }}" class="btn btn-mm-oro btn-lg">Vai al mio coupon</a>
            @else
                <a href="{{ route('registrazione') }}" class="btn btn-mm-oro btn-lg">Ricevi il coupon gratuito</a>
            @endauth
        </div>
    </section>

@endsection
