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

                </div>
            </div>

            {{-- FOOTER --}}
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                {{ Auth::user()->name }}
            </div>

        </nav>

    </div>
</div>
