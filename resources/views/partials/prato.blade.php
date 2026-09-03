{{--
    Il prato dietro l'intestazione.
    Se in public/img/ c'e' una foto (prato.jpg o prato.png) viene usata quella,
    altrimenti si usa il disegno prato.svg. Sfuma via mentre si scorre.
--}}

@php
    $sfondoPrato = collect(['img/prato.jpg', 'img/prato.png', 'img/prato.webp', 'img/prato.svg'])
        ->first(fn ($f) => is_file(public_path($f)));
@endphp

@if ($sfondoPrato)
    <div class="mm-prato" style="background-image:url('{{ asset($sfondoPrato) }}')" aria-hidden="true"></div>
@endif
