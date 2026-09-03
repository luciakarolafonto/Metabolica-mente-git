@extends('layouts.app')

@section('titolo', 'Convalida coupon')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include("partials.prato")
        <div class="container">
            <span class="mm-occhiello">Area staff</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">Convalida coupon</h1>
            <p class="lead text-secondary mm-max-700 mx-auto mb-0">
                Cerca il codice che ti mostra la persona e timbralo.
                Dopo la convalida il coupon non è più riutilizzabile.
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="mm-form-box mm-form-box-oro mb-4">
                        <form method="GET" action="{{ route('admin.convalida') }}" class="row g-2">
                            <div class="col-sm-9">
                                <label for="codice" class="form-label">Codice del coupon</label>
                                <input type="text" id="codice" name="codice" class="form-control font-monospace text-uppercase"
                                       value="{{ $codice }}" placeholder="#2CFR78" autofocus>
                                <div class="mm-aiuto mt-1">
                                    Puoi scriverlo anche senza cancelletto e in minuscolo.
                                </div>
                            </div>
                            <div class="col-sm-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-mm-blu w-100">
                                    <i class="bi bi-search me-1"></i> Cerca
                                </button>
                            </div>
                        </form>
                    </div>

                    @if ($cercato && ! $coupon)
                        <div class="alert alert-danger border-0 rounded-4">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            Nessun coupon con il codice <strong>{{ $codice }}</strong>.
                            Controlla che sia scritto esattamente come sul biglietto.
                        </div>
                    @endif

                    @if ($coupon)
                        <div class="mm-form-box mb-4">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                                <h2 class="h4 mb-0">{{ $coupon->user->full_name }}</h2>

                                @if ($coupon->isUsato())
                                    <span class="mm-stato mm-stato-usato">Già utilizzato</span>
                                @elseif ($coupon->isScaduto())
                                    <span class="mm-stato mm-stato-scaduto">Scaduto</span>
                                @else
                                    <span class="mm-stato mm-stato-valido">Valido</span>
                                @endif
                            </div>

                            <dl class="row mb-4 small">
                                <dt class="col-sm-4 text-muted fw-normal">Codice</dt>
                                <dd class="col-sm-8"><code>{{ $coupon->code }}</code></dd>

                                <dt class="col-sm-4 text-muted fw-normal">Email</dt>
                                <dd class="col-sm-8">{{ $coupon->user->email }}</dd>

                                @if ($coupon->user->phone)
                                    <dt class="col-sm-4 text-muted fw-normal">Telefono</dt>
                                    <dd class="col-sm-8">{{ $coupon->user->phone }}</dd>
                                @endif

                                <dt class="col-sm-4 text-muted fw-normal">Emesso il</dt>
                                <dd class="col-sm-8">{{ $coupon->issued_at->format('d/m/Y') }}</dd>

                                <dt class="col-sm-4 text-muted fw-normal">Valido fino al</dt>
                                <dd class="col-sm-8">{{ $coupon->expires_at->format('d/m/Y') }}</dd>

                                @if ($coupon->used_at)
                                    <dt class="col-sm-4 text-muted fw-normal">Utilizzato il</dt>
                                    <dd class="col-sm-8">{{ $coupon->used_at->format('d/m/Y H:i') }}</dd>
                                @endif
                            </dl>

                            @if ($coupon->isUsato())
                                <div class="alert alert-secondary border-0 rounded-4 mb-0">
                                    Questo coupon è già stato usato: non va accettato una seconda volta.
                                </div>
                            @elseif ($coupon->isScaduto())
                                <div class="alert alert-warning border-0 rounded-4 mb-0">
                                    Coupon scaduto il {{ $coupon->expires_at->format('d/m/Y') }}.
                                </div>
                            @else
                                <form method="POST" action="{{ route('admin.convalida.salva') }}"
                                      onsubmit="return confirm('Confermi la convalida? Il coupon non sarà più riutilizzabile.')">
                                    @csrf
                                    <input type="hidden" name="code" value="{{ $coupon->code }}">
                                    <button type="submit" class="btn btn-mm-verde btn-lg w-100">
                                        <i class="bi bi-check2-circle me-1"></i> Convalida il coupon
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                    <div class="mm-card">
                        <h3 class="h5 mb-3">Ultimi coupon emessi</h3>

                        @if ($ultimi->isEmpty())
                            <p class="text-muted mb-0">Nessun coupon emesso finora.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted small">
                                            <th>Persona</th>
                                            <th>Codice</th>
                                            <th>Scadenza</th>
                                            <th>Stato</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ultimi as $c)
                                            <tr>
                                                <td>{{ $c->user->full_name }}</td>
                                                <td>
                                                    <a href="{{ route('admin.convalida', ['codice' => $c->code]) }}">
                                                        <code class="small">{{ $c->code }}</code>
                                                    </a>
                                                </td>
                                                <td class="small">{{ $c->expires_at->format('d/m/Y') }}</td>
                                                <td class="small">{{ $c->etichettaStato() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
