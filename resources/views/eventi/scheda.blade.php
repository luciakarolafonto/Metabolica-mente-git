{{-- Scheda riassuntiva di un appuntamento, usata in home e nell'elenco --}}

<div class="mm-card h-100 d-flex flex-column">

    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <span class="badge bg-navy px-3 py-2 rounded-pill">
            <i class="bi bi-calendar-week me-1"></i>
            {{ $evento->inizia_il->format('d/m/Y') }}
        </span>

        @if ($evento->isAnnullato())
            <span class="mm-stato mm-stato-usato">Annullato</span>
        @elseif ($evento->isGratuito())
            <span class="badge bg-success px-3 py-2 rounded-pill">Gratuito</span>
        @else
            <span class="badge bg-gold text-white px-3 py-2 rounded-pill">
                {{ rtrim(rtrim(number_format((float) $evento->prezzo, 2, ',', '.'), '0'), ',') }} &euro;
            </span>
        @endif
    </div>

    <h3 class="h4 fw-bold text-navy">{{ $evento->titolo }}</h3>

    <p class="text-muted small mb-2">
        <i class="bi bi-clock me-1 text-gold"></i> {{ $evento->quando() }}
    </p>
    <p class="text-muted small mb-3">
        <i class="bi bi-geo-alt-fill me-1 text-danger"></i> {{ $evento->luogo }}
    </p>

    @if ($evento->sommario)
        <p class="text-secondary small">{{ $evento->sommario }}</p>
    @endif

    <div class="d-flex flex-wrap gap-2 mb-3">
        @if ($evento->coupon_attivo)
            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                <i class="bi bi-ticket-perforated me-1"></i> Coupon dedicato
            </span>
        @endif

        @php $rimasti = $evento->postiRimasti(); @endphp
        @if ($rimasti !== null)
            <span class="badge bg-light text-dark border rounded-pill px-3 py-2">
                @if ($rimasti > 0)
                    <i class="bi bi-people me-1"></i> {{ $rimasti }} posti liberi
                @else
                    <i class="bi bi-x-circle me-1"></i> Posti esauriti
                @endif
            </span>
        @endif
    </div>

    <div class="mt-auto">
        <a href="{{ route('eventi.show', $evento) }}" class="btn btn-mm-blu w-100">
            @if ($evento->isPrenotabile())
                Prenota il tuo posto
            @else
                Vedi i dettagli
            @endif
        </a>
    </div>

</div>
