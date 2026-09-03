<nav class="navbar navbar-expand-lg sticky-top mm-navbar py-3 mm-no-stampa">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
            <img src="{{ asset('img/logo.jpg') }}" alt="Logo {{ config('asd.name') }}">
            <span class="d-flex flex-column lh-1">
                <span class="mm-marchio-nome">Metabolica</span>
                <span class="mm-marchio-sotto">&#10022; Mente A.S.D. &#10022;</span>
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#menuPrincipale" aria-controls="menuPrincipale"
                aria-expanded="false" aria-label="Apri il menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipale">

            <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'attivo' : '' }}" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('metodo') ? 'attivo' : '' }}" href="{{ route('metodo') }}">Cos'&egrave;</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}#benefici">I benefici</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('eventi.*') ? 'attivo' : '' }}" href="{{ route('eventi.index') }}">
                        Appuntamenti
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('chi-siamo') ? 'attivo' : '' }}" href="{{ route('chi-siamo') }}">La trainer</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('metodo') }}#faq">FAQ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contatti') ? 'attivo' : '' }}" href="{{ route('contatti') }}">Contatti</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                @auth
                    @if (auth()->user()->is_staff)
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary rounded-pill btn-sm px-3 dropdown-toggle"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-tools me-1"></i> Staff
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4">
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.eventi.index') }}">
                                        <i class="bi bi-calendar-week me-2 text-gold"></i> Gestione appuntamenti
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.eventi.create') }}">
                                        <i class="bi bi-plus-lg me-2 text-gold"></i> Nuovo appuntamento
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.convalida') }}">
                                        <i class="bi bi-ticket-perforated me-2 text-gold"></i> Convalida coupon
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <a href="{{ route('coupon.index') }}" class="btn btn-mm-blu btn-sm px-3 py-2">
                        <i class="bi bi-person-circle me-1"></i> Area personale
                    </a>

                    <form method="POST" action="{{ route('esci') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link btn-sm text-secondary text-decoration-none px-2"
                                title="Esci dall'account">
                            Esci
                        </button>
                    </form>
                @else
                    <a href="{{ route('accesso') }}" class="btn btn-link btn-sm text-secondary text-decoration-none px-2">
                        Accedi
                    </a>
                    <a href="{{ route('registrazione') }}" class="btn btn-mm-blu btn-sm px-3 py-2">
                        <i class="bi bi-stars text-warning me-1"></i> Registrati &amp; prova gratis
                    </a>
                @endauth
            </div>

        </div>
    </div>
</nav>
