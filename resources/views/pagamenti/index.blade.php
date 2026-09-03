@extends('layouts.app')

@section('titolo', 'I miei pagamenti')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include('partials.prato')
        <div class="container">
            <span class="mm-occhiello">Area personale</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">I miei pagamenti</h1>
            <p class="lead text-secondary mm-max-700 mx-auto mb-0">
                Qui vedi cosa hai già saldato e cosa resta da pagare.
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    @if ($daSaldare > 0)
                        <div class="mm-card mb-4 text-center" style="border:2px solid var(--mm-gold)">
                            <div class="mm-aiuto text-uppercase">Totale ancora da pagare</div>
                            <div class="mm-numerone text-gold my-2">
                                {{ number_format($daSaldare, 2, ',', '.') }} &euro;
                            </div>
                            <p class="text-secondary small mb-0">
                                Si paga al ritrovo, o con bonifico se hai scelto quello.
                                Nessun importo viene addebitato dal sito.
                            </p>
                        </div>
                    @endif

                    @if ($pagamenti->isEmpty())

                        <div class="mm-card text-center">
                            <div class="fs-1 text-gold mb-2"><i class="bi bi-wallet2"></i></div>
                            <h2 class="h5">Non c'è ancora niente qui</h2>
                            <p class="text-secondary mb-4">
                                I pagamenti compaiono quando prenoti un appuntamento a pagamento.
                            </p>
                            <a href="{{ route('eventi.index') }}" class="btn btn-mm-blu">Guarda il calendario</a>
                        </div>

                    @else

                        <div class="row g-3">
                            @foreach ($pagamenti as $p)
                                <div class="col-md-6">
                                    <div class="mm-card h-100 {{ $p->stato === \App\Models\Pagamento::ANNULLATO ? 'opacity-75' : '' }}">

                                        <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                            <span class="mm-bollo mm-bollo-oro">
                                                <i class="bi {{ $p->iconaMetodo() }}"></i>
                                            </span>

                                            @if ($p->isPagato())
                                                <span class="mm-stato mm-stato-valido">{{ $p->etichettaStato() }}</span>
                                            @elseif ($p->stato === \App\Models\Pagamento::ANNULLATO)
                                                <span class="mm-stato mm-stato-usato">Annullato</span>
                                            @else
                                                <span class="mm-stato mm-stato-scaduto">Da pagare</span>
                                            @endif
                                        </div>

                                        <h3 class="h6 fw-bold text-navy mb-1">
                                            {{ $p->prenotazione?->evento?->titolo ?? 'Appuntamento' }}
                                        </h3>
                                        <p class="small text-muted mb-3">
                                            {{ $p->prenotazione?->evento?->inizia_il->format('d/m/Y') }}
                                            &bull; {{ $p->prenotazione?->evento?->luogo }}
                                        </p>

                                        <dl class="row small mb-3">
                                            <dt class="col-6 text-muted fw-normal">Importo</dt>
                                            <dd class="col-6 text-end fw-bold">{{ $p->importoLeggibile() }}</dd>

                                            <dt class="col-6 text-muted fw-normal">Metodo</dt>
                                            <dd class="col-6 text-end">{{ $p->etichettaMetodo() }}</dd>

                                            <dt class="col-6 text-muted fw-normal">Riferimento</dt>
                                            <dd class="col-6 text-end font-monospace">{{ $p->codice }}</dd>
                                        </dl>

                                        <a href="{{ route('pagamenti.show', $p) }}" class="btn btn-mm-contorno w-100 btn-sm">
                                            @if ($p->isInAttesa())
                                                Come pagare
                                            @else
                                                Vedi il dettaglio
                                            @endif
                                        </a>

                                    </div>
                                </div>
                            @endforeach
                        </div>

                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('coupon.index') }}" class="btn btn-link text-secondary text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Torna all'area personale
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
