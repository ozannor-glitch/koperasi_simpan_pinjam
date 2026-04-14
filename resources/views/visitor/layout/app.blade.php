<!DOCTYPE html>
<html lang="en">

{{-- HEAD --}}
@include('visitor.partials.head')


<body>

    <!-- NAVBAR -->
    {{-- @include('visitor.components.navbar') --}}

    {{-- @yield('content') --}}
    @yield('content')

    {{-- FOOTER --}}
    @include('visitor.components.footer')

    {{-- SCRIPT --}}
    @include('visitor.partials.script')


</body>

</html>
