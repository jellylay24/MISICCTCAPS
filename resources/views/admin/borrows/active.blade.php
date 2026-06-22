@extends('layouts.app')

@section('title', 'Active Borrows')
@section('header', 'Active Borrows')
@section('subheader', 'Currently borrowed items that need to be returned')

@section('content')
{{-- Filter Tabs --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 mb-5 flex flex-wrap items-center justify-center gap-2">
    <a href="{{ route('admin.borrows.index') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 transition">All</a>
    <a href="{{ route('admin.borrows.pending') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 transition">Pending</a>
    <a href="{{ route('admin.borrows.active') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.active') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-900' }} transition">Active</a>
    <a href="{{ route('admin.borrows.history') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 transition">History</a>
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
                <td class="px-6 py-4 text-sm font-medium due-cell" data-due="{{ $localDue?->timestamp ?: '' }}">
                    @if($localDue)
                        <span class="due-countdown" data-due-ts="{{ $localDue->timestamp }}">--</span>
                    @else
                        &#8212;
                    @endif
                </td>
                <td class="px-6 py-4">
                    <form action="{{ route('admin.borrows.markReturned', $b) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Mark Returned
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">No active borrows.</td></tr>
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
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200 shrink-0 ml-2">Borrowed</span>
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
                        @if($localDue)
                            <span class="due-countdown-mobile" data-due-ts="{{ $dueTs }}">--</span>
                        @else
                            &#8212;
                        @endif
                    </p>
                </div>
            </div>
            <div class="pt-1 flex justify-center">
                <form action="{{ route('admin.borrows.markReturned', $b) }}" method="POST" class="w-full max-w-sm">
                    @csrf
                    <button type="submit" class="w-full px-3 py-2 rounded-lg bg-green-50 text-green-700 text-xs font-medium hover:bg-green-100 transition flex items-center justify-center">
                        Mark Returned
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">No active borrows.</div>
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
