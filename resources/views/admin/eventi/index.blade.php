@extends('layouts.app')

@section('titolo', 'Gestione appuntamenti')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include('partials.prato')
        <div class="container">
            <span class="mm-occhiello">Area staff</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-2">Gestione appuntamenti</h1>
            <p class="lead text-secondary mb-0">Crea le date, apri le prenotazioni, guarda chi viene.</p>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
                <h2 class="h4 mb-0">{{ $eventi->count() }} appuntamenti</h2>
                <a href="{{ route('admin.eventi.create') }}" class="btn btn-mm-oro">
                    <i class="bi bi-plus-lg me-1"></i> Nuovo appuntamento
                </a>
            </div>

            @if ($eventi->isEmpty())

                <div class="mm-card text-center">
                    <div class="fs-1 text-gold mb-2"><i class="bi bi-calendar-plus"></i></div>
                    <h3 class="h5">Non c'è ancora nessun appuntamento</h3>
                    <p class="text-secondary mb-4">
                        Creane uno: finché resta in "bozza" non lo vede nessuno,
                        e quando è pronto lo metti su "pubblicato".
                    </p>
                    <a href="{{ route('admin.eventi.create') }}" class="btn btn-mm-blu">Crea il primo</a>
                </div>

            @else

                <div class="mm-card">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted small">
                                    <th>Quando</th>
                                    <th>Appuntamento</th>
                                    <th>Stato</th>
                                    <th class="text-center">Prenotati</th>
                                    <th class="text-end">Prezzo</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($eventi as $e)
                                    <tr class="{{ $e->isPassato() ? 'opacity-75' : '' }}">
                                        <td class="small">
                                            <strong>{{ $e->inizia_il->format('d/m/Y') }}</strong><br>
                                            <span class="text-muted">{{ $e->inizia_il->format('H:i') }}</span>
                                        </td>

                                        <td>
                                            <strong class="text-navy">{{ $e->titolo }}</strong><br>
                                            <span class="small text-muted">{{ $e->luogo }}</span>
                                            @if ($e->coupon_attivo)
                                                <span class="badge bg-warning text-dark rounded-pill ms-1">coupon</span>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($e->stato === \App\Models\Evento::PUBBLICATO)
                                                <span class="mm-stato mm-stato-valido">Pubblicato</span>
                                            @elseif ($e->stato === \App\Models\Evento::ANNULLATO)
                                                <span class="mm-stato mm-stato-usato">Annullato</span>
                                            @else
                                                <span class="mm-stato mm-stato-scaduto">Bozza</span>
                                            @endif
                                        </td>

                                        <td class="text-center">
                                            <a href="{{ route('admin.eventi.partecipanti', $e) }}" class="text-decoration-none">
                                                <strong>{{ $e->prenotazioni_attive_count }}</strong>
                                                @if ($e->posti)
                                                    <span class="text-muted small">/ {{ $e->posti }}</span>
                                                @endif
                                            </a>
                                        </td>

                                        <td class="text-end small">
                                            @if ($e->isGratuito())
                                                <span class="text-success">Gratuito</span>
                                            @else
                                                {{ number_format((float) $e->prezzo, 2, ',', '.') }} &euro;
                                            @endif
                                        </td>

                                        <td class="text-end">
                                            <div class="d-flex gap-1 justify-content-end">
                                                <a href="{{ route('eventi.show', $e) }}"
                                                   class="btn btn-sm btn-outline-secondary rounded-pill" title="Vedi sul sito">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.eventi.partecipanti', $e) }}"
                                                   class="btn btn-sm btn-outline-secondary rounded-pill" title="Chi viene">
                                                    <i class="bi bi-people"></i>
                                                </a>
                                                <a href="{{ route('admin.eventi.edit', $e) }}"
                                                   class="btn btn-sm btn-outline-primary rounded-pill" title="Modifica">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="{{ route('admin.eventi.destroy', $e) }}"
                                                      onsubmit="return confirm('Eliminare definitivamente questo appuntamento?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill" title="Elimina">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @endif

        </div>
    </section>

@endsection
