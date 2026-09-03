@extends('layouts.app')

@section('titolo', $evento->titolo)
@section('descrizione', $evento->sommario ?: 'Appuntamento di camminata metabolica a ' . $evento->luogo)

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo">
        @include('partials.prato')
        <div class="container">

            <a href="{{ route('eventi.index') }}" class="small text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Tutti gli appuntamenti
            </a>

            <div class="d-flex flex-wrap gap-2 mt-3 mb-2">
                @if ($evento->isAnnullato())
                    <span class="mm-stato mm-stato-usato">Annullato</span>
                @elseif ($evento->isPassato())
                    <span class="mm-stato mm-stato-scaduto">Già svolto</span>
                @elseif ($evento->isGratuito())
                    <span class="badge bg-success px-3 py-2 rounded-pill">Gratuito</span>
                @else
                    <span class="badge bg-gold text-white px-3 py-2 rounded-pill">
                        {{ rtrim(rtrim(number_format((float) $evento->prezzo, 2, ',', '.'), '0'), ',') }} &euro; a persona
                    </span>
                @endif

                @if ($evento->stato === \App\Models\Evento::BOZZA)
                    <span class="badge bg-secondary px-3 py-2 rounded-pill">Bozza (visibile solo allo staff)</span>
                @endif
            </div>

            <h1 class="display-5 fw-extrabold text-navy mb-3">{{ $evento->titolo }}</h1>

            <div class="d-flex flex-wrap gap-3 text-secondary">
                <span><i class="bi bi-clock text-gold me-1"></i> {{ $evento->quando() }}</span>
                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $evento->luogo }}</span>
                @if ($evento->posti)
                    <span><i class="bi bi-people text-navy me-1"></i> {{ $evento->postiRimasti() }} posti liberi su {{ $evento->posti }}</span>
                @endif
            </div>

        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row g-5">

                {{-- ================= COLONNA SINISTRA: descrizione ================= --}}
                <div class="col-lg-7">

                    @if ($evento->descrizione)
                        <div class="mm-card mb-4">
                            <h2 class="h4 mb-3">L'appuntamento</h2>
                            <div style="white-space: pre-line">{{ $evento->descrizione }}</div>
                        </div>
                    @endif

                    <div class="mm-card mb-4">
                        <h2 class="h5 mb-3">Dettagli pratici</h2>
                        <ul class="mm-lista-icone mb-0">
                            <li>
                                <span class="mm-bollo mm-bollo-oro"><i class="bi bi-calendar-week"></i></span>
                                {{ $evento->quando() }}
                                @if ($evento->finisce_il)
                                    &mdash; fino alle {{ $evento->finisce_il->format('H:i') }}
                                @endif
                            </li>
                            <li>
                                <span class="mm-bollo mm-bollo-corallo"><i class="bi bi-geo-alt-fill"></i></span>
                                {{ $evento->luogo }}@if ($evento->ritrovo) &mdash; {{ $evento->ritrovo }}@endif
                            </li>
                            <li>
                                <span class="mm-bollo mm-bollo-blu"><i class="bi bi-cash-coin"></i></span>
                                @if ($evento->isGratuito())
                                    Partecipazione gratuita
                                @else
                                    {{ rtrim(rtrim(number_format((float) $evento->prezzo, 2, ',', '.'), '0'), ',') }} &euro; a persona
                                @endif
                            </li>
                        </ul>
                    </div>

                    <div class="mm-card">
                        <h2 class="h5 mb-3">Cosa portare</h2>
                        <ul class="mm-lista-icone mb-4">
                            @foreach (config('asd.equipment') as $cosa)
                                <li>
                                    <span class="mm-bollo mm-bollo-oro"><x-icona :nome="$cosa['icona']" :dim="20" /></span>
                                    {{ $cosa['testo'] }}
                                </li>
                            @endforeach
                        </ul>

                        <h2 class="h5 mb-3">Mettiamo noi</h2>
                        <ul class="mm-lista-icone mb-0">
                            @foreach (config('asd.provided') as $cosa)
                                <li>
                                    <span class="mm-bollo mm-bollo-verde"><x-icona :nome="$cosa['icona']" :dim="20" /></span>
                                    {{ $cosa['testo'] }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

                {{-- ================= COLONNA DESTRA: prenotazione ================= --}}
                <div class="col-lg-5">

                    {{-- ---------- Coupon dedicato all'evento ---------- --}}
                    @if ($evento->coupon_attivo)
                        <div class="mm-card mb-4" style="border:2px solid var(--mm-gold)">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="mm-bollo mm-bollo-oro"><i class="bi bi-ticket-perforated"></i></span>
                                <h2 class="h5 mb-0">{{ $evento->coupon_titolo ?: 'Coupon dedicato' }}</h2>
                            </div>

                            <p class="text-secondary small">
                                @if ((float) ($evento->coupon_valore ?? 0) > 0)
                                    Sconto di
                                    <strong>{{ rtrim(rtrim(number_format((float) $evento->coupon_valore, 2, ',', '.'), '0'), ',') }} &euro;</strong>
                                    su questo appuntamento.
                                @else
                                    Ingresso <strong>omaggio</strong> per questo appuntamento.
                                @endif
                                Se ne può ritirare <strong>uno solo a persona</strong>.
                            </p>

                            @auth
                                @if ($coupon)
                                    <div class="mm-nota">
                                        Il tuo codice: <strong class="font-monospace">{{ $coupon->code }}</strong>
                                        &mdash; {{ $coupon->etichettaStato() }}.
                                        <a href="{{ route('coupon.index') }}">Vai all'area personale</a>
                                        per scaricarlo.
                                    </div>
                                @elseif ($evento->couponRitirabile())
                                    <form method="POST" action="{{ route('eventi.coupon', $evento) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-mm-oro w-100">
                                            <i class="bi bi-stars me-1"></i> Ritira il coupon
                                        </button>
                                    </form>
                                @else
                                    <p class="small text-muted mb-0">Il coupon di questo appuntamento non è più ritirabile.</p>
                                @endif
                            @else
                                <a href="{{ route('registrazione') }}" class="btn btn-mm-oro w-100">
                                    Registrati per ritirarlo
                                </a>
                            @endauth
                        </div>
                    @endif

                    {{-- ---------- Prenotazione ---------- --}}
                    <div class="mm-card">
                        <h2 class="h4 mb-3">Prenota il tuo posto</h2>

                        @guest
                            <p class="text-secondary">
                                Per prenotare serve un account: ci vuole un minuto e non chiediamo
                                dati di pagamento.
                            </p>
                            <a href="{{ route('registrazione') }}" class="btn btn-mm-blu w-100 mb-2">Registrati</a>
                            <a href="{{ route('accesso') }}" class="btn btn-mm-contorno w-100">Ho già un account</a>
                        @endguest

                        @auth
                            @if ($prenotazione && ! $prenotazione->isAnnullata())

                                <div class="alert alert-success border-0 rounded-4">
                                    <strong>Sei prenotato!</strong> Presenta il codice alla trainer
                                    {{ config('asd.trainer') }} nel giorno dell'appuntamento.
                                </div>

                                <div class="mm-riquadro-scuro text-center mb-3"
                                     style="background: var(--mm-navy); color:#fff; padding:1.2rem">
                                    <div class="small text-warning text-uppercase">Codice prenotazione</div>
                                    <div class="mm-codice">{{ $prenotazione->codice }}</div>
                                </div>

                                <dl class="row small mb-3">
                                    <dt class="col-6 text-muted fw-normal">Posti</dt>
                                    <dd class="col-6 text-end">{{ $prenotazione->posti }}</dd>

                                    <dt class="col-6 text-muted fw-normal">Importo</dt>
                                    <dd class="col-6 text-end">
                                        @if ($prenotazione->isGratuita())
                                            <span class="text-success fw-bold">Gratuito</span>
                                        @else
                                            <strong>{{ number_format((float) $prenotazione->importo, 2, ',', '.') }} &euro;</strong>
                                        @endif
                                    </dd>

                                    <dt class="col-6 text-muted fw-normal">Pagamento</dt>
                                    <dd class="col-6 text-end">{{ $prenotazione->etichettaMetodo() }}</dd>

                                    <dt class="col-6 text-muted fw-normal">Stato</dt>
                                    <dd class="col-6 text-end">{{ $prenotazione->etichettaStato() }}</dd>
                                </dl>

                                @unless ($evento->isPassato())
                                    <form method="POST" action="{{ route('eventi.annulla', $evento) }}"
                                          onsubmit="return confirm('Vuoi davvero annullare la prenotazione?')">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-secondary text-decoration-none w-100">
                                            Annulla la prenotazione
                                        </button>
                                    </form>
                                @endunless

                            @elseif ($motivo = $evento->motivoNonPrenotabile())

                                <div class="alert alert-secondary border-0 rounded-4 mb-0">{{ $motivo }}</div>

                            @else

                                <form method="POST" action="{{ route('eventi.prenota', $evento) }}">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="posti" class="form-label">Quante persone</label>
                                        <input type="number" id="posti" name="posti" min="1"
                                               max="{{ $evento->postiRimasti() !== null ? min($evento->postiRimasti(), 10) : 10 }}"
                                               value="{{ old('posti', 1) }}"
                                               class="form-control @error('posti') is-invalid @enderror" required>
                                        @error('posti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    @unless ($evento->isGratuito())
                                        <div class="mb-3">
                                            <label class="form-label">Come preferisci pagare</label>

                                            @foreach ($metodi as $chiave => $m)
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio" name="metodo"
                                                           id="metodo-{{ $chiave }}" value="{{ $chiave }}"
                                                           {{ old('metodo', array_key_first($metodi)) === $chiave ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="metodo-{{ $chiave }}">
                                                        <i class="bi {{ $m['icona'] }} me-1 text-gold"></i>
                                                        {{ $m['etichetta'] }}
                                                        <span class="d-block mm-aiuto">{{ $m['descrizione'] }}</span>
                                                    </label>
                                                </div>
                                            @endforeach

                                            @error('metodo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror

                                            <div class="mm-nota mt-2">
                                                <i class="bi bi-info-circle-fill text-gold me-1"></i>
                                                Il sito non addebita nulla: qui dici solo come pagherai,
                                                così la trainer lo sa in anticipo.
                                            </div>
                                        </div>
                                    @else
                                        {{-- Appuntamento gratuito: nessuna scelta da fare --}}
                                        <input type="hidden" name="metodo" value="{{ array_key_first($metodi) }}">
                                        <div class="mm-nota mb-3">
                                            <i class="bi bi-gift-fill text-gold me-1"></i>
                                            Questo appuntamento è gratuito: non c'è niente da pagare.
                                        </div>
                                    @endunless

                                    @if ($coupon && $coupon->isValido())
                                        <div class="mm-nota mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="usa_coupon"
                                                       id="usa_coupon" value="1" checked>
                                                <label class="form-check-label" for="usa_coupon">
                                                    Usa il coupon <strong class="font-monospace">{{ $coupon->code }}</strong>
                                                    ({{ $coupon->descrizioneVantaggio() }})
                                                </label>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="note" class="form-label">
                                            Note per la trainer <span class="text-muted fw-normal">(facoltativo)</span>
                                        </label>
                                        <textarea id="note" name="note" rows="2"
                                                  class="form-control @error('note') is-invalid @enderror"
                                                  placeholder="Es. vengo con mia sorella, prima volta...">{{ old('note') }}</textarea>
                                        @error('note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>

                                    <button type="submit" class="btn btn-mm-blu w-100 py-3">
                                        <i class="bi bi-check2-circle me-1"></i> Conferma la prenotazione
                                    </button>
                                </form>

                            @endif
                        @endauth
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
