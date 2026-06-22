@extends('layouts.app')

@section('title', 'Pending Requests')
@section('header', 'Pending Borrow Requests')
@section('subheader', 'Review and process faculty borrowing requests')

@section('content')
{{-- Filter Tabs --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 mb-5 flex flex-wrap items-center justify-center gap-2">
    <a href="{{ route('admin.borrows.index') }}" class="px-4 py-2 rounded-lg text-xs font-medium text-gray-600 hover:text-gray-900 transition">All</a>
    <a href="{{ route('admin.borrows.pending') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.pending') ? 'bg-amber-500 text-white' : 'text-gray-600 hover:text-gray-900' }} transition">Pending</a>
    <a href="{{ route('admin.borrows.active') }}" class="px-4 py-2 rounded-lg text-xs font-medium {{ request()->routeIs('admin.borrows.active') ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-900' }} transition">Active</a>
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
                <th class="px-6 py-4 font-medium">Date</th>
                <th class="px-6 py-4 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($borrows as $b)
            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-medium text-gray-900 text-sm">{{ $b->user->name }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $b->resource->name }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $b->quantity }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $b->room ?: '—' }}</td>
                <td class="px-6 py-4 text-sm text-gray-500">{{ $b->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</td>
                <td class="px-6 py-4">
                    <div class="flex gap-2 flex-wrap">
                        <form action="{{ route('admin.borrows.approve', $b) }}" method="POST" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" onclick="return confirm('Approve this request?')" class="px-3 py-1.5 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition font-medium">Approve</button>
                        </form>
                        <button onclick="showRejectModal({{ $b->id }})" class="px-3 py-1.5 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition font-medium">Reject</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">No pending requests.</td></tr>
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
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200 shrink-0 ml-2">Pending</span>
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
                    <span class="text-gray-400">Date</span>
                    <p class="text-gray-700">{{ $b->created_at->setTimezone('Asia/Manila')->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <form action="{{ route('admin.borrows.approve', $b) }}" method="POST" class="flex-1">
                    @csrf
                    @method('PATCH')
                    <button type="submit" onclick="return confirm('Approve this request?')" class="w-full px-3 py-2 bg-green-500 text-white text-xs rounded-lg hover:bg-green-600 transition font-medium">Approve</button>
                </form>
                <button onclick="showRejectModal({{ $b->id }})" class="flex-1 px-3 py-2 bg-red-500 text-white text-xs rounded-lg hover:bg-red-600 transition font-medium">Reject</button>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">No pending requests.</div>
        @endforelse
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" class="fixed inset-0 bg-black/40 hidden items-center justify-center z-50 p-4" onclick="if(event.target===this)hideRejectModal()">
    <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md" onclick="event.stopPropagation()">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Reject Request</h3>
        <form id="rejectForm" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for rejection</label>
                <textarea name="notes" rows="3" class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Enter reason..." required></textarea>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm rounded-xl hover:bg-gray-200 transition">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-500 text-white text-sm rounded-xl hover:bg-red-600 transition">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/borrows/' + id + '/reject';
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}
function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}
</script>
@push('scripts')
    @include('partials.borrow-polling', ['role' => 'admin'])
@endpush
@endsection
