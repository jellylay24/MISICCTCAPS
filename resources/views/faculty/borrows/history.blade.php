@extends('layouts.app')

@section('title', 'My Borrow History')
@section('header', 'My Borrow History')
@section('subheader', 'Track all your borrowing requests and returns')

@section('content')
{{-- Overdue Alert Banner --}}
@php
    $overdueCount = $borrows->filter(fn($b) => $b->localDueAt() && $b->localDueAt()->isPast() && in_array($b->status, ['approved','borrowed']))->count();
    $overdueItems = $borrows->filter(fn($b) => $b->localDueAt() && $b->localDueAt()->isPast() && in_array($b->status, ['approved','borrowed']));
@endphp
@if($overdueCount > 0)
<div class="flex items-start gap-3 bg-red-50 border-l-4 border-red-500 rounded-xl px-5 py-4 mb-6">
    <svg class="w-6 h-6 shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="flex-1">
        <p class="text-sm font-bold text-red-800">You have {{ $overdueCount }} overdue item{{ $overdueCount > 1 ? 's' : '' }}.</p>
        <p class="text-xs text-red-600 mt-0.5">Please return {{ $overdueCount > 1 ? 'them' : 'it' }} as soon as possible to avoid penalties.</p>
        @if($overdueItems->count())
        <div class="mt-2 space-y-0.5">
            @foreach($overdueItems as $item)
            <div class="text-xs text-red-700 font-medium flex items-center gap-1">
                <span>• {{ $item->quantity }}x {{ $item->resource->name }}</span>
                @if($item->localDueAt())
                <span class="text-red-400">(due {{ $item->localDueAt()->format('M d, Y h:i A') }})</span>
                @endif
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

{{-- Approved Alert Banner --}}
@php
    $approvedCount = $borrows->where('status', 'approved')
        ->filter(fn($b) => is_null($b->due_at) || $b->due_at->isFuture())
        ->count();
    $approvedItems = $borrows->where('status', 'approved')
        ->filter(fn($b) => is_null($b->due_at) || $b->due_at->isFuture());
    $dismissSeconds = 30;
@endphp
@if($approvedCount > 0)
<div id="approved-alert" class="flex items-start gap-3 bg-green-50 border-l-4 border-green-500 rounded-xl px-5 py-4 mb-6 transition-[opacity,transform] duration-700 ease-out" style="opacity:1;transform:translateY(0);">
    <svg class="w-6 h-6 shrink-0 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="flex-1">
        <p class="text-sm font-bold text-green-800">{{ $approvedCount }} item{{ $approvedCount > 1 ? 's' : '' }} approved — your item is on the way</p>
        <p class="text-xs text-green-600 mt-0.5">This will auto-dismiss in {{ $dismissSeconds }} seconds.</p>
        @if($approvedItems->count())
        <div class="mt-2 space-y-0.5">
            @foreach($approvedItems as $item)
            <div class="text-xs text-green-700 font-medium flex items-center gap-1">
                <span>• {{ $item->quantity }}x {{ $item->resource->name }}</span>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endif

