@extends('layouts.guest')

@section('title', 'Forgot Password - ICCT MIS')

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
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Reset Password</h2>
            <p class="text-sm text-gray-500 mb-6">{{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}</p>

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700 mb-1.5" />
                    <x-text-input id="email" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autofocus placeholder="your@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                </div>

                <button type="submit" class="w-full bg-[#1a237e] text-white font-semibold py-3 px-6 rounded-xl hover:bg-[#283593] transition text-sm">
                    {{ __('Send Reset Link') }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Back to Login</a>
            </p>
        </div>
    </div>
</div>
@endsection
