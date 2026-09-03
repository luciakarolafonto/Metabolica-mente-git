@extends('layouts.app')

@section('titolo', 'Contatti')
@section('descrizione', 'Scrivi a Metabolica Mente A.S.D. per informazioni su orari, ritrovo e camminate metaboliche.')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello">Parliamone</span>
            <h1 class="display-4 fw-extrabold text-navy mt-3 mb-3">Contatti</h1>
            <p class="lead text-secondary mm-max-700 mx-auto mb-0">
                Hai una domanda prima di iniziare? Scrivici: risponde direttamente
                {{ config('asd.trainer') }}.
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row g-5">

                <div class="col-lg-7">
                    <div class="mm-form-box mm-form-box-oro">
                        <h2 class="h3 mb-4">Scrivici un messaggio</h2>

                        <form method="POST" action="{{ route('contatti.invia') }}" novalidate>
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nome" class="form-label">Nome e cognome</label>
                                    <input type="text" id="nome" name="nome"
                                           class="form-control @error('nome') is-invalid @enderror"
                                           value="{{ old('nome') }}" required>
                                    @error('nome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">
                                        Telefono <span class="text-muted fw-normal">(facoltativo)</span>
                                    </label>
                                    <input type="text" id="telefono" name="telefono"
                                           class="form-control @error('telefono') is-invalid @enderror"
                                           value="{{ old('telefono') }}">
                                    @error('telefono')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label for="messaggio" class="form-label">Messaggio</label>
                                    <textarea id="messaggio" name="messaggio" rows="6"
                                              class="form-control @error('messaggio') is-invalid @enderror"
                                              required>{{ old('messaggio') }}</textarea>
                                    @error('messaggio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            {{-- Trappola per i robot: gli umani non vedono questo campo --}}
                            <div style="position:absolute;left:-9999px" aria-hidden="true">
                                <label for="website">Non compilare</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>

                            <button type="submit" class="btn btn-mm-blu mt-4">
                                <i class="bi bi-send me-1"></i> Invia il messaggio
                            </button>
                        </form>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="mm-card mb-4">
                        <h3 class="h4"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Dove siamo</h3>
                        <p class="text-muted mb-1"><strong>Ritrovo:</strong> {{ config('asd.location') }}</p>
                        @if (config('asd.city'))
                            <p class="text-muted mb-1"><strong>Zona:</strong> {{ config('asd.city') }}</p>
                        @endif
                        @foreach (config('asd.appuntamenti') as $a)
                            <p class="text-muted mb-0"><strong>{{ $a['giorno'] }}:</strong> {{ $a['orario'] }}</p>
                        @endforeach
                    </div>

                    <div class="mm-card mb-4">
                        <h3 class="h4"><i class="bi bi-chat-dots-fill text-gold me-2"></i>Come raggiungerci</h3>
                        <p class="text-muted mb-2">
                            <i class="bi bi-envelope me-1"></i>
                            <a href="mailto:{{ config('asd.email') }}">{{ config('asd.email') }}</a>
                        </p>
                        @if (config('asd.phone'))
                            <p class="text-muted mb-2"><i class="bi bi-telephone me-1"></i> {{ config('asd.phone') }}</p>
                        @endif
                        @if (config('asd.whatsapp'))
                            <p class="text-muted mb-2">
                                <i class="bi bi-whatsapp me-1"></i>
                                <a href="https://wa.me/{{ config('asd.whatsapp') }}" target="_blank" rel="noopener">Scrivici su WhatsApp</a>
                            </p>
                        @endif
                        <p class="text-muted mb-0">
                            <i class="bi bi-instagram me-1"></i>
                            <a href="https://www.instagram.com/{{ config('asd.instagram') }}/" target="_blank" rel="noopener">
                                &#64;{{ config('asd.instagram') }}
                            </a>
                        </p>
                    </div>

                    <div class="mm-nota">
                        <i class="bi bi-info-circle-fill text-gold me-1"></i>
                        Le date delle camminate e gli eventuali rinvii per maltempo
                        li pubblichiamo sempre prima su Instagram.
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
