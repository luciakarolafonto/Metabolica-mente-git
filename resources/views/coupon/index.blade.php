@extends('layouts.app')

@section('titolo', 'Area personale')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center mm-no-stampa">
        @include('partials.prato')
        <div class="container">
            <span class="mm-occhiello">Area personale</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">Ciao {{ auth()->user()->name }}!</h1>
            <p class="lead text-secondary mb-0">
                Qui trovi i tuoi coupon e le tue prenotazioni.
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    {{-- ================= COUPON DI PROVA ================= --}}
                    @if (! $coupon)

                        <div class="mm-form-box mm-form-box-oro text-center mb-5">
                            <div class="fs-1 text-gold mb-2"><i class="bi bi-ticket-perforated"></i></div>
                            <h2 class="h3 mb-3">Il tuo coupon di prova ti aspetta</h2>

                            <p class="text-secondary mx-auto mb-4 mm-max-700">
                                È personale e nominativo: viene intestato a
                                <strong class="text-navy">{{ auth()->user()->full_name }}</strong>,
                                vale una lezione di prova ({{ config('asd.coupon.value') }}&euro;)
                                e resta valido {{ config('asd.coupon.days') }} giorni da oggi.
                                Si usa una volta sola.
                            </p>

                            <form method="POST" action="{{ route('coupon.genera') }}">
                                @csrf
                                <button type="submit" class="btn btn-mm-oro btn-lg">
                                    <i class="bi bi-stars me-1"></i> Genera il mio coupon
                                </button>
                            </form>

                            <p class="mm-aiuto mt-3 mb-0">
                                Lo riceverai anche via email, con il biglietto allegato in PDF e in immagine.
                            </p>
                        </div>

                    @else

                        <div class="mb-5">
                            @include('coupon.biglietto', ['coupon' => $coupon])
                        </div>

                    @endif

                    {{-- ================= COUPON DEGLI APPUNTAMENTI ================= --}}
                    @if ($couponEventi->isNotEmpty())
                        <div class="mm-no-stampa">
                            <h2 class="h4 mb-4 mt-5 pt-3 border-top">
                                <i class="bi bi-calendar-heart text-gold me-2"></i>
                                Coupon degli appuntamenti
                            </h2>
                        </div>

                        @foreach ($couponEventi as $c)
                            <div class="mb-5">
                                @include('coupon.biglietto', ['coupon' => $c])
                            </div>
                        @endforeach
                    @endif

                    {{-- ================= PRENOTAZIONI ================= --}}
                    <div class="mm-no-stampa mt-5 pt-3 border-top">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                            <h2 class="h4 mb-0">
                                <i class="bi bi-calendar-check text-gold me-2"></i>
                                Le mie prenotazioni
                            </h2>
                            <a href="{{ route('pagamenti.index') }}" class="btn btn-mm-contorno btn-sm">
                                <i class="bi bi-wallet2 me-1"></i> I miei pagamenti
                            </a>
                        </div>

                        @if ($prenotazioni->isEmpty())

                            <div class="mm-card text-center">
                                <p class="text-secondary mb-3">
                                    Non hai ancora prenotato nessun appuntamento.
                                </p>
                                <a href="{{ route('eventi.index') }}" class="btn btn-mm-blu">
                                    Guarda il calendario
                                </a>
                            </div>

                        @else

                            <div class="row g-3">
                                @foreach ($prenotazioni as $p)
                                    <div class="col-md-6">
                                        <div class="mm-card h-100 {{ $p->isAnnullata() ? 'opacity-75' : '' }}">

                                            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                <span class="badge bg-navy rounded-pill px-3 py-2">
                                                    {{ $p->evento->inizia_il->format('d/m/Y') }}
                                                </span>

                                                @if ($p->isAnnullata())
                                                    <span class="mm-stato mm-stato-usato">Annullata</span>
                                                @elseif ($p->pagato || $p->isGratuita())
                                                    <span class="mm-stato mm-stato-valido">{{ $p->etichettaStato() }}</span>
                                                @else
                                                    <span class="mm-stato mm-stato-scaduto">Da pagare</span>
                                                @endif
                                            </div>

                                            <h3 class="h6 fw-bold text-navy mb-1">
                                                <a href="{{ route('eventi.show', $p->evento) }}" class="text-decoration-none">
                                                    {{ $p->evento->titolo }}
                                                </a>
                                            </h3>
                                            <p class="small text-muted mb-2">{{ $p->evento->luogo }}</p>

                                            <dl class="row small mb-0">
                                                <dt class="col-6 text-muted fw-normal">Codice</dt>
                                                <dd class="col-6 text-end font-monospace">{{ $p->codice }}</dd>

                                                <dt class="col-6 text-muted fw-normal">Posti</dt>
                                                <dd class="col-6 text-end">{{ $p->posti }}</dd>

                                                <dt class="col-6 text-muted fw-normal">Importo</dt>
                                                <dd class="col-6 text-end">
                                                    @if ($p->isGratuita())
                                                        <span class="text-success fw-bold">Gratuito</span>
                                                    @else
                                                        {{ number_format((float) $p->importo, 2, ',', '.') }} &euro;
                                                    @endif
                                                </dd>

                                                <dt class="col-6 text-muted fw-normal">Pagamento</dt>
                                                <dd class="col-6 text-end">{{ $p->etichettaMetodo() }}</dd>
                                            </dl>

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    {{-- Coriandoli quando il coupon viene appena generato --}}
    @if (session('successo'))
        <script src="{{ asset('js/confetti.min.js') }}"></script>
        <script>
            window.addEventListener('load', function () {
                if (typeof confetti === 'function') {
                    confetti({ particleCount: 110, spread: 75, origin: { y: 0.6 } });
                }
            });
        </script>
    @endif
@endpush