<script>
(function() {
    var el = document.getElementById('approved-alert');
    var ms = {{ $dismissSeconds }} * 1000;
    var approveUrl = '{{ route('faculty.history') }}';
    var lastShownCount = {{ $approvedCount }};

    function createBanner(count) {
        var container = document.querySelector('[class*="mb-6"]');
        if (container) container = container.parentElement;
        else container = document.querySelector('main') || document.querySelector('.container') || document.body;
        var div = document.createElement('div');
        div.id = 'approved-alert';
        div.className = 'flex items-start gap-3 bg-green-50 border-l-4 border-green-500 rounded-xl px-5 py-4 mb-6 transition-[opacity,transform] duration-700 ease-out';
        div.style.opacity = '0';
        div.style.transform = 'translateY(-10px)';
        div.innerHTML = '<svg class="w-6 h-6 shrink-0 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div class="flex-1"><p class="text-sm font-bold text-green-800">' + count + ' item' + (count > 1 ? 's' : '') + ' approved — your item is on the way</p><p class="text-xs text-green-600 mt-0.5">This will auto-dismiss in ' + (ms/1000) + ' seconds.</p></div><a href="' + approveUrl + '" class="shrink-0 px-4 py-2 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition whitespace-nowrap">View Items</a>';
        container.insertBefore(div, container.firstChild);
        el = div;
        setTimeout(hideBanner, ms);
        requestAnimationFrame(function() {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
    }

    function hideBanner() {
        if (!el) return;
        localStorage.setItem('approved_banner_shown_v3', lastShownCount);
        el.style.opacity = '0';
        el.style.transform = 'translateY(-10px)';
        setTimeout(function() {
            if (el) el.style.display = 'none';
        }, 700);
    }

    function showBanner(count) {
        if (count <= lastShownCount) return;
        if (count <= parseInt(localStorage.getItem('approved_banner_shown_v3') || 0)) return;
        lastShownCount = count;
        if (!el) {
            createBanner(count);
            return;
        }
        var nameEl = el.querySelector('.font-bold.text-green-800');
        if (nameEl) {
            nameEl.textContent = count + ' item' + (count > 1 ? 's' : '') + ' approved — your item is on the way';
        }
        var subtitle = el.querySelector('.text-xs.text-green-600');
        if (subtitle) subtitle.textContent = 'This will auto-dismiss in ' + (ms/1000) + ' seconds.';
        el.style.display = 'flex';
        requestAnimationFrame(function() {
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        });
        setTimeout(hideBanner, ms);
    }

    if (el) {
        var prevShown = parseInt(localStorage.getItem('approved_banner_shown_v3') || 0);
        if ({{ $approvedCount }} > 0) {
            if (prevShown >= {{ $approvedCount }}) {
                el.style.display = 'none';
            } else {
                setTimeout(hideBanner, ms);
            }
        }
    }

    // Poll for new approved items every 3 seconds — feels instant
    setInterval(function() {
        fetch('/faculty/approved-check')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.count > 0) {
                    showBanner(data.count);
                }
            })
            .catch(function() {});
    }, 3000);
})();
</script>

