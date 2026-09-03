<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('descrizione', 'Associazione Sportiva Dilettantistica Metabolica Mente. Camminata metabolica nei parchi con cuffie wireless e coupon di prova gratuita valido ' . config('asd.coupon.days') . ' giorni.')">

    <title>@yield('titolo', 'Camminata Metabolica & Benessere') | {{ config('asd.name') }}</title>

    <link rel="icon" href="{{ asset('img/logo.jpg') }}">

    {{-- Caratteri: se non c'e' connessione il sito usa quelli di sistema --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Bootstrap e icone sono salvati dentro il progetto: funzionano anche offline --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/metabolica.css') }}">
</head>
<body>

    @include('partials.promo')
    @include('partials.navbar')
    @include('partials.avvisi')

    <main>
        @yield('contenuto')
    </main>

    @include('partials.footer')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/sito.js') }}"></script>
    @stack('scripts')
</body>
</html>
