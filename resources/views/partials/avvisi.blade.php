{{-- Messaggi di esito (successo / errore / informazione) mostrati sotto la navbar --}}

@if (session('successo') || session('errore') || session('info'))
    <div class="container mt-3 mm-no-stampa">
        @if (session('successo'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-start gap-2" role="alert">
                <strong>Fatto:</strong> <span>{{ session('successo') }}</span>
            </div>
        @endif

        @if (session('errore'))
            <div class="alert alert-danger border-0 shadow-sm rounded-4 d-flex align-items-start gap-2" role="alert">
                <strong>Attenzione:</strong> <span>{{ session('errore') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info border-0 shadow-sm rounded-4 d-flex align-items-start gap-2" role="alert">
                <span>{{ session('info') }}</span>
            </div>
        @endif
    </div>
@endif
