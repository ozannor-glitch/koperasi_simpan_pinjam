<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>@yield('title')</title>
    <!--<link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" /> -->
    <link href="{{ asset('admin-template/css/styles.css') }}" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="{{ asset('admin-template/css/tambahcss.css') }}" rel="stylesheet" />
        
</head>

<body class="sb-nav-fixed">

    @include('superadmin.components.topbar')

   <div id="layoutSidenav">

    {{-- SIDEBAR --}}
    <div id="layoutSidenav_nav">
        @include('superadmin.components.sidenav')
    </div>

    {{-- CONTENT --}}
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">@yield('title')</h1>
                @yield('content')
            </div>
        </main>
    </div>

</div>
</body>

</html>
