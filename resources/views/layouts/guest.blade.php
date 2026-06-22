<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Login - ICCT MIS')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

    {{-- PWA Support --}}
    @include('partials.pwa-meta')
    <style>
        [x-cloak] { display: none !important; }
        .right-0 { right: 0px; }
        .right-3 { right: 0.75rem; }
        .hover\:text-gray-600:hover { color: rgb(75 85 99); }
        .transition { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
        input.pr-10 { padding-right: 2.5rem; }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex bg-gradient-to-br from-[#1a237e] via-[#283593] to-[#0d47a1]">
        @yield('content')
    </div>

    <!-- Scripts -->
    <script defer src="{{ asset('assets/js/app.min.js') }}"></script>
</body>
</html>
