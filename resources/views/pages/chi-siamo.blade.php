@extends('layouts.app')

@section('titolo', 'Chi siamo')
@section('descrizione', 'Metabolica Mente A.S.D. è un\'associazione sportiva dilettantistica che organizza camminate metaboliche guidate all\'aperto.')

@section('contenuto')

    {{-- ============================== INTESTAZIONE ============================== --}}
    <section class="mm-hero mm-hero-piccolo">
        @include("partials.prato")
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8 text-center text-lg-start">
                    <span class="mm-occhiello">Associazione Sportiva Dilettantistica</span>
                    <h1 class="display-4 fw-extrabold text-navy mt-3 mb-3">Chi siamo</h1>
                    <p class="lead text-secondary mb-0">
                        Un gruppo di persone che ha smesso di rimandare
                        e ha ricominciato a camminare. Insieme.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <img src="{{ asset('img/logo.jpg') }}" alt="{{ config('asd.name') }}"
                         class="rounded-circle bg-white shadow" width="180" height="180"
                         style="border:5px solid var(--mm-gold)">
                </div>
            </div>
        </div>
    </section>

    {{-- ============================== STORIA ============================== --}}
    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row g-5">

                <div class="col-lg-7">
                    <span class="mm-occhiello mm-occhiello-oro">La nostra storia</span>
                    <h2 class="display-6 fw-bold text-navy mt-3 mb-3">
                        Nata da una camminata, cresciuta con le persone
                    </h2>
                    <p>
                        {{ config('asd.name') }} nasce da un'idea semplice: rendere il movimento
                        una cosa che si aspetta con piacere, non un dovere da spuntare.
                        Abbiamo cominciato in pochi, all'aperto, con qualche paio di cuffie e
                        una fascia elastica. Oggi ci troviamo con costanza, ogni settimana,
                        e il gruppo continua ad allargarsi.
                    </p>
                    <p>
                        Siamo un'associazione sportiva dilettantistica: non un centro fitness,
                        non un abbonamento. Chi partecipa entra in un gruppo, e il gruppo è la
                        ragione per cui la maggior parte delle persone, dopo la prima volta,
                        torna anche la seconda.
                    </p>

                    <h3 class="h4 mt-5 mb-3 text-navy">In cosa crediamo</h3>
                    <ul class="mm-lista-spunta">
                        <li><strong>Il movimento è per tutti.</strong> Nessun livello minimo, nessun confronto, nessun giudizio.</li>
                        <li><strong>La costanza batte l'intensità.</strong> Un'ora a settimana fatta sempre vale più di un mese di allenamenti feroci e poi il nulla.</li>
                        <li><strong>Il corpo e la testa vanno insieme.</strong> Da qui il nostro nome: {{ config('asd.claim') }}</li>
                        <li><strong>All'aperto, sempre che si possa.</strong> Luce, aria e verde fanno parte del metodo.</li>
                    </ul>
                </div>

                <div class="col-lg-5">
                    <div class="mm-card mb-4">
                        <span class="mm-occhiello mm-occhiello-oro">La trainer</span>
                        <h3 class="h4 mt-3">{{ config('asd.trainer_full') }}</h3>
                        <p class="text-muted small">
                            Guida ogni camminata di persona. Sistema le fasce, controlla le
                            andature, corregge le posture e conosce il nome di chi cammina.
                            Se hai un dubbio prima di iniziare, è a lei che devi chiederlo.
                        </p>
                        <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" target="_blank" rel="noopener"
                           class="btn btn-outline-danger rounded-pill btn-sm px-3">
                            <i class="bi bi-instagram me-1"></i> &#64;{{ config('asd.instagram') }}
                        </a>
                    </div>

                    <div class="mm-card">
                        <span class="mm-occhiello">Dove ci troviamo</span>
                        <h3 class="h4 mt-3">{{ config('asd.location') }}</h3>
                        <p class="text-muted small mb-3">
                            Il punto di ritrovo abituale delle nostre camminate.
                            Date e orari aggiornati li pubblichiamo su Instagram
                            e li mandiamo per email agli iscritti.
                        </p>
                        <a href="{{ route('contatti') }}" class="btn btn-mm-oro btn-sm">Scrivici</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ============================== CHIUSURA ============================== --}}
    <section class="py-5" style="background: linear-gradient(135deg, var(--mm-navy) 0%, var(--mm-navy-light) 100%)">
        <div class="container py-4 text-center text-white">
            <h2 class="display-6 fw-bold text-white mb-3">Vieni a vedere com'è</h2>
            <p class="text-light mm-max-700 mx-auto mb-4">
                La prima lezione è offerta dall'associazione. Ti registri, ricevi il coupon
                e ci vediamo al ritrovo.
            </p>
            @auth
                <a href="{{ route('coupon.index') }}" class="btn btn-mm-oro btn-lg">Vai al mio coupon</a>
            @else
                <a href="{{ route('registrazione') }}" class="btn btn-mm-oro btn-lg">Registrati gratis</a>
            @endauth
        </div>
    </section>

@endsection
