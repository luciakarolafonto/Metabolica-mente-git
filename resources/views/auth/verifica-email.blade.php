@extends('layouts.app')

@section('titolo', 'Conferma la tua email')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello mm-occhiello-oro">Ultimo passaggio</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">Controlla la tua posta</h1>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-6">

                    <div class="mm-form-box mm-form-box-oro text-center">
                        <div class="fs-1 text-gold mb-2"><i class="bi bi-envelope-check"></i></div>

                        <p class="text-secondary">
                            Abbiamo inviato un messaggio a
                            <strong class="text-navy">{{ auth()->user()->email }}</strong>.
                            Dentro c'è un link: cliccalo per confermare l'indirizzo e sbloccare
                            il tuo coupon di prova gratuita.
                        </p>

                        <div class="mm-nota text-start my-4">
                            <i class="bi bi-search me-1 text-gold"></i>
                            Non trovi il messaggio? Guarda nella cartella <strong>spam</strong> o
                            <strong>promozioni</strong>: capita spesso alla prima email.
                        </div>

                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-mm-oro">
                                <i class="bi bi-arrow-repeat me-1"></i> Rimandami la mail di conferma
                            </button>
                        </form>

                        {{-- Scorciatoia visibile solo mentre si sviluppa, quando le mail
                             non partono ancora davvero. Sparisce da sola con Gmail attivo. --}}
                        @if (! empty($linkSviluppo))
                            <div class="alert alert-warning border-0 rounded-4 text-start mt-4 mb-0">
                                <h2 class="h6 fw-bold text-dark">
                                    <i class="bi bi-tools me-1"></i> Modalità sviluppo
                                </h2>
                                <p class="small mb-2">
                                    Le email non vengono ancora spedite davvero: finiscono nel file
                                    <code>storage/logs/laravel.log</code>. Per non restare bloccato,
                                    puoi confermare il tuo indirizzo da qui.
                                </p>
                                <a href="{{ $linkSviluppo }}" class="btn btn-sm btn-dark rounded-pill">
                                    Conferma adesso senza email
                                </a>
                                <p class="small text-muted mt-2 mb-0">
                                    Questo riquadro sparisce da solo appena configuri Gmail
                                    con <code>php artisan asd:mail</code>.
                                </p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('esci') }}" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-link btn-sm text-muted text-decoration-none">
                                Esci dall'account
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
