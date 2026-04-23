<div id="layoutSidenav">
    <div id="layoutSidenav_nav">

        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">

            <div class="sb-sidenav-menu">
                <div class="nav">

                    {{-- HEADING --}}
                    <div class="sb-sidenav-menu-heading">Super Admin</div>

                    {{-- KELOLA USER --}}
                    @if(Auth::user()->role === 'super_admin')
                    <a class="nav-link {{ request()->routeIs('superadmin.user.*') ? 'active' : '' }}"
                       href="{{ route('superadmin.user.index') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        Kelola User
                    </a>
                    @endif

                    {{-- SIMPANAN --}}
                    @if(in_array(Auth::user()->role, ['super_admin','admin']))
                    <a class="nav-link {{ request()->routeIs('superadmin.saving.*') ? 'active' : '' }}"
                       href="{{ route('superadmin.saving.index') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        Simpanan
                    </a>
                    @endif

                    {{-- Pinjaman --}}
                    @if(in_array(Auth::user()->role, ['super_admin','teller']))
                     <a class="nav-link {{ request()->is('superadmin/pinjaman') ? 'active' : '' }}"
                       href="{{ url('/superadmin/pinjaman') }}">
                       <div class="sb-nav-link-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        Pinjaman
                    </a>
                    @endif

                    {{-- Penarikan --}}
                    <a class="nav-link {{ request()->is('superadmin/penarikan') ? 'active' : '' }}"
                       href="{{ url('/superadmin/penarikan') }}">
                        <div class="sb-nav-link-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        Penarikan
                    </a>

                    {{-- AKUNTANSI --}}
                    <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#menuAkuntansi">
                        <div class="sb-nav-link-icon">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        Akuntansi
                        <div class="sb-sidenav-collapse-arrow">
                            <i class="fas fa-angle-down"></i>
                        </div>
                    </a>

                    <div class="collapse {{ request()->is('superadmin/akuntansi*') ? 'show' : '' }}" id="menuAkuntansi">
                        <nav class="sb-sidenav-menu-nested nav">

                            <a class="nav-link {{ request()->is('superadmin/akuntansi/jurnal*') ? 'active' : '' }}"
                            href="{{ url('/superadmin/akuntansi/jurnal') }}">
                                Jurnal Umum
                            </a>

                            <a class="nav-link {{ request()->is('superadmin/akuntansi/buku-besar*') ? 'active' : '' }}"
                            href="{{ url('/superadmin/akuntansi/buku-besar') }}">
                                Buku Besar
                            </a>

                            <a class="nav-link {{ request()->is('superadmin/akuntansi/neraca-saldo*') ? 'active' : '' }}"
                            href="{{ url('/superadmin/akuntansi/neraca-saldo') }}">
                                Neraca Saldo
                            </a>

                            <a class="nav-link {{ request()->is('superadmin/akuntansi/laba-rugi*') ? 'active' : '' }}"
                            href="{{ url('/superadmin/akuntansi/laba-rugi') }}">
                                Laporan Rugi
                            </a>

                            <a class="nav-link {{ request()->is('superadmin/akuntansi/neraca*') ? 'active' : '' }}"
                            href="{{ url('/superadmin/akuntansi/neraca') }}">
                                Neraca Keuangan
                            </a>


                        </nav>



                </div>
                 {{-- 🔥 MENU LAPORAN --}}
<a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#menuLaporan">
    <div class="sb-nav-link-icon">
        <i class="fa-solid fa-receipt"></i>
    </div>
    Laporan
    <div class="sb-sidenav-collapse-arrow">
        <i class="fas fa-angle-down"></i>
    </div>
</a>

<div class="collapse {{ request()->is('superadmin/laporan*') ? 'show' : '' }}" id="menuLaporan">
    <nav class="sb-sidenav-menu-nested nav">

        <a class="nav-link {{ request()->is('superadmin/laporan/anggota*') ? 'active' : '' }}"
           href="{{ route('superadmin.laporan.anggota') }}">
            Laporan Anggota
        </a>

        {{--
        <a class="nav-link {{ request()->is('superadmin/laporan/pdf*') ? 'active' : '' }}"
           href="{{ route('superadmin.laporan.pdf') }}">
            PDF
        </a>

        <a class="nav-link {{ request()->is('superadmin/laporan/slip*') ? 'active' : '' }}"
           href="{{ route('superadmin.laporan.slip') }}">
            Slip Angsuran
        </a>
        --}}

    </nav>
</div>


            {{-- FOOTER --}}
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                {{ Auth::user()->name }}
            </div>
</nav>
    </div>
</div>
