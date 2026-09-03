@extends('layouts.app')

@section('titolo', 'Pagamento ' . $pagamento->codice)

@php
    $evento  = $pagamento->prenotazione?->evento;
    $metodi  = app(\App\Services\GestorePagamenti::class)->metodiDisponibili();
    $online  = app(\App\Services\GestorePagamenti::class)->onlineAttivo();
@endphp

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include('partials.prato')
        <div class="container">
            <span class="mm-occhiello">Pagamento</span>
            <h1 class="display-6 fw-extrabold text-navy mt-3 mb-2">
                {{ $pagamento->isPagato() ? 'Pagamento registrato' : 'Come completare il pagamento' }}
            </h1>
            <p class="lead text-secondary mb-0">
                {{ $evento?->titolo }}
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center g-4">

                {{-- ============ Riepilogo ============ --}}
                <div class="col-lg-5">
                    <div class="mm-biglietto">
                        <div class="d-flex justify-content-between align-items-center border-bottom border-light border-opacity-50 pb-3 mb-3">
                            <div>
                                <h2 class="h5 m-0 fw-bold">Riepilogo</h2>
                                <small class="text-warning text-uppercase">{{ $evento?->titolo }}</small>
                            </div>
                            @if ($pagamento->isPagato())
                                <span class="badge bg-success rounded-pill px-3 py-2">Pagato</span>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-2">Da pagare</span>
                            @endif
                        </div>

                        <div class="mm-riquadro-chiaro border border-warning mb-3 text-center">
                            <span class="small text-warning text-uppercase fw-bold d-block">Importo</span>
                            <span class="display-6 fw-bold text-white">{{ $pagamento->importoLeggibile() }}</span>
                        </div>

                        <div class="row g-2 small">
                            <div class="col-12">
                                <div class="mm-riquadro-scuro">
                                    <span class="text-warning d-block">Riferimento del pagamento</span>
                                    <strong class="mm-codice">{{ $pagamento->codice }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mm-riquadro-scuro">
                                    <span class="text-light d-block">Quando</span>
                                    <strong>{{ $evento?->inizia_il->format('d/m/Y H:i') }}</strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mm-riquadro-scuro">
                                    <span class="text-light d-block">Posti</span>
                                    <strong>{{ $pagamento->prenotazione?->posti }}</strong>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mm-riquadro-scuro">
                                    <span class="text-light d-block">Dove</span>
                                    <strong>{{ $evento?->luogo }}</strong>
                                </div>
                            </div>
                        </div>

                        @if ($pagamento->prenotazione?->coupon)
                            <div class="mt-3 small text-light">
                                <i class="bi bi-ticket-perforated text-warning me-1"></i>
                                Sconto del coupon
                                <strong class="font-monospace">{{ $pagamento->prenotazione->coupon->code }}</strong>
                                già applicato.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ============ Istruzioni ============ --}}
                <div class="col-lg-6">

                    @if ($pagamento->isPagato())

                        <div class="mm-card text-center">
                            <div class="fs-1 text-success mb-2"><i class="bi bi-check-circle-fill"></i></div>
                            <h2 class="h4">Tutto a posto</h2>
                            <p class="text-secondary">
                                Il pagamento risulta registrato
                                @if ($pagamento->pagato_il)
                                    il {{ $pagamento->pagato_il->format('d/m/Y') }}
                                @endif
                                con il metodo <strong>{{ $pagamento->etichettaMetodo() }}</strong>.
                                Non devi fare altro: ci vediamo al ritrovo.
                            </p>
                            <a href="{{ route('pagamenti.index') }}" class="btn btn-mm-contorno">I miei pagamenti</a>
                        </div>

                    @elseif ($pagamento->stato === \App\Models\Pagamento::ANNULLATO)

                        <div class="mm-card text-center">
                            <div class="fs-1 text-muted mb-2"><i class="bi bi-x-circle"></i></div>
                            <h2 class="h4">Pagamento annullato</h2>
                            <p class="text-secondary mb-0">
                                La prenotazione collegata è stata annullata, quindi non c'è più
                                nulla da pagare.
                            </p>
                        </div>

                    @else

                        <div class="mm-card mb-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <span class="mm-bollo mm-bollo-oro"><i class="bi {{ $pagamento->iconaMetodo() }}"></i></span>
                                <div>
                                    <h2 class="h5 mb-0">{{ $pagamento->etichettaMetodo() }}</h2>
                                    <span class="small text-muted">Il metodo che hai scelto</span>
                                </div>
                            </div>

                            <p class="text-secondary">{{ $pagamento->istruzioniMetodo() }}</p>

                            {{-- Istruzioni specifiche del bonifico --}}
                            @if ($pagamento->metodo === 'bonifico' && ! empty($bonifico['iban']))
                                <div class="mm-nota">
                                    <div class="mb-2">
                                        <span class="mm-aiuto d-block text-uppercase">Intestato a</span>
                                        <strong>{{ $bonifico['intestatario'] }}</strong>
                                    </div>
                                    <div class="mb-2">
                                        <span class="mm-aiuto d-block text-uppercase">IBAN</span>
                                        <strong class="font-monospace">{{ $bonifico['iban'] }}</strong>
                                    </div>
                                    @if (! empty($bonifico['banca']))
                                        <div class="mb-2">
                                            <span class="mm-aiuto d-block text-uppercase">Banca</span>
                                            <strong>{{ $bonifico['banca'] }}</strong>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="mm-aiuto d-block text-uppercase">Causale da scrivere</span>
                                        <strong class="font-monospace">{{ $causale }}</strong>
                                    </div>
                                </div>
                                <p class="mm-aiuto mt-2 mb-0">
                                    Scrivi la causale esattamente così: è come la trainer
                                    riconosce il tuo versamento.
                                </p>
                            @endif

                            {{-- Pagamento con carta sul sito --}}
                            @if ($pagamento->metodo === 'carta_online')
                                @if ($online)
                                    <form method="POST" action="{{ route('pagamenti.paga', $pagamento) }}" class="mt-3">
                                        @csrf
                                        <button type="submit" class="btn btn-mm-blu w-100 py-3">
                                            <i class="bi bi-lock-fill me-1"></i>
                                            Paga ora {{ $pagamento->importoLeggibile() }}
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-warning border-0 rounded-4 mb-0">
                                        Il pagamento con carta sul sito non è ancora attivo.
                                        Scegli qui sotto un altro metodo: si paga al ritrovo.
                                    </div>
                                @endif
                            @endif

                            @if (! $pagamento->isOnline())
                                <div class="mm-nota mt-3">
                                    <i class="bi bi-info-circle-fill text-gold me-1"></i>
                                    <strong>Nessun addebito dal sito.</strong> Qui stai solo dicendo
                                    come pagherai: la trainer {{ config('asd.trainer') }} lo sa già
                                    e ti aspetta con il resto pronto.
                                </div>
                            @endif
                        </div>

                        {{-- Cambio metodo --}}
                        <div class="mm-card">
                            <h2 class="h6 mb-3">Hai cambiato idea?</h2>

                            <form method="POST" action="{{ route('pagamenti.metodo', $pagamento) }}">
                                @csrf

                                @foreach ($metodi as $chiave => $m)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="metodo"
                                               id="metodo-{{ $chiave }}" value="{{ $chiave }}"
                                               {{ $pagamento->metodo === $chiave ? 'checked' : '' }}>
                                        <label class="form-check-label" for="metodo-{{ $chiave }}">
                                            <i class="bi {{ $m['icona'] }} me-1 text-gold"></i>
                                            {{ $m['etichetta'] }}
                                            <span class="d-block mm-aiuto">{{ $m['descrizione'] }}</span>
                                        </label>
                                    </div>
                                @endforeach

                                <button type="submit" class="btn btn-mm-contorno btn-sm mt-2">
                                    Aggiorna il metodo
                                </button>
                            </form>
                        </div>

                    @endif

                    <div class="text-center mt-4">
                        <a href="{{ route('pagamenti.index') }}" class="btn btn-link text-secondary text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Tutti i miei pagamenti
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
