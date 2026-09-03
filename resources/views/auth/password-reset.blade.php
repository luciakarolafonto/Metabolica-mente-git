@extends('layouts.app')

@section('titolo', 'Nuova password')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello mm-occhiello-oro">Quasi fatto</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-0">Scegli la nuova password</h1>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-5">

                    <div class="mm-form-box mm-form-box-oro">
                        <form method="POST" action="{{ route('password.update') }}" novalidate>
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $email) }}" required readonly>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Nuova password</label>
                                <input type="password" id="password" name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required autofocus autocomplete="new-password">
                                <div class="mm-aiuto mt-1">Almeno 8 caratteri, con lettere e numeri.</div>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Conferma la nuova password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                       class="form-control" required autocomplete="new-password">
                            </div>

                            <button type="submit" class="btn btn-mm-blu w-100 py-3">
                                <i class="bi bi-shield-lock me-1"></i> Salva la password
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
