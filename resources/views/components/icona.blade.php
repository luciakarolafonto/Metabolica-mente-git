{{--
    Icona riutilizzabile.

    Uso:  <x-icona nome="scarpa" class="text-gold" />
          <x-icona nome="bi-headphones" />

    Se il nome comincia con "bi-" usa le icone di Bootstrap.
    Altrimenti disegna uno dei simboli fatti in casa qui sotto:
    quelli che alle icone Bootstrap mancano (scarpe, magliette, ecc.).
--}}

@props(['nome' => '', 'dim' => 24])

@php
    $classi = trim('mm-icona '.$attributes->get('class', ''));
@endphp

@if (str_starts_with($nome, 'bi-'))

    <i class="bi {{ $nome }} {{ $attributes->get('class') }}" style="font-size:{{ $dim }}px"></i>

@else

    <svg class="{{ $classi }}" width="{{ $dim }}" height="{{ $dim }}" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="1.6"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">

        @switch($nome)

            {{-- Scarpa da ginnastica, vista di lato --}}
            @case('scarpa')
                <path d="M2 16.5c0-1.4.5-2.6 1.2-3.6l2-2.8c.4-.5 1-.8 1.6-.8h1.4l1.5 1.6 2.5.7 3.1 1.6c1.6.8 3 1.3 4.4 1.5.8.1 1.3.8 1.3 1.6v1.2c0 .8-.6 1.5-1.5 1.5H3.5C2.7 19 2 18.3 2 17.4z"/>
                <path d="M6.2 9.3 8 12"/>
                <path d="M9.4 10.9 11 13.2"/>
                <path d="M12.4 12.3 14 14.4"/>
                <path d="M2.4 17.6h19.2"/>
                @break

            {{-- Maglietta --}}
            @case('maglietta')
                <path d="M8.5 3 12 5.2 15.5 3l4.6 2.4c.5.3.7.9.5 1.4l-1.2 3c-.2.5-.8.8-1.3.6l-1.6-.5V20c0 .6-.4 1-1 1H8.5c-.6 0-1-.4-1-1V9.9l-1.6.5c-.5.2-1.1-.1-1.3-.6l-1.2-3c-.2-.5 0-1.1.5-1.4z"/>
                <path d="M8.5 3c0 1.7 1.6 3 3.5 3s3.5-1.3 3.5-3"/>
                @break

            {{-- Borraccia / bottiglietta d'acqua --}}
            @case('borraccia')
                <path d="M10 2h4v2.2c0 .5.2 1 .6 1.4l.9.9c.6.6 1 1.5 1 2.4V20c0 .6-.4 1-1 1H8.5c-.6 0-1-.4-1-1V8.9c0-.9.3-1.8 1-2.4l.9-.9c.4-.4.6-.9.6-1.4z"/>
                <path d="M7.5 12h9"/>
                <path d="M7.5 16h9"/>
                @break

            {{-- Asciugamano piegato --}}
            @case('asciugamano')
                <rect x="3" y="6" width="18" height="12" rx="2.5"/>
                <path d="M7 6v12"/>
                <path d="M17 6v12"/>
                <path d="M10 9.5h4"/>
                <path d="M10 12h4"/>
                @break

            {{-- Fascia elastica F-Band --}}
            @case('fascia')
                <ellipse cx="12" cy="12" rx="9.2" ry="5.4"/>
                <ellipse cx="12" cy="12" rx="4.4" ry="2.4"/>
                @break

            {{-- Cuore / benessere, usato come ripiego --}}
            @default
                <path d="M12 20s-7-4.4-7-9a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 4.6-7 9-7 9z"/>

        @endswitch

    </svg>

@endif
