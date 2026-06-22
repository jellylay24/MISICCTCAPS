@extends('layouts.app')

@section('title', 'Create Account')
@section('header', 'Create Account')
@section('subheader', 'Add a faculty or admin account')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <x-input-label for="name" :value="__('Full Name')" class="text-sm font-medium text-gray-700 mb-1.5" />
                <x-text-input id="name" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" :value="old('name')" required autofocus placeholder="Juan Dela Cruz" />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs" />
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <x-input-label for="email" :value="__('Email')" class="text-sm font-medium text-gray-700 mb-1.5" />
                <x-text-input id="email" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required placeholder="email@icct.edu.ph" />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5 text-xs" />
            </div>

            {{-- Department --}}
            <div class="mb-4">
                <x-input-label for="department" :value="__('Department')" class="text-sm font-medium text-gray-700 mb-1.5" />
                <x-text-input id="department" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="department" :value="old('department')" placeholder="e.g. Computer Studies" />
                <x-input-error :messages="$errors->get('department')" class="mt-1.5 text-xs" />
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <x-input-label for="password" :value="__('Password')" class="text-sm font-medium text-gray-700 mb-1.5" />
                <div class="relative" x-data="{ show: false }">
                    <input id="password" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3 pr-10 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                           :type="show ? 'text' : 'password'"
                           name="password" required autocomplete="new-password"
                           placeholder="Min. 8 characters">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition">
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
                           placeholder="Repeat password">
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs" />
            </div>

            {{-- Role --}}
            <div class="mb-4" x-data="{ role: 'faculty' }">
                <x-input-label value="{{ __('Account Type') }}" class="text-sm font-medium text-gray-700 mb-1.5" />
                <div class="flex bg-gray-100 rounded-xl p-1">
                    <button type="button" @click="role = 'faculty'"
                        class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-semibold transition"
                        :class="role === 'faculty' ? 'bg-white text-[#1a237e] shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                        Faculty
                    </button>
                    <button type="button" @click="role = 'admin'"
                        class="flex-1 text-center py-2 px-4 rounded-lg text-sm font-semibold transition"
                        :class="role === 'admin' ? 'bg-white text-[#1a237e] shadow-sm' : 'text-gray-500 hover:text-gray-700'">
                        Admin
                    </button>
                </div>
                <input type="hidden" name="role" :value="role">
                <x-input-error :messages="$errors->get('role')" class="mt-1.5 text-xs" />
            </div>

            <div class="flex items-center gap-3 mt-6">
                <button type="submit" class="flex-1 bg-[#1a237e] text-white font-semibold py-3 px-6 rounded-xl hover:bg-[#283593] transition text-sm">
                    Create Account
                </button>
                <a href="{{ route('admin.users.index') }}" class="flex-1 text-center py-3 px-6 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
