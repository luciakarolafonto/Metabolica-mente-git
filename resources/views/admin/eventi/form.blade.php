@extends('layouts.app')

@section('titolo', $evento->exists ? 'Modifica appuntamento' : 'Nuovo appuntamento')

@section('contenuto')

    <section class="mm-hero mm-hero-piccolo text-center">
        @include('partials.prato')
        <div class="container">
            <span class="mm-occhiello">Area staff</span>
            <h1 class="display-5 fw-extrabold text-navy mt-3 mb-0">
                {{ $evento->exists ? 'Modifica appuntamento' : 'Nuovo appuntamento' }}
            </h1>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8">

                    <div class="mm-form-box mm-form-box-oro">
                        <form method="POST"
                              action="{{ $evento->exists ? route('admin.eventi.update', $evento) : route('admin.eventi.store') }}"
                              novalidate>
                            @csrf
                            @if ($evento->exists)
                                @method('PUT')
                            @endif

                            <h2 class="h5 mb-3">Informazioni principali</h2>

                            <div class="mb-3">
                                <label for="titolo" class="form-label">Titolo</label>
                                <input type="text" id="titolo" name="titolo"
                                       class="form-control @error('titolo') is-invalid @enderror"
                                       value="{{ old('titolo', $evento->titolo) }}"
                                       placeholder="Camminata al tramonto" required autofocus>
                                @error('titolo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label for="sommario" class="form-label">
                                    Frase di presentazione <span class="text-muted fw-normal">(facoltativa)</span>
                                </label>
                                <input type="text" id="sommario" name="sommario"
                                       class="form-control @error('sommario') is-invalid @enderror"
                                       value="{{ old('sommario', $evento->sommario) }}"
                                       placeholder="Un'ora di camminata sul mare, al tramonto">
                                <div class="mm-aiuto mt-1">Compare nella scheda dell'elenco, sotto il titolo.</div>
                                @error('sommario')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label for="descrizione" class="form-label">
                                    Descrizione <span class="text-muted fw-normal">(facoltativa)</span>
                                </label>
                                <textarea id="descrizione" name="descrizione" rows="5"
                                          class="form-control @error('descrizione') is-invalid @enderror"
                                          placeholder="Racconta come sarà la camminata, il percorso, a chi è adatta...">{{ old('descrizione', $evento->descrizione) }}</textarea>
                                @error('descrizione')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <h2 class="h5 mb-3 pt-3 border-top">Quando e dove</h2>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="inizia_il" class="form-label">Inizio</label>
                                    <input type="datetime-local" id="inizia_il" name="inizia_il"
                                           class="form-control @error('inizia_il') is-invalid @enderror"
                                           value="{{ old('inizia_il', $evento->inizia_il?->format('Y-m-d\TH:i')) }}" required>
                                    @error('inizia_il')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="finisce_il" class="form-label">
                                        Fine <span class="text-muted fw-normal">(facoltativa)</span>
                                    </label>
                                    <input type="datetime-local" id="finisce_il" name="finisce_il"
                                           class="form-control @error('finisce_il') is-invalid @enderror"
                                           value="{{ old('finisce_il', $evento->finisce_il?->format('Y-m-d\TH:i')) }}">
                                    @error('finisce_il')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="luogo" class="form-label">Luogo</label>
                                    <input type="text" id="luogo" name="luogo"
                                           class="form-control @error('luogo') is-invalid @enderror"
                                           value="{{ old('luogo', $evento->luogo ?: config('asd.location')) }}" required>
                                    @error('luogo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="ritrovo" class="form-label">
                                        Punto di ritrovo <span class="text-muted fw-normal">(facoltativo)</span>
                                    </label>
                                    <input type="text" id="ritrovo" name="ritrovo"
                                           class="form-control @error('ritrovo') is-invalid @enderror"
                                           value="{{ old('ritrovo', $evento->ritrovo) }}"
                                           placeholder="Davanti all'ingresso principale">
                                    @error('ritrovo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <h2 class="h5 mb-3 pt-3 border-top">Posti, prezzo e pubblicazione</h2>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="posti" class="form-label">
                                        Posti <span class="text-muted fw-normal">(vuoto = illimitati)</span>
                                    </label>
                                    <input type="number" id="posti" name="posti" min="1" max="500"
                                           class="form-control @error('posti') is-invalid @enderror"
                                           value="{{ old('posti', $evento->posti) }}">
                                    @error('posti')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="prezzo" class="form-label">Prezzo a persona (&euro;)</label>
                                    <input type="number" id="prezzo" name="prezzo" min="0" max="9999" step="0.50"
                                           class="form-control @error('prezzo') is-invalid @enderror"
                                           value="{{ old('prezzo', $evento->prezzo ?? 0) }}" required>
                                    <div class="mm-aiuto mt-1">Metti 0 per un appuntamento gratuito.</div>
                                    @error('prezzo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="stato" class="form-label">Stato</label>
                                    <select id="stato" name="stato" class="form-select @error('stato') is-invalid @enderror" required>
                                        @php $statoAttuale = old('stato', $evento->stato ?: \App\Models\Evento::BOZZA); @endphp
                                        <option value="bozza" {{ $statoAttuale === 'bozza' ? 'selected' : '' }}>
                                            Bozza (non visibile)
                                        </option>
                                        <option value="pubblicato" {{ $statoAttuale === 'pubblicato' ? 'selected' : '' }}>
                                            Pubblicato
                                        </option>
                                        <option value="annullato" {{ $statoAttuale === 'annullato' ? 'selected' : '' }}>
                                            Annullato
                                        </option>
                                    </select>
                                    @error('stato')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <h2 class="h5 mb-3 pt-3 border-top">Coupon dedicato</h2>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="coupon_attivo" id="coupon_attivo"
                                       value="1" {{ old('coupon_attivo', $evento->coupon_attivo) ? 'checked' : '' }}>
                                <label class="form-check-label" for="coupon_attivo">
                                    Attiva un coupon dedicato a questo appuntamento
                                </label>
                                <div class="mm-aiuto">
                                    Ogni persona registrata potrà ritirarne <strong>uno solo</strong> per questo
                                    appuntamento, oltre al coupon di prova gratuita.
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label for="coupon_titolo" class="form-label">Titolo del coupon</label>
                                    <input type="text" id="coupon_titolo" name="coupon_titolo"
                                           class="form-control @error('coupon_titolo') is-invalid @enderror"
                                           value="{{ old('coupon_titolo', $evento->coupon_titolo) }}"
                                           placeholder="Coupon camminata al tramonto">
                                    @error('coupon_titolo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="coupon_valore" class="form-label">Sconto (&euro;)</label>
                                    <input type="number" id="coupon_valore" name="coupon_valore" min="0" max="9999" step="0.50"
                                           class="form-control @error('coupon_valore') is-invalid @enderror"
                                           value="{{ old('coupon_valore', $evento->coupon_valore) }}">
                                    <div class="mm-aiuto mt-1">Vuoto o 0 = ingresso omaggio.</div>
                                    @error('coupon_valore')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-3">
                                    <label for="coupon_scadenza" class="form-label">Scade il</label>
                                    <input type="date" id="coupon_scadenza" name="coupon_scadenza"
                                           class="form-control @error('coupon_scadenza') is-invalid @enderror"
                                           value="{{ old('coupon_scadenza', $evento->coupon_scadenza?->format('Y-m-d')) }}">
                                    <div class="mm-aiuto mt-1">Vuoto = il giorno dell'appuntamento.</div>
                                    @error('coupon_scadenza')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mt-5">
                                <button type="submit" class="btn btn-mm-blu px-4">
                                    <i class="bi bi-check2 me-1"></i>
                                    {{ $evento->exists ? 'Salva le modifiche' : 'Crea l\'appuntamento' }}
                                </button>
                                <a href="{{ route('admin.eventi.index') }}" class="btn btn-mm-contorno">Annulla</a>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
