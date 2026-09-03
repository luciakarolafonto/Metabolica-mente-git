@extends('layouts.app')

@section('titolo', 'Appuntamenti')
@section('descrizione', 'Tutti gli appuntamenti di camminata metabolica di Metabolica Mente A.S.D.: date, luoghi, posti disponibili e prenotazione online.')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include('partials.prato')
        <div class="container">
            <span class="mm-occhiello">Calendario</span>
            <h1 class="display-4 fw-extrabold text-navy mt-3 mb-3">Gli appuntamenti</h1>
            <p class="lead text-secondary mm-max-700 mx-auto mb-0">
                Scegli la camminata più comoda, prenota il posto e dicci come preferisci pagare.
                Alla trainer arriva tutto in anticipo.
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">

            @if ($inProgramma->isEmpty())

                <div class="mm-card text-center mm-max-700 mx-auto">
                    <div class="fs-1 text-gold mb-2"><i class="bi bi-calendar-x"></i></div>
                    <h2 class="h4">Nessun appuntamento in programma</h2>
                    <p class="text-secondary mb-4">
                        Al momento non ci sono date aperte alle prenotazioni. Le pubblichiamo
                        qui e su Instagram appena sono pronte.
                    </p>
                    <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" target="_blank" rel="noopener"
                       class="btn btn-outline-danger rounded-pill px-4">
                        <i class="bi bi-instagram me-1"></i> Seguici su Instagram
                    </a>
                </div>

            @else

                <div class="text-center mb-5">
                    <span class="mm-occhiello mm-occhiello-oro">In programma</span>
                    <h2 class="display-6 fw-bold text-navy mt-3">
                        {{ $inProgramma->count() }}
                        {{ $inProgramma->count() === 1 ? 'appuntamento aperto' : 'appuntamenti aperti' }}
                    </h2>
                </div>

                <div class="row g-4">
                    @foreach ($inProgramma as $evento)
                        <div class="col-md-6 col-lg-4 mm-anim">
                            @include('eventi.scheda', ['evento' => $evento])
                        </div>
                    @endforeach
                </div>

            @endif

        </div>
    </section>

    @if ($passati->isNotEmpty())
        <section class="py-5 mm-sezione-verde">
            <div class="container py-4">

                <div class="text-center mb-5">
                    <span class="mm-occhiello">Archivio</span>
                    <h2 class="display-6 fw-bold text-navy mt-3">Le camminate già fatte</h2>
                </div>

                <div class="row g-3">
                    @foreach ($passati as $evento)
                        <div class="col-md-6 col-lg-4">
                            <div class="mm-card h-100 opacity-75">
                                <div class="small text-muted mb-1">
                                    <i class="bi bi-calendar-check me-1"></i> {{ $evento->inizia_il->format('d/m/Y') }}
                                </div>
                                <h3 class="h6 fw-bold text-navy mb-1">{{ $evento->titolo }}</h3>
                                <div class="small text-muted">{{ $evento->luogo }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif

@endsection
