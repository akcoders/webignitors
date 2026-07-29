<nav class="navbar navbar-expand-lg fixed-top site-nav" aria-label="Main navigation">
    <div class="container">
        <div class="nav-surface d-flex align-items-center w-100">
            <a class="navbar-brand" href="{{ route('home') }}" aria-label="WebIgnitors home">
                <span class="brand-mark" aria-hidden="true"><span>WI</span></span>
                WebIgnitors
            </a>

            <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-3"></i>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('services*') ? 'active' : '' }}" href="{{ route('services') }}" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Services
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('services') }}">All services</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.web') }}">Web development</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.mobile') }}">iOS & Android apps</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.marketing') }}">Digital marketing</a></li>
                            <li><a class="dropdown-item" href="{{ route('services.ai') }}">AI integration</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('work') ? 'active' : '' }}" href="{{ route('work') }}">Work</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('process') ? 'active' : '' }}" href="{{ route('process') }}">Process</a>
                    </li>
                </ul>

                <span class="nav-status d-none d-xl-inline-flex">
                    <span></span> Available
                </span>
                <a class="btn btn-ink" href="{{ route('contact') }}">
                    Start a project <i class="bi bi-arrow-up-right"></i>
                </a>
            </div>
        </div>
    </div>
</nav>
