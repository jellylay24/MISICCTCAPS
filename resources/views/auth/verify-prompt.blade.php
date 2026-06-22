@extends('layouts.guest')

@section('title', 'Verify Email - ICCT MIS')

@section('content')
<div class="w-full flex">
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-center items-center px-12 text-white">
        <div class="max-w-md">
            <div class="mb-4">
                <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=3" alt="ICCT MIS Logo" class="w-56 h-auto mx-auto opacity-90">
            </div>
            <h1 class="text-4xl font-extrabold leading-tight mb-2">ICCT CAINTA<br>CAMPUS</h1>
            <p class="text-lg text-indigo-200 font-medium">MIS Resource Management System</p>
        </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
        <div class="w-full max-w-md">
            <div class="bg-white rounded-3xl shadow-2xl p-8 sm:p-10 text-center">
                {{-- Mail icon --}}
                <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-5">
                    <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-900 mb-2">Check Your Email</h2>
                <p class="text-gray-500 mb-2 text-sm">
                    We've sent a verification link to
                </p>
                <p class="font-semibold text-gray-800 text-base mb-6">
                    {{ $email }}
                </p>

                <div class="bg-indigo-50 rounded-xl p-4 mb-6 text-left text-sm text-gray-600">
                    <p class="font-medium text-indigo-800 mb-1">📩 Next Steps:</p>
                    <ol class="list-decimal list-inside space-y-1">
                        <li>Open your email inbox</li>
                        <li>Click the <strong>Verify Email Address</strong> button</li>
                        <li>You'll be redirected back to login</li>
                    </ol>
                </div>

                <a href="{{ route('login') }}" class="w-full block bg-[#1a237e] text-white font-semibold py-3 px-6 rounded-xl hover:bg-[#283593] transition text-sm text-center">
                    Go to Login
                </a>

                <p class="text-xs text-gray-400 mt-4">
                    Didn't receive the email? Check your spam folder, or
                    <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">try again</a>.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
