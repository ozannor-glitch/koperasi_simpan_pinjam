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
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">Primary Card</div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Details</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

            </div>
        </div>