{{-- Summary Stats --}}
@php
    $total = $borrows->count();
    $cntPending = $borrows->where('status', 'pending')->count();
    $cntActive = $borrows->whereIn('status', ['approved', 'borrowed'])->count();
    $cntReturned = $borrows->where('status', 'returned')->count();
    $cntRejected = $borrows->where('status', 'rejected')->count();
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 mb-6 lg:mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Total Requests</p>
                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ $total }}</p>
            </div>
            <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                <p class="text-2xl lg:text-3xl font-bold {{ $cntPending > 0 ? 'text-amber-500' : 'text-gray-900' }} mt-1">{{ $cntPending }}</p>
            </div>
            <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl {{ $cntPending > 0 ? 'bg-amber-50' : 'bg-gray-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 {{ $cntPending > 0 ? 'text-amber-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Active</p>
                <p class="text-2xl lg:text-3xl font-bold {{ $cntActive > 0 ? 'text-blue-600' : 'text-gray-900' }} mt-1">{{ $cntActive }}</p>
            </div>
            <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl {{ $cntActive > 0 ? 'bg-blue-50' : 'bg-gray-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 {{ $cntActive > 0 ? 'text-blue-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Returned</p>
                <p class="text-2xl lg:text-3xl font-bold {{ $cntReturned > 0 ? 'text-green-600' : 'text-gray-900' }} mt-1">{{ $cntReturned }}</p>
            </div>
            <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-xl {{ $cntReturned > 0 ? 'bg-green-50' : 'bg-gray-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 {{ $cntReturned > 0 ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- History Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Header with search/filter placeholder --}}
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="text-sm font-semibold text-gray-700">Borrow Records</span>
            <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $total }} items</span>


            @if($cntPending > 0)
            <span class="text-xs text-amber-600 bg-amber-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                {{ $cntPending }} pending
            </span>
            @endif

            @php $overdueCount = $borrows->filter(fn($b) => $b->localDueAt() && $b->localDueAt()->isPast() && in_array($b->status, ['approved','borrowed']))->count(); @endphp
            @if($overdueCount > 0)
            <span class="text-xs text-red-600 bg-red-50 px-2 py-0.5 rounded-full flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                {{ $overdueCount }} overdue
            </span>
            @endif
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="px-2 flex gap-3 mb-3 flex-wrap" id="filter-tabs">
        <button onclick="filterHistory('all')" class="filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-[#1a237e] text-white shadow-sm" data-filter="all">📋 All</button>
        <button onclick="filterHistory('pending')" class="filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="pending">🕐 Pending</button>
        <button onclick="filterHistory('approved')" class="filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="approved">✅ Approved</button>
        <button onclick="filterHistory('borrowed')" class="filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="borrowed">📌 Borrowed</button>
        <button onclick="filterHistory('returned')" class="filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="returned">📦 Returned</button>
        <button onclick="filterHistory('rejected')" class="filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-gray-100 text-gray-600 hover:bg-gray-200" data-filter="rejected">❌ Rejected</button>
    </div>

    {{-- Desktop Table --}}
    <div class="hidden md:block">
    <table class="w-full">
        <thead>
            <tr class="text-left text-xs text-gray-400 uppercase tracking-widest border-b border-gray-100 bg-gray-50/50">
                <th class="px-6 py-3.5 font-bold">Item</th>
                <th class="px-6 py-3.5 font-bold">Qty</th>
                <th class="px-6 py-3.5 font-bold">Status</th>
                <th class="px-6 py-3.5 font-bold">Requested</th>
                <th class="px-6 py-3.5 font-bold">Borrowed</th>
                <th class="px-6 py-3.5 font-bold">Due</th>
                <th class="px-6 py-3.5 font-bold">Time Left</th>
                <th class="px-6 py-3.5 font-bold">Returned</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $b)
            @php
                $localDue = $b->localDueAt();
                $isOverdue = $localDue && $localDue->isPast() && in_array($b->status, ['approved','borrowed']);
                $statusColors = match($b->status) {
                    'pending' => ['text' => 'text-amber-700', 'bg' => 'bg-amber-50', 'border' => 'border-amber-200', 'dot' => 'bg-amber-400'],
                    'approved' => ['text' => 'text-blue-700', 'bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'dot' => 'bg-blue-500'],
                    'borrowed' => ['text' => 'text-indigo-700', 'bg' => 'bg-indigo-50', 'border' => 'border-indigo-200', 'dot' => 'bg-indigo-500'],
                    'returned' => ['text' => 'text-emerald-700', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500'],
                    'rejected' => ['text' => 'text-red-700', 'bg' => 'bg-red-50', 'border' => 'border-red-200', 'dot' => 'bg-red-400'],
                };
                $rowOverdue = $isOverdue ? 'bg-red-50/30' : '';
            @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50/80 transition {{ $rowOverdue }}" data-status="{{ $b->status }}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-sm shadow-sm shrink-0">
                            @switch($b->status)
                                @case('pending') 📋 @case('approved') ✅ @case('borrowed') 📌 @case('returned') 📦 @default ❌
                            @endswitch
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">{{ $b->resource->name }}</div>
                            @if($b->resource->description)
                            <div class="text-[11px] text-gray-400 truncate max-w-[180px]">{{ $b->resource->description }}</div>
                            @endif
                            @if($b->room)
                            <div class="text-[11px] text-blue-600 font-medium mt-0.5">📍 {{ $b->room }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-xs font-extrabold text-gray-600 shadow-sm">{{ $b->quantity }}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold border shadow-sm {{ $statusColors['bg'] }} {{ $statusColors['text'] }} {{ $statusColors['border'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusColors['dot'] }} {{ $b->status === 'pending' ? 'animate-pulse' : '' }}"></span>
                        {{ ucfirst($b->status) }}
                        @if($b->status === 'pending')
                        <span class="text-[10px] opacity-70">· Awaiting</span>
                        @endif
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                    <div class="font-medium text-gray-700">{{ $b->created_at->setTimezone('Asia/Manila')->format('M d') }}</div>
                    <div class="text-[11px] text-gray-400">{{ $b->created_at->setTimezone('Asia/Manila')->format('h:i A') }}</div>
                </td>
                <td class="px-6 py-4 text-sm whitespace-nowrap">
                    @if($b->localBorrowedAt())
                    <div class="font-medium text-gray-700">{{ $b->localBorrowedAt()->format('M d') }}</div>
                    <div class="text-[11px] text-gray-400">{{ $b->localBorrowedAt()->format('h:i A') }}</div>
                    @else
                    <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm whitespace-nowrap">
                    @if($localDue)
                    <div class="font-semibold {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">{{ $localDue->format('M d') }}</div>
                    <div class="text-[11px] {{ $isOverdue ? 'text-red-400' : 'text-gray-400' }}">{{ $localDue->format('h:i A') }}</div>
                    @else
                    <span class="text-gray-300">—</span>
                    @endif
                </td>
                <td class="px-6 py-4 due-cell whitespace-nowrap">
                    @if($localDue && in_array($b->status, ['approved', 'borrowed']))
                        @if($isOverdue)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs font-extrabold bg-red-100 text-red-700 shadow-sm border border-red-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                            <span class="due-countdown" data-due-ts="{{ $localDue->timestamp }}">OVERDUE</span>
                        </span>
                        @else
                        <span class="due-countdown inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold shadow-sm border
                            @if($localDue->diffInHours(now()) < 1) bg-rose-50 text-rose-700 border-rose-200
                            @elseif($localDue->diffInHours(now()) < 3) bg-amber-50 text-amber-700 border-amber-200
                            @else bg-gray-50 text-gray-700 border-gray-200 @endif"
                            data-due-ts="{{ $localDue->timestamp }}">--</span>
                        @endif
                    @else
                    <span class="text-gray-300 text-xs">—</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-sm whitespace-nowrap">
                    @if($b->localReturnedAt())
                    <div class="font-medium text-emerald-700">{{ $b->localReturnedAt()->format('M d') }}</div>
                    <div class="text-[11px] text-emerald-400">{{ $b->localReturnedAt()->format('h:i A') }}</div>
                    @else
                    <span class="text-gray-300">—</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="px-6 py-20 text-center">
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mx-auto mb-4 shadow-sm">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <p class="text-gray-500 text-base font-semibold mb-1">No borrow history yet</p>
                <p class="text-gray-400 text-sm">Start by browsing resources and borrowing what you need.</p>
                <a href="{{ route('faculty.resources.index') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition shadow-lg shadow-indigo-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Browse Resources
                </a>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    </div>

    {{-- Phone Cards --}}
    <div class="md:hidden space-y-3 p-3">
        @forelse($borrows as $b)
        @php
            $localDue = $b->localDueAt();
            $dueTs = $localDue?->timestamp ?: '';
            $isOverdue = $localDue && $localDue->isPast() && in_array($b->status, ['approved', 'borrowed']);
            $glowColor = match($b->status) {
                'pending' => 'shadow-amber-200/50 border-amber-200',
                'approved' => 'shadow-blue-200/50 border-blue-200',
                'borrowed' => 'shadow-indigo-200/50 border-indigo-200',
                'returned' => 'shadow-emerald-200/50 border-emerald-200',
                'rejected' => 'shadow-red-200/50 border-red-200',
            };
            $statusEmoji = match($b->status) {
                'pending' => '🕐', 'approved' => '✅', 'borrowed' => '📌', 'returned' => '📦', 'rejected' => '❌',
            };
        @endphp
        <div class="bg-white rounded-2xl shadow-md {{ $glowColor }} border overflow-hidden" data-status="{{ $b->status }}">
            {{-- Header bar --}}
            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-50
                @switch($b->status)
                    @case('pending') bg-gradient-to-r from-amber-50 to-white @break
                    @case('approved') bg-gradient-to-r from-blue-50 to-white @break
                    @case('borrowed') bg-gradient-to-r from-indigo-50 to-white @break
                    @case('returned') bg-gradient-to-r from-emerald-50 to-white @break
                    @default bg-gradient-to-r from-red-50 to-white @endswitch">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-sm">{{ $statusEmoji }}</div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm">{{ $b->resource->name }}</div>
                        <div class="text-[10px] text-gray-400">Qty: {{ $b->quantity }}</div>
                        @if($b->room)
                        <div class="text-[10px] text-blue-600 font-medium mt-0.5">📍 {{ $b->room }}</div>
                        @endif
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border shadow-sm
                    @switch($b->status)
                        @case('pending') bg-amber-50 text-amber-700 border-amber-200 @break
                        @case('approved') bg-blue-50 text-blue-700 border-blue-200 @break
                        @case('borrowed') bg-indigo-50 text-indigo-700 border-indigo-200 @break
                        @case('returned') bg-emerald-50 text-emerald-700 border-emerald-200 @break
                        @default bg-red-50 text-red-700 border-red-200 @endswitch">
                    <span class="w-1 h-1 rounded-full {{ $b->status === 'pending' ? 'bg-amber-400 animate-pulse' : match($b->status) {'approved' => 'bg-blue-500', 'borrowed' => 'bg-indigo-500', 'returned' => 'bg-emerald-500', default => 'bg-red-400'} }}"></span>
                    {{ ucfirst($b->status) }}
                </span>
            </div>

            {{-- Body --}}
            <div class="px-4 py-3 space-y-2.5">
                <div class="grid grid-cols-2 gap-y-2.5 text-xs">
                    <div>
                        <span class="text-gray-400 flex items-center gap-1">📅 Requested</span>
                        <p class="text-gray-800 font-semibold mt-0.5">{{ $b->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 flex items-center gap-1">✅ Borrowed</span>
                        <p class="text-gray-800 font-semibold mt-0.5">{{ $b->localBorrowedAt()?->format('M d, Y h:i A') ?: '—' }}</p>
                    </div>
                    <div>
                        <span class="text-gray-400 flex items-center gap-1">⏰ Due</span>
                        <p class="font-semibold mt-0.5 {{ $isOverdue ? 'text-red-600' : 'text-gray-800' }}">
                            @if($localDue) {{ $localDue->format('M d, Y h:i A') }} @else — @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-gray-400 flex items-center gap-1">📦 Returned</span>
                        <p class="text-gray-800 font-semibold mt-0.5">{{ $b->localReturnedAt()?->format('M d, Y h:i A') ?: '—' }}</p>
                    </div>
                </div>

                {{-- Time remaining badge --}}
                @if($localDue && in_array($b->status, ['approved', 'borrowed']))
                <div class="pt-1.5 border-t border-gray-50">
                    <span class="text-[11px] text-gray-400 font-medium">⏳ Time Remaining</span>
                    <div class="mt-1">
                        @if($isOverdue)
                        <span class="due-countdown-mobile inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-extrabold bg-red-100 text-red-700 shadow-sm border border-red-200">
                            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                            <span data-due-ts="{{ $dueTs }}">OVERDUE</span>
                        </span>
                        @else
                        <span class="due-countdown-mobile inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm border
                            @if($localDue->diffInHours(now()) < 1) bg-rose-50 text-rose-700 border-rose-200
                            @elseif($localDue->diffInHours(now()) < 3) bg-amber-50 text-amber-700 border-amber-200
                            @else bg-gray-50 text-gray-700 border-gray-200 @endif"
                            data-due-ts="{{ $dueTs }}">--</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
            <p class="text-gray-400 text-sm font-medium">No borrow history yet.</p>
        </div>
        @endforelse
    </div>
</div>

<script>
function updateDueCountdowns() {
    document.querySelectorAll('.due-countdown, .due-countdown-mobile').forEach(function(el) {
        var ts = parseInt(el.dataset.dueTs);
        if (!ts) return;
        var now = Math.floor(Date.now() / 1000);
        var diff = ts - now;
        if (diff <= 0) {
            el.className = (el.className.includes('due-countdown-mobile') ? 'due-countdown-mobile ' : 'due-countdown ') + 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-extrabold bg-red-100 text-red-700 shadow-sm border border-red-200';
            el.innerHTML = '<span class=\"w-2 h-2 rounded-full bg-red-500 animate-pulse\"></span> OVERDUE';
            return;
        }
        var days = Math.floor(diff / 86400);
        var hours = Math.floor((diff % 86400) / 3600);
        var mins = Math.floor((diff % 3600) / 60);
        var secs = diff % 60;
        var text;
        if (days > 0) {
            text = days + 'd ' + hours.toString().padStart(2,'0') + 'h ' + mins.toString().padStart(2,'0') + 'm ' + secs.toString().padStart(2,'0') + 's';
        } else {
            text = hours.toString().padStart(2,'0') + 'h ' + mins.toString().padStart(2,'0') + 'm ' + secs.toString().padStart(2,'0') + 's';
        }
        el.textContent = text;
        var className;
        if (diff < 3600) {
            className = 'bg-rose-50 text-rose-700 border-rose-200';
        } else if (diff < 86400) {
            className = 'bg-amber-50 text-amber-700 border-amber-200';
        } else {
            className = 'bg-gray-50 text-gray-700 border-gray-200';
        }
        el.className = (el.className.includes('due-countdown-mobile') ? 'due-countdown-mobile ' : 'due-countdown ') + 'inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm border ' + className;
    });
}
updateDueCountdowns();
setInterval(updateDueCountdowns, 1000);

setInterval(function() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newTable = doc.querySelector('.bg-white.rounded-2xl.overflow-hidden');
            var oldTable = document.querySelector('.bg-white.rounded-2xl.overflow-hidden');
            if (newTable && oldTable) {
                oldTable.innerHTML = newTable.innerHTML;
                updateDueCountdowns();
            }
        })
        .catch(function() {});
}, 30000);
</script>

<script>
function filterHistory(status) {
    // Update button styles
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
        if (btn.dataset.filter === status) {
            btn.className = 'filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-[#1a237e] text-white shadow-sm';
        } else {
            btn.className = 'filter-btn px-5 py-2.5 rounded-full text-sm font-bold transition bg-gray-100 text-gray-600 hover:bg-gray-200';
        }
    });

    // Filter desktop rows
    document.querySelectorAll('[data-status]').forEach(function(el) {
        if (status === 'all' || el.dataset.status === status) {
            el.style.display = '';
        } else {
            el.style.display = 'none';
        }
    });
}
</script>

@push('scripts')
    @include('partials.borrow-polling', ['role' => 'faculty'])
@endpush
@endsection
