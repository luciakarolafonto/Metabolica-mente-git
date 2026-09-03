@extends('layouts.app')

@section('titolo', 'Chi viene — ' . $evento->titolo)

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo">
        @include('partials.prato')
        <div class="container">
            <a href="{{ route('admin.eventi.index') }}" class="small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Tutti gli appuntamenti
            </a>
            <span class="mm-occhiello d-block mt-3 mb-2" style="width:fit-content">Area staff</span>
            <h1 class="display-6 fw-extrabold text-navy mb-2">{{ $evento->titolo }}</h1>
            <p class="text-secondary mb-0">
                <i class="bi bi-clock text-gold me-1"></i> {{ $evento->quando() }}
                &nbsp;&bull;&nbsp;
                <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $evento->luogo }}
            </p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">

            {{-- ---------- Riepilogo ---------- --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-lg-3">
                    <div class="mm-card text-center py-4">
                        <div class="mm-numerone text-navy">{{ $evento->postiOccupati() }}</div>
                        <div class="small text-muted">Posti prenotati</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="mm-card text-center py-4">
                        <div class="mm-numerone text-gold">
                            {{ $evento->postiRimasti() !== null ? $evento->postiRimasti() : '∞' }}
                        </div>
                        <div class="small text-muted">Posti liberi</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="mm-card text-center py-4">
                        <div class="mm-numerone text-verde">{{ number_format($incassato, 2, ',', '.') }} &euro;</div>
                        <div class="small text-muted">Già incassato</div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="mm-card text-center py-4">
                        <div class="mm-numerone text-corallo">
                            {{ number_format($incassoAtteso - $incassato, 2, ',', '.') }} &euro;
                        </div>
                        <div class="small text-muted">Ancora da incassare</div>
                    </div>
                </div>
            </div>

            {{-- ---------- Chi paga come ---------- --}}
            @php
                $attive = $prenotazioni->where('stato', \App\Models\Prenotazione::CONFERMATA);
            @endphp

            @if ($attive->isNotEmpty())
                <div class="mm-card mb-4">
                    <h2 class="h5 mb-3">Come pagano</h2>
                    <div class="row g-3">
                        @foreach (\App\Models\Prenotazione::METODI as $chiave => $etichetta)
                            @php $quanti = $attive->where('metodo', $chiave)->count(); @endphp
                            <div class="col-md-4">
                                <div class="d-flex align-items-center gap-3">
                                    <span class="mm-bollo {{ $chiave === 'contanti' ? 'mm-bollo-verde' : ($chiave === 'carta' ? 'mm-bollo-blu' : 'mm-bollo-oro') }}">
                                        <i class="bi {{ $chiave === 'contanti' ? 'bi-cash-coin' : ($chiave === 'carta' ? 'bi-credit-card' : 'bi-bank') }}"></i>
                                    </span>
                                    <div>
                                        <strong class="text-navy">{{ $quanti }}</strong>
                                        <div class="small text-muted">{{ $etichetta }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ---------- Elenco ---------- --}}
            <div class="mm-card">
                <h2 class="h5 mb-3">Elenco delle prenotazioni</h2>

                @if ($prenotazioni->isEmpty())
                    <p class="text-muted mb-0">Nessuno si è ancora prenotato per questo appuntamento.</p>
                @else
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Persona</th>
                                    <th>Codice</th>
                                    <th class="text-center">Posti</th>
                                    <th>Pagamento</th>
                                    <th class="text-end">Importo</th>
                                    <th class="text-end">Stato</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prenotazioni as $p)
                                    <tr class="{{ $p->isAnnullata() ? 'opacity-50' : '' }}">
                                        <td>
                                            <strong class="text-navy">{{ $p->user->full_name }}</strong><br>
                                            <span class="small text-muted">{{ $p->user->email }}</span>
                                            @if ($p->user->phone)
                                                <br><span class="small text-muted">{{ $p->user->phone }}</span>
                                            @endif
                                            @if ($p->note)
                                                <div class="small fst-italic text-secondary mt-1">"{{ $p->note }}"</div>
                                            @endif
                                        </td>

                                        <td class="font-monospace small">
                                            {{ $p->codice }}
                                            @if ($p->coupon)
                                                <br><span class="badge bg-warning text-dark rounded-pill">
                                                    coupon {{ $p->coupon->code }}
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center">{{ $p->posti }}</td>

                                        <td class="small">{{ $p->etichettaMetodo() }}</td>

                                        <td class="text-end">
                                            @if ($p->isGratuita())
                                                <span class="text-success">Gratuito</span>
                                            @else
                                                {{ number_format((float) $p->importo, 2, ',', '.') }} &euro;
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            @if ($p->isAnnullata())
                                                <span class="mm-stato mm-stato-usato">Annullata</span>
                                            @elseif ($p->isGratuita())
                                                <span class="mm-stato mm-stato-valido">Confermata</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.prenotazioni.pagamento', $p) }}">
                                                    @csrf
                                                    <input type="hidden" name="pagato" value="{{ $p->pagato ? 0 : 1 }}">
                                                    <button type="submit"
                                                            class="btn btn-sm rounded-pill {{ $p->pagato ? 'btn-success' : 'btn-outline-secondary' }}">
                                                        @if ($p->pagato)
                                                            <i class="bi bi-check2-circle me-1"></i> Pagata
                                                        @else
                                                            Segna come pagata
                                                        @endif
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    </section>

@endsection
