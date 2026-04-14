<div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            @if(Auth::user()->role == 'super_admin')
                                <li>Kelola User</li>
                            @endif

                            @if(in_array(Auth::user()->role, ['super_admin','admin']))
                                <li>Simpanan</li>
                            @endif

                            @if(in_array(Auth::user()->role, ['super_admin','teller']))
                                <li>Transaksi</li>
                            @endif

                              <li class="nav-item {{ request()->routeIs('user.*') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('user.index') }}">
                                    <i class="fas fa-user"></i>
                                    <span>User</span>
                                </a>
                              </li>

                            <li class="nav-item {{request()->routeIs('admin/statistic/*') ? 'active' : ''}}">
                                <a class="nav-link" href="/superadmin/statistic">
                                    <i class="fa-solid fa-chart-line"></i>
                                 <span>Statistic</span></a>
                            </li>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ Auth::user()->name }}
                    </div>
                </nav>
            </div>
        </div>
