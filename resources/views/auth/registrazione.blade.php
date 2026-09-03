@extends('layouts.app')

@section('titolo', 'Registrati')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello mm-occhiello-oro">Prova gratuita</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">Crea il tuo account</h1>
            <p class="lead text-secondary mm-max-700 mx-auto mb-0">
                Ti serve per ricevere il coupon della lezione di prova.
                Ci vuole un minuto e non chiediamo nessun dato di pagamento.
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-7 col-xl-6">

                    <div class="mm-form-box mm-form-box-oro">
                        <form method="POST" action="{{ route('registrazione.salva') }}" novalidate>
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nome</label>
                                    <input type="text" id="name" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required autofocus autocomplete="given-name">
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="surname" class="form-label">Cognome</label>
                                    <input type="text" id="surname" name="surname"
                                           class="form-control @error('surname') is-invalid @enderror"
                                           value="{{ old('surname') }}" required autocomplete="family-name">
                                    @error('surname')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" id="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required autocomplete="email">
                                    <div class="mm-aiuto mt-1">Ci mandiamo il coupon: controlla che sia scritta bene.</div>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-12">
                                    <label for="phone" class="form-label">
                                        Telefono <span class="text-muted fw-normal">(facoltativo)</span>
                                    </label>
                                    <input type="text" id="phone" name="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           value="{{ old('phone') }}" autocomplete="tel">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" id="password" name="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required autocomplete="new-password">
                                    <div class="mm-aiuto mt-1">Almeno 8 caratteri, con lettere e numeri.</div>
                                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label">Conferma password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                           class="form-control" required autocomplete="new-password">
                                    <div class="mm-aiuto mt-1">Riscrivi la stessa password.</div>
                                </div>

                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input @error('privacy') is-invalid @enderror"
                                               type="checkbox" id="privacy" name="privacy" value="1"
                                               {{ old('privacy') ? 'checked' : '' }} required>
                                        <label class="form-check-label small" for="privacy">
                                            Acconsento al trattamento dei miei dati per la gestione
                                            dell'iscrizione e l'invio del coupon di prova.
                                        </label>
                                        @error('privacy')
                                            <div class="invalid-feedback d-block">Devi accettare per continuare.</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-mm-blu w-100 mt-4 py-3">
                                <i class="bi bi-stars text-warning me-1"></i> Registrati e ricevi il coupon
                            </button>
                        </form>

                        <p class="text-center small text-muted mt-4 mb-0">
                            Hai già un account? <a href="{{ route('accesso') }}">Accedi</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
