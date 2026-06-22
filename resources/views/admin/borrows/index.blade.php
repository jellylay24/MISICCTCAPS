@extends('layouts.app')

@section('title', 'Borrow Management')
@section('header', 'Borrow Management')
@section('subheader', 'Manage all borrowing transactions')

@section('content')
{{-- Filter Tabs --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 mb-5 flex flex-wrap items-center justify-center gap-2">
    <a href="{{ route('admin.borrows.index') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.index') ? 'bg-[#1a237e] text-white' : 'text-gray-600 hover:text-gray-900' }} transition">All</a>
    <a href="{{ route('admin.borrows.pending') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.pending') ? 'bg-amber-500 text-white' : 'text-gray-600 hover:text-gray-900' }} transition">Pending</a>
    <a href="{{ route('admin.borrows.active') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.active') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-900' }} transition">Active</a>
    <a href="{{ route('admin.borrows.history') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.history') ? 'bg-gray-500 text-white' : 'text-gray-600 hover:text-gray-900' }} transition">History</a>
    <a href="{{ route('admin.borrows.export') }}" class="px-4 py-2 rounded-lg text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 transition flex items-center gap-1">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        Export CSV
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Desktop & Tablet Table --}}
    <div class="hidden md:block overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b border-gray-100">
                <th class="px-6 py-4 font-medium">User</th>
                <th class="px-6 py-4 font-medium">Resource</th>
                <th class="px-6 py-4 font-medium">Qty</th>
                <th class="px-6 py-4 font-medium">Room</th>
                <th class="px-6 py-4 font-medium">Status</th>
                <th class="px-6 py-4 font-medium">Due Date</th>
                <th class="px-6 py-4 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $b)
            @php $localDue = $b->localDueAt(); @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-medium text-gray-900 text-sm">{{ $b->user->name }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $b->resource->name }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $b->quantity }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $b->room ?: '—' }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                        @switch($b->status)
                            @case('pending') bg-amber-50 text-amber-700 border-amber-200 @break
                            @case('approved') bg-blue-50 text-blue-700 border-blue-200 @break
                            @case('borrowed') bg-indigo-50 text-indigo-700 border-indigo-200 @break
                            @case('returned') bg-green-50 text-green-700 border-green-200 @break
                            @default bg-red-50 text-red-700 border-red-200
                        @endswitch">
                        {{ ucfirst($b->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm font-medium">
                    @if($localDue && !in_array($b->status, ['returned','rejected','pending']))
                        <span class="due-countdown" data-due-ts="{{ $localDue->timestamp }}">--</span>
                    @elseif($localDue)
                        <span class="text-gray-500">{{ $localDue->format('M d, Y h:i A') }}</span>
                    @else
                        <span class="text-gray-400">&#8212;</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        @if($b->status === 'pending')
                        <form action="{{ route('admin.borrows.approve', $b) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Approve
                            </button>
                        </form>
                        <form action="{{ route('admin.borrows.reject', $b) }}" method="POST" class="inline" onsubmit="return confirm('Reject this request?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Reject
                            </button>
                        </form>
                        @elseif(in_array($b->status, ['approved', 'borrowed']))
                        <form action="{{ route('admin.borrows.markReturned', $b) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Mark Returned
                            </button>
                        </form>
                        @else
                        <span class="text-gray-400 text-xs">&#8212;</span>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No borrow records.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

    {{-- Phone Cards --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($borrows as $b)
        @php
            $localDue = $b->localDueAt();
            $dueTs = $localDue?->timestamp ?: '';
        @endphp
        <div class="p-4 space-y-2">
            <div class="flex justify-between items-start">
                <div class="font-medium text-gray-900 text-sm">{{ $b->user->name }}</div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border shrink-0 ml-2
                    @switch($b->status)
                        @case('pending') bg-amber-50 text-amber-700 border-amber-200 @break
                        @case('approved') bg-blue-50 text-blue-700 border-blue-200 @break
                        @case('borrowed') bg-indigo-50 text-indigo-700 border-indigo-200 @break
                        @case('returned') bg-green-50 text-green-700 border-green-200 @break
                        @default bg-red-50 text-red-700 border-red-200
                    @endswitch">
                    {{ ucfirst($b->status) }}
                </span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <div>
                    <span class="text-gray-400">Resource</span>
                    <p class="text-gray-700 font-medium">{{ $b->resource->name }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Qty</span>
                    <p class="text-gray-700 font-medium">{{ $b->quantity }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Room</span>
                    <p class="text-gray-700 font-medium">{{ $b->room ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-gray-400">Due Date</span>
                    <p class="text-gray-700 font-medium">
                        @if($localDue && !in_array($b->status, ['returned','rejected','pending']))
                            <span class="due-countdown-mobile" data-due-ts="{{ $dueTs }}">--</span>
                        @elseif($localDue)
                            {{ $localDue->format('M d, Y h:i A') }}
                        @else
                            &#8212;
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                @if($b->status === 'pending')
                <form action="{{ route('admin.borrows.approve', $b) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100 transition">Approve</button>
                </form>
                <form action="{{ route('admin.borrows.reject', $b) }}" method="POST" class="flex-1" onsubmit="return confirm('Reject this request?')">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition">Reject</button>
                </form>
                @elseif(in_array($b->status, ['approved', 'borrowed']))
                <form action="{{ route('admin.borrows.markReturned', $b) }}" method="POST" class="flex justify-center">
                    @csrf
                    <button type="submit" class="w-full max-w-sm px-3 py-2 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100 transition">Mark Returned</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">No borrow records.</div>
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
            el.className = (el.className.includes('due-countdown-mobile') ? 'due-countdown-mobile ' : 'due-countdown ') + 'text-red-600 font-bold';
            el.textContent = 'OVERDUE';
            return;
        }
        var days = Math.floor(diff / 86400);
        var hours = Math.floor((diff % 86400) / 3600);
        var mins = Math.floor((diff % 3600) / 60);
        var secs = diff % 60;
        if (days > 0) {
            el.textContent = days + 'd ' + hours.toString().padStart(2,'0') + 'h ' + mins.toString().padStart(2,'0') + 'm ' + secs.toString().padStart(2,'0') + 's';
        } else {
            el.textContent = hours.toString().padStart(2,'0') + 'h ' + mins.toString().padStart(2,'0') + 'm ' + secs.toString().padStart(2,'0') + 's';
        }
        if (diff < 3600) {
            el.className = (el.className.includes('due-countdown-mobile') ? 'due-countdown-mobile ' : 'due-countdown ') + 'text-red-600 font-bold';
        } else if (diff < 86400) {
            el.className = (el.className.includes('due-countdown-mobile') ? 'due-countdown-mobile ' : 'due-countdown ') + 'text-amber-600 font-medium';
        } else {
            el.className = (el.className.includes('due-countdown-mobile') ? 'due-countdown-mobile ' : 'due-countdown ') + 'text-gray-700';
        }
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
@push('scripts')
    @include('partials.borrow-polling', ['role' => 'admin'])
@endpush
@endsection
