@extends('layouts.app')

@section('title', 'Browse Resources')
@section('header', 'Browse Resources')
@section('subheader', 'View and borrow available campus resources')

@section('content')
{{-- Overdue Alert --}}
@php
    $overdueBorrows = \App\Models\Borrow::where('user_id', auth()->id())
        ->whereIn('status', ['approved', 'borrowed'])
        ->where('due_at', '<', now())
        ->count();
@endphp
@if($overdueBorrows > 0)
<div class="flex items-center gap-3 bg-red-50 border-l-4 border-red-500 rounded-xl px-5 py-3 mb-6">
    <svg class="w-5 h-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span class="text-sm font-medium text-red-700">{{ $overdueBorrows }} overdue item(s) — please return them as soon as possible.</span>
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Available</p>
                <p class="text-3xl font-bold text-green-600 mt-1">{{ $available }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">In Use</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">{{ $inUse }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Unavailable</p>
                <p class="text-3xl font-bold text-red-500 mt-1">{{ $unavailable }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Resource Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    @forelse($resources as $r)
    @php
        $avail = $r->quantity - ($r->borrowed_qty ?? 0);
    @endphp
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        {{-- Status Pill --}}
        <div class="flex items-start justify-between">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                {{ $avail > 0 ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                {{ $avail > 0 ? 'Available' : 'Unavailable' }}
            </span>
        </div>

        {{-- Title + Quantity controls --}}
        <div class="mt-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-gray-900">{{ $r->name }}</h3>
                @if($r->description)
                <p class="text-sm text-gray-500 mt-0.5">{{ $r->description }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <span class="text-sm font-bold {{ $avail > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $avail }}</span>
                <span class="text-xs text-gray-400">/ {{ $r->quantity }}</span>
            </div>
        </div>

        {{-- Action Bar --}}
        <div class="mt-5">
            @if($avail > 0)
            <a href="{{ route('faculty.borrows.create', $r) }}"
               class="flex items-center justify-center gap-2 w-full bg-[#1a237e] text-white font-medium py-2.5 px-4 rounded-xl hover:bg-[#283593] transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Borrow This Item
            </a>
            @else
            <div class="w-full bg-gray-100 text-gray-400 font-medium py-2.5 px-4 rounded-xl text-sm text-center">
                Not Available
            </div>
            @endif
        </div>
    </div>
    @empty
    <div class="col-span-full bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
        </svg>
        <p class="text-gray-400 text-sm">No resources available yet.</p>
    </div>
    @endforelse
</div>
@endsection
