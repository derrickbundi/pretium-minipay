<head>
    <meta name="theme-color" content="#15645e" />
    <meta charset="utf-8" />
    <title>{{config('app.name')}} x Minipay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="PRT" name="description" />
    <meta content="derrick.bundi27@gmail.con" name="author" />
    <!-- App favicon -->
    {{-- <link rel="shortcut icon" href="{{ asset('logo/teal-64*64.png') }}"> --}}
    <!-- Bootstrap Css -->
    {{-- <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" /> --}}
    <link href="{{ mix('assets/css/minipay.css') }}" rel="stylesheet">
    @stack('css')
</head>