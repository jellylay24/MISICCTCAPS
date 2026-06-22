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
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 sm:p-10">
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Verify Your Email</h2>
                <p class="text-sm text-gray-500 mb-6">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 text-center">
                    {{ __('A new verification link has been sent to your email.') }}
                </div>
            @endif

            <div class="flex items-center justify-between gap-3">
                <form method="POST" action="{{ route('verification.send') }}" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full bg-indigo-600 text-white font-semibold py-3 px-4 rounded-xl hover:bg-indigo-500 transition text-sm">
                        {{ __('Resend Verification') }}
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-gray-100 text-gray-600 font-semibold py-3 px-4 rounded-xl hover:bg-gray-200 transition text-sm whitespace-nowrap">
                        {{ __('Log Out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
