@extends('layouts.guest')

@section('title', 'Reset Password - ICCT MIS')

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
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Set New Password</h2>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="mb-4">
                    <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700 mb-1.5" />
                    <x-text-input id="email" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
                </div>

                <div class="mb-4">
                    <x-input-label for="password" :value="__('New Password')" class="text-sm font-medium text-gray-700 mb-1.5" />
                    <div class="relative" x-data="{ show: false }">
                        <input id="password" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                               :type="show ? 'text' : 'password'"
                               name="password" required autocomplete="new-password"
                               placeholder="Min. 8 characters">
                        <button type="button" @@click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
                </div>

                <div class="mb-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm New Password')" class="text-sm font-medium text-gray-700 mb-1.5" />
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

                <button type="submit" class="w-full bg-[#1a237e] text-white font-semibold py-3 px-6 rounded-xl hover:bg-[#283593] transition text-sm">
                    {{ __('Reset Password') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
