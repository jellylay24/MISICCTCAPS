<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ICCT Cainta Campus - MIS Resource Management System</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <script defer src="{{ asset('assets/js/app.min.js') }}"></script>
    <style>
        body { font-family: 'Figtree', system-ui, -apple-system, sans-serif; }
        .brand-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-[#1a237e] via-[#283593] to-[#0d47a1] min-h-screen flex items-center justify-center relative overflow-hidden">
    <div class="flex w-full max-w-5xl mx-auto px-6 relative z-10">
        {{-- Left: Branding with Logo --}}
        <div class="hidden lg:flex lg:w-1/2 flex-col justify-center items-center pr-8">
            <div class="max-w-md text-center">
                <div class="mb-4">
                    <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=3" alt="ICCT MIS Logo" class="w-56 h-auto mx-auto opacity-90">
                </div>
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-2">ICCT CAINTA<br>CAMPUS</h1>
                <p class="text-lg text-indigo-200 font-medium">MIS Resource Management System</p>
            </div>
        </div>

        {{-- Right: Card --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center">
            <div class="w-full max-w-md bg-white/10 backdrop-blur-sm rounded-3xl p-10 text-center">
                {{-- Mobile logo --}}
                <div class="lg:hidden flex justify-center mb-6">
                    <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=3" alt="ICCT MIS Logo" class="w-24 h-auto opacity-90">
                </div>
                <h2 class="text-2xl font-bold text-white mb-3 lg:hidden">ICCT CAINTA CAMPUS</h2>
                <p class="text-white/80 mb-8 lg:mb-10">Welcome to the ICCT MIS platform. Sign in to manage resources and borrow equipment.</p>

                <div class="space-y-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="block w-full bg-white text-indigo-900 font-semibold py-3 px-6 rounded-xl hover:bg-indigo-50 transition shadow-lg">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-white text-[#1a237e] font-semibold py-3 px-6 rounded-xl hover:bg-indigo-50 transition shadow-lg">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="block w-full bg-white/20 text-white font-semibold py-3 px-6 rounded-xl hover:bg-white/30 transition border border-white/30">
                            Sign Up
                        </a>
                    @endauth
                </div>

                <footer class="mt-10 text-indigo-300/60 text-xs">
                    &copy; {{ date('Y') }} ICCT Cainta Campus. All rights reserved.
                </footer>
            </div>
        </div>
    </div>
</body>
</html>
