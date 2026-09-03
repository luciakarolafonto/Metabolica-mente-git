{{-- Biglietto di un coupon + pulsanti di scarico. Serve sia per il coupon
     di prova sia per quelli legati agli appuntamenti. --}}

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 mm-no-stampa">
    <h3 class="h5 mb-0">{{ $coupon->titolo }}</h3>

    @if ($coupon->isUsato())
        <span class="mm-stato mm-stato-usato">
            <i class="bi bi-x-circle me-1"></i>{{ $coupon->etichettaStato() }}
        </span>
    @elseif ($coupon->isScaduto())
        <span class="mm-stato mm-stato-scaduto">
            <i class="bi bi-clock-history me-1"></i>{{ $coupon->etichettaStato() }}
        </span>
    @else
        <span class="mm-stato mm-stato-valido">
            <i class="bi bi-check-circle me-1"></i>
            Valido &bull; {{ $coupon->giorniRimanenti() }} giorni rimasti
        </span>
    @endif
</div>

<div class="mm-biglietto">

    <div class="d-flex justify-content-between align-items-center border-bottom border-light border-opacity-50 pb-3 mb-4 flex-wrap gap-2">
        <div>
            <h4 class="m-0 fw-bold">METABOLICA MENTE A.S.D.</h4>
            <small class="text-warning text-uppercase">{{ $coupon->sottotitolo() }}</small>
        </div>
        <span class="mm-timbro">{{ $coupon->descrizioneVantaggio() }}</span>
    </div>

    <div class="row align-items-start g-4">

        <div class="col-md-7">
            <span class="small text-light text-uppercase fw-bold">Coupon intestato a</span>
            <h3 class="display-6 fw-bold text-white mb-3">{{ $coupon->user->full_name }}</h3>

            <div class="mm-riquadro-chiaro border border-warning mb-3">
                <span class="small text-warning text-uppercase fw-bold d-block">Codice univoco</span>
                <span class="mm-codice">{{ $coupon->code }}</span>
            </div>

            <div class="row g-2 small">
                <div class="col-6">
                    <div class="mm-riquadro-scuro">
                        <span class="text-light d-block">Riscattato il</span>
                        <strong>{{ $coupon->issued_at->format('d/m/Y') }}</strong>
                    </div>
                </div>
                <div class="col-6">
                    <div class="mm-riquadro-scuro">
                        <span class="text-warning d-block">Valido fino al</span>
                        <strong>{{ $coupon->expires_at->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="mm-riquadro-chiaro">
                @if ($coupon->evento)
                    <h6 class="fw-bold text-warning mb-2">Vale per</h6>
                    <p class="small mb-3">
                        <strong>{{ $coupon->evento->titolo }}</strong><br>
                        {{ $coupon->evento->quando() }}<br>
                        {{ $coupon->evento->luogo }}
                    </p>
                @else
                    <h6 class="fw-bold text-warning mb-2">La prova include</h6>
                    <ul class="list-unstyled small mb-3">
                        @foreach (config('asd.provided') as $cosa)
                            <li class="mb-2 d-flex align-items-center gap-2">
                                <x-icona :nome="$cosa['icona']" :dim="18" class="text-warning" />
                                {{ $cosa['testo'] }}
                            </li>
                        @endforeach
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-check text-warning"></i>
                            60 min con {{ config('asd.trainer') }}
                        </li>
                    </ul>
                @endif

                <h6 class="fw-bold text-warning mb-2">Porta con te</h6>
                <ul class="list-unstyled small mb-0">
                    @foreach (config('asd.equipment') as $cosa)
                        <li class="mb-2 d-flex align-items-center gap-2">
                            <x-icona :nome="$cosa['icona']" :dim="18" class="text-warning" />
                            {{ $cosa['testo'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    <div class="alert alert-warning text-dark mt-4 mb-0 small">
        <i class="bi bi-info-circle-fill me-1"></i>
        <strong>Istruzioni:</strong> porta questo coupon con te (sul telefono o stampato)
        e presentalo alla trainer {{ config('asd.trainer') }}
        <strong>nel giorno dell'appuntamento</strong>, prima che la lezione cominci.
        Vale una volta sola e non è cedibile.
    </div>

</div>

@if ($coupon->isUsato())
    <div class="alert alert-secondary border-0 rounded-4 mt-3 mm-no-stampa">
        @if ($coupon->prenotazione)
            Coupon già applicato alla tua prenotazione
            <strong class="font-monospace">{{ $coupon->prenotazione->codice }}</strong>
            del {{ $coupon->used_at?->format('d/m/Y') }}: lo sconto è già calcolato nell'importo.
        @else
            Coupon utilizzato il <strong>{{ $coupon->used_at?->format('d/m/Y') }}</strong>.
        @endif
    </div>
@elseif ($coupon->isScaduto())
    <div class="alert alert-warning border-0 rounded-4 mt-3 mm-no-stampa">
        Coupon scaduto. Scrivici a
        <a href="mailto:{{ config('asd.email') }}">{{ config('asd.email') }}</a>.
    </div>
@endif

<div class="d-flex flex-wrap justify-content-center gap-2 mt-3 mm-no-stampa">
    <a href="{{ route('coupon.pdf', $coupon) }}" class="btn btn-mm-blu">
        <i class="bi bi-file-earmark-pdf me-1"></i> Scarica il PDF
    </a>
    <a href="{{ route('coupon.png', $coupon) }}" class="btn btn-mm-oro">
        <i class="bi bi-download me-1"></i> Scarica l'immagine
    </a>
    <button type="button" class="btn btn-mm-contorno" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> Stampa
    </button>
    <form method="POST" action="{{ route('coupon.rinvia', $coupon) }}">
        @csrf
        <button type="submit" class="btn btn-link text-secondary text-decoration-none">
            <i class="bi bi-envelope me-1"></i> Rimandamelo per email
        </button>
    </form>
</div>
