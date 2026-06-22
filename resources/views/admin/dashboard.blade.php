@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('header', 'Dashboard Overview')
@section('subheader', 'System statistics and insights')

@section('content')
{{-- Admin Dashboard Watermark Logo --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 mb-6">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="mb-3">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-900">Welcome to Admin Dashboard</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->setTimezone('Asia/Manila')->format('l, F d, Y') }}</p>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed">
                ICCT Cainta Campus MIS — Manage resources, track borrows, and oversee system operations.
            </p>
        </div>
    </div>
</div>

{{-- Alert Banner --}}
@if($overdueCount > 0)
<div class="bg-amber-50 border border-amber-200 rounded-xl px-4 lg:px-5 py-3 mb-4 lg:mb-6 flex items-center gap-2 lg:gap-3">
    <svg class="w-5 h-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
    </svg>
    <span class="text-xs lg:text-sm font-medium text-amber-800">{{ $overdueCount }} overdue item(s) — please follow up with borrowers.</span>
</div>
@endif

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Total Resources</p>
                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ $totalResources }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Available</p>
                <p class="text-2xl lg:text-3xl font-bold text-green-600 mt-1">{{ max(0, $totalAvailable) }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">In Use</p>
                <p class="text-2xl lg:text-3xl font-bold text-blue-600 mt-1">{{ $activeBorrows }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                <p class="text-2xl lg:text-3xl font-bold {{ $pendingRequests > 0 ? 'text-amber-500' : 'text-gray-900' }} mt-1">{{ $pendingRequests }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl {{ $pendingRequests > 0 ? 'bg-amber-50' : 'bg-gray-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 {{ $pendingRequests > 0 ? 'text-amber-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Row --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 lg:gap-5 mt-4 lg:mt-8">

    {{-- Panel 1: Transactions This Week --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <h3 class="font-semibold text-gray-900 text-sm lg:text-base mb-4">Transactions This Week</h3>
        <div style="position: relative; height: 150px;">
            <svg width="100%" height="150" viewBox="0 0 280 150" preserveAspectRatio="xMidYMid meet" style="overflow: visible;">
                <line x1="20" y1="130" x2="260" y2="130" stroke="#f3f4f6" stroke-width="1"/>
                <line x1="20" y1="95" x2="260" y2="95" stroke="#f3f4f6" stroke-width="1"/>
                <line x1="20" y1="60" x2="260" y2="60" stroke="#f3f4f6" stroke-width="1"/>
                <line x1="20" y1="25" x2="260" y2="25" stroke="#f3f4f6" stroke-width="1"/>

                @php
                    $points = collect();
                    $padding = 30;
                    $chartH = 120;
                    $bottomY = 130;
                    $stepX = (280 - 2 * $padding) / max(count($transactionsThisWeek) - 1, 1);
                    $maxVal = max($maxTx, 1);

                    foreach($transactionsThisWeek as $i => $tx) {
                        $x = $padding + $i * $stepX;
                        $y = $bottomY - ($tx['count'] / $maxVal) * $chartH;
                        $points->push(['x' => $x, 'y' => $y, 'count' => $tx['count'], 'day' => $tx['day']]);
                    }

                    $pathD = '';
                    foreach($points as $j => $p) {
                        if ($j === 0) {
                            $pathD = "M{$p['x']},{$p['y']}";
                        } else {
                            $prev = $points[$j - 1];
                            $cx1 = ($prev['x'] + $p['x']) / 2;
                            $pathD .= " C{$cx1},{$prev['y']} {$cx1},{$p['y']} {$p['x']},{$p['y']}";
                        }
                    }
                @endphp

                <defs>
                    <linearGradient id="lineFill" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#1a237e" stop-opacity="0.25"/>
                        <stop offset="100%" stop-color="#1a237e" stop-opacity="0.02"/>
                    </linearGradient>
                </defs>

                <path d="{{ $pathD }} L{{ $points->last()['x'] }},{{ $bottomY }} L{{ $points->first()['x'] }},{{ $bottomY }} Z" fill="url(#lineFill)"/>
                <path d="{{ $pathD }}" fill="none" stroke="#1a237e" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

                @foreach($points as $p)
                    <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#fff" stroke="#1a237e" stroke-width="2"/>
                    <text x="{{ $p['x'] }}" y="146" text-anchor="middle" fill="#6b7280" font-size="10" font-weight="500">{{ $p['day'] }}</text>
                    <text x="{{ $p['x'] }}" y="{{ $p['y'] - 10 }}" text-anchor="middle" fill="#1a237e" font-size="9" font-weight="bold">{{ $p['count'] }}</text>
                @endforeach
            </svg>
        </div>
        <p class="text-xs text-gray-400 mt-1 text-center">Transactions from {{ $transactionsThisWeek->first()['full_date'] }} to {{ $transactionsThisWeek->last()['full_date'] }}</p>
    </div>

    {{-- Panel 2: Most Used Resources --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <h3 class="font-semibold text-gray-900 text-sm lg:text-base mb-4">Most Used Resources</h3>
        @if($mostUsed->count() > 0)
            <div class="space-y-3">
                @foreach($mostUsed as $resource)
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-gray-500 w-4">{{ $loop->iteration }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm font-medium text-gray-800 truncate">{{ $resource->name }}</span>
                            <span class="text-xs font-bold text-indigo-600">{{ $resource->borrows_count }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full" style="width: {{ ($resource->borrows_count / $maxBorrows) * 100 }}%; background: #1a237e;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <p class="text-xs">No transactions yet</p>
            </div>
        @endif
    </div>
</div>

{{-- Resources Status (full width below) --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5 mt-3 lg:mt-5">
    <h3 class="font-semibold text-gray-900 text-sm lg:text-base mb-4">Resources Status</h3>
    @if($resourceStatus->count() > 0)
        <div class="space-y-3">
            @foreach($resourceStatus as $rs)
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-sm font-medium text-gray-800 truncate">{{ $rs['name'] }}</span>
                    <span class="text-xs text-gray-500 shrink-0 ml-2">{{ $rs['available'] }}/{{ $rs['total'] }} available</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2 flex overflow-hidden">
                    @if($rs['in_use'] > 0)
                    <div class="h-2 bg-blue-500 rounded-l-full transition-all"
                         style="width: {{ ($rs['in_use'] / max($rs['total'], 1)) * 100 }}%"></div>
                    @endif
                    @if($rs['available'] > 0)
                    <div class="h-2 bg-green-500 transition-all"
                         style="width: {{ ($rs['available'] / max($rs['total'], 1)) * 100 }}%"></div>
                    @endif
                </div>
                <div class="flex items-center gap-3 mt-1">
                    <span class="flex items-center gap-1 text-[10px] text-blue-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 inline-block"></span>
                        {{ $rs['in_use'] }} in use
                    </span>
                    <span class="flex items-center gap-1 text-[10px] text-green-600">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                        {{ $rs['available'] }} available
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-8 text-gray-400">
            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="text-xs">No resources yet</p>
        </div>
    @endif
</div>
@endsection
