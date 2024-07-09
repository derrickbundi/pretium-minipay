<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
@include('layouts.minipay.head')

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    @vite('resources/js/app.js')

    <div class="layout-wrapper landing">

    @yield('body')

    @include('includes.minipay_telegram_button')

    {{-- @include('layouts.minipay.footer') --}}
    
    </div>
    @include('layouts.minipay.scripts')
</body>
</html>