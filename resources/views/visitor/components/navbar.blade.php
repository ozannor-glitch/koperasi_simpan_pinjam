<nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light site-navbar-target"
    id="ftco-navbar">
    <div class="container">

        <a class="navbar-brand" href="/">
            Koperasi <span>Simpan Pinjam</span>
        </a>

        <button class="navbar-toggler js-fh5co-nav-toggle fh5co-nav-toggle" type="button" data-toggle="collapse"
            data-target="#ftco-nav">

            <span class="oi oi-menu"></span> Menu
        </button>

        <div class="collapse navbar-collapse" id="ftco-nav">

            <ul class="navbar-nav nav ml-auto">

                <li class="nav-item">
                    <a href="#home-section" class="nav-link">
                        <span>Home</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#about-section" class="nav-link">
                        <span>Tentang Kami</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#services-section" class="nav-link">
                        <span>Simpan Pinjam</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#faq-section" class="nav-link">
                        <span>FAQ</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#testimony-section" class="nav-link">
                        <span>Testimoni</span>
                    </a>
                </li>

                @guest
                    <li class="nav-item ml-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                            Login
                        </a>
                    </li>

                    <li class="nav-item ml-2">
                        <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                            Daftar
                        </a>
                    </li>
                @endguest

                @auth
                    <li class="nav-item ml-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-success btn-sm">
                            Dashboard
                        </a>
                    </li>

                    <li class="nav-item ml-2">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-danger btn-sm">
                                Logout
                            </button>
                        </form>
                    </li>
                @endauth

            </ul>
        </div>
    </div>
</nav>
