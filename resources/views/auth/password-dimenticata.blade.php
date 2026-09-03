@extends('layouts.app')

@section('titolo', 'Password dimenticata')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello">Recupero accesso</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">Password dimenticata</h1>
            <p class="lead text-secondary mm-max-700 mx-auto mb-0">
                Scrivi l'email con cui ti sei registrato: ti mandiamo un link
                per sceglierne una nuova.
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-5">

                    <div class="mm-form-box mm-form-box-oro">
                        <form method="POST" action="{{ route('password.email') }}" novalidate>
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" required autofocus>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <button type="submit" class="btn btn-mm-blu w-100 py-3">
                                <i class="bi bi-send me-1"></i> Inviami il link
                            </button>
                        </form>

                        <p class="text-center small text-muted mt-4 mb-0">
                            Te la sei ricordata? <a href="{{ route('accesso') }}">Torna all'accesso</a>
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
