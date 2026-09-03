@extends('layouts.app')

@section('titolo', 'Accedi')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello">Area personale</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">Accedi</h1>
            <p class="lead text-secondary mb-0">Entra per vedere e scaricare il tuo coupon.</p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-5">

                    <div class="mm-form-box mm-form-box-oro">
                        <form method="POST" action="{{ route('accesso.entra') }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required autofocus autocomplete="email">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required autocomplete="current-password">
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                                    <label class="form-check-label small" for="remember">Ricordami</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="small">Password dimenticata?</a>
                            </div>

                            <button type="submit" class="btn btn-mm-blu w-100 py-3">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Entra
                            </button>
                        </form>

                        <p class="text-center small text-muted mt-4 mb-0">
                            Non hai ancora un account? <a href="{{ route('registrazione') }}">Registrati gratis</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
