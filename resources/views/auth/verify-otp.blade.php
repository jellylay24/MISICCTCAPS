@extends('layouts.guest')

@section('title', 'Verify OTP - ICCT MIS')

@section('content')
<div class="w-full flex">
    {{-- Left Side: Branding (hidden on mobile) --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-center items-center px-12 text-white">
        <div class="max-w-md">
            <div class="mb-4">
                <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=3" alt="ICCT MIS Logo" class="w-56 h-auto mx-auto opacity-90">
            </div>
            <h1 class="text-4xl font-extrabold leading-tight mb-2">ICCT CAINTA<br>CAMPUS</h1>
            <p class="text-lg text-indigo-200 font-medium">MIS Resource Management System</p>
        </div>
    </div>

    {{-- Right Side: OTP Card --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-white rounded-3xl shadow-2xl p-8 sm:p-10">
            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-[#1a237e]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">Phone Verification</h2>
                <p class="text-sm text-gray-500 mt-2">Enter the 6-digit code sent to <strong>{{ $user->phone }}</strong></p>
            </div>

            {{-- Status messages --}}
            @if (session('status') === 'otp-sent')
                <div class="bg-green-50 border border-green-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-2 text-sm text-green-700">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ session('message') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border border-red-200 rounded-xl px-4 py-3 mb-6 flex items-center gap-2 text-sm text-red-700">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- OTP Form --}}
            <form method="POST" action="{{ route('otp.verify.submit') }}">
                @csrf

                <div class="mb-6">
                    <x-input-label for="otp" :value="__('One-Time Passcode')" class="text-sm font-medium text-gray-700 mb-2" />
                    <x-text-input id="otp" class="block w-full rounded-xl border-gray-300 bg-gray-50 px-4 py-3.5 text-center text-2xl font-bold tracking-[0.5em] focus:border-indigo-500 focus:ring-indigo-500"
                        type="text"
                        name="otp"
                        required
                        maxlength="6"
                        autocomplete="one-time-code"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        placeholder="000000"
                        oninput="this.value=this.value.replace(/[^0-9]/g,'')" />
                    <x-input-error :messages="$errors->get('otp')" class="mt-1 text-xs" />
                </div>

                <button type="submit" class="w-full bg-[#1a237e] text-white font-semibold py-3 px-6 rounded-xl hover:bg-[#283593] transition text-sm">
                    {{ __('Verify & Continue') }}
                </button>
            </form>

            {{-- Resend --}}
            <div class="text-center mt-6">
                <p class="text-sm text-gray-500 mb-2">Didn't receive the code?</p>
                <form method="POST" action="{{ route('otp.resend') }}" class="inline" id="resendForm">
                    @csrf
                    <button type="submit" id="resendBtn" class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm">
                        Resend OTP
                    </button>
                </form>
                <p id="timer" class="text-xs text-gray-400 mt-1"></p>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus OTP input
document.addEventListener('DOMContentLoaded', function() {
    const otpInput = document.getElementById('otp');
    if (otpInput) otpInput.focus();

    // Resend cooldown timer (60 seconds)
    let cooldown = 0;
    const btn = document.getElementById('resendBtn');
    const timer = document.getElementById('timer');
    const form = document.getElementById('resendForm');

    if (btn && timer) {
        form.addEventListener('submit', function(e) {
            if (cooldown > 0) {
                e.preventDefault();
                return;
            }
            cooldown = 60;
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');

            const interval = setInterval(() => {
                cooldown--;
                timer.textContent = `Resend available in ${cooldown}s`;
                if (cooldown <= 0) {
                    clearInterval(interval);
                    btn.disabled = false;
                    btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    timer.textContent = '';
                }
            }, 1000);
        });
    }
});
</script>
@endsection
