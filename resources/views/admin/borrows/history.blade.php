@extends('layouts.app')

@section('title', 'Borrow History')
@section('header', 'Borrow History')
@section('subheader', 'Completed and cancelled borrowing records')

@section('content')
{{-- Filter Tabs --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 mb-5 flex flex-wrap items-center justify-center gap-2">
    <a href="{{ route('admin.borrows.index') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 transition">All</a>
    <a href="{{ route('admin.borrows.pending') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 transition">Pending</a>
    <a href="{{ route('admin.borrows.active') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 transition">Active</a>
    <a href="{{ route('admin.borrows.history') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.history') ? 'bg-gray-500 text-white' : 'text-gray-600 hover:text-gray-900' }} transition">History</a>
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
                <th class="px-6 py-4 font-medium">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $b)
            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-medium text-gray-900 text-sm">{{ $b->user->name }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $b->resource->name }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $b->quantity }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $b->room ?: '—' }}</td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                        @if($b->status === 'returned') bg-green-50 text-green-700 border-green-200
                        @else bg-red-50 text-red-700 border-red-200 @endif">
                        {{ ucfirst($b->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $b->created_at->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">No completed borrows.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

    {{-- Phone Cards --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($borrows as $b)
        <div class="p-4 space-y-2">
            <div class="flex justify-between items-start">
                <div class="font-medium text-gray-900 text-sm">{{ $b->user->name }}</div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border shrink-0 ml-2
                    @if($b->status === 'returned') bg-green-50 text-green-700 border-green-200
                    @else bg-red-50 text-red-700 border-red-200 @endif">
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
                <div class="col-span-2">
                    <span class="text-gray-400">Date</span>
                    <p class="text-gray-700">{{ $b->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">No completed borrows.</div>
        @endforelse
    </div>
</div>

<script>
function reloadTable() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function(r) { return r.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var newTable = doc.querySelector('.bg-white.rounded-2xl.overflow-hidden');
            var oldTable = document.querySelector('.bg-white.rounded-2xl.overflow-hidden');
            if (newTable && oldTable) {
                oldTable.innerHTML = newTable.innerHTML;
            }
        })
        .catch(function() {});
}
setInterval(reloadTable, 30000);
</script>
@push('scripts')
    @include('partials.borrow-polling', ['role' => 'admin'])
@endpush
@endsection
