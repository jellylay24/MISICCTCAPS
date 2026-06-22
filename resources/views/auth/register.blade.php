@extends('layouts.guest')

@section('title', 'Sign Up - ICCT MIS')

@section('content')
<div class="w-full flex">
    {{-- Left Side: Branding with Logo --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-center items-center px-12 text-white">
        <div class="max-w-md text-center">
            <div class="mb-4">
                <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=3" alt="ICCT MIS Logo" class="w-56 h-auto mx-auto opacity-90">
            </div>

            <h1 class="text-4xl font-extrabold leading-tight mb-2">
                ICCT CAINTA<br>CAMPUS
            </h1>
            <p class="text-lg text-indigo-200 font-medium">
                MIS Resource Management System
            </p>
        </div>
    </div>

    {{-- Right Side: Sign Up Card --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 sm:p-10">
            {{-- Tab: Login / Sign Up --}}
            <div class="flex bg-gray-100 rounded-xl p-1 mb-8">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-semibold {{ request()->routeIs('login') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    Login
                </a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-semibold {{ request()->routeIs('register') ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                    Sign Up
                </a>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name --}}
                <div class="mb-4">
                    <x-input-label for="name" :value="__('Full Name')" class="text-sm font-medium text-gray-700 mb-1.5" />
                    <x-text-input id="name" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Juan Dela Cruz" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs" />
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700 mb-1.5" />
                    <x-text-input id="email" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="your@email.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                </div>



                {{-- Password --}}
                <div class="mb-4">
                    <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700 mb-1.5" />
                    <div class="relative" x-data="{ show: false }">
                        <input id="password" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               :type="show ? 'text' : 'password'"
                               name="password"
                               required autocomplete="new-password"
                               placeholder="Min. 8 characters">
                        <button type="button" @@click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-sm font-medium text-gray-700 mb-1.5" />
                    <div class="relative" x-data="{ show: false }">
                        <input id="password_confirmation" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               :type="show ? 'text' : 'password'"
                               name="password_confirmation" required autocomplete="new-password"
                               placeholder="Repeat your password">
                        <button type="button" @@click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs" />
                </div>

                {{-- Note: all registrations are Faculty by default --}}
                <div class="mb-4">
                    <div class="flex items-center gap-2 px-4 py-3 bg-gray-50 rounded-xl border border-gray-200">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-sm text-gray-600">Registering as <strong>Faculty</strong></span>
                    </div>
                    <input type="hidden" name="role" value="faculty">
                </div>


                {{-- Sign Up Button --}}
                <button type="submit" class="w-full bg-[#1a237e] text-white font-semibold py-3 px-6 rounded-xl hover:bg-[#283593] transition text-sm">
                    {{ __('Create Account') }}
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Already have an account?
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">Login</a>
            </p>

            {{-- Mobile Logo -- sa gilid ng card --}}
            <div class="flex justify-center mt-8 pt-6 border-t border-gray-100 lg:hidden">
                <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=5" alt="ICCT MIS" class="w-24 h-24 object-contain opacity-90">
            </div>
        </div>
    </div>
</div>
@endsection
