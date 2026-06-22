@extends('layouts.app')

@section('title', 'Manage Resources')
@section('header', 'Manage Resources')
@section('subheader', 'All campus resources and equipment')

@section('content')
<div class="flex justify-end mb-5">
    <a href="{{ route('admin.resources.create') }}" class="inline-flex items-center gap-2 bg-[#1a237e] text-white px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-[#283593] transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Resource
    </a>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Desktop & Tablet Table --}}
    <div class="hidden md:block overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b border-gray-100">
                <th class="px-6 py-4 font-medium">Name</th>
                <th class="px-6 py-4 font-medium">Total Qty</th>
                <th class="px-6 py-4 font-medium">Borrowed</th>
                <th class="px-6 py-4 font-medium">Available</th>
                <th class="px-6 py-4 font-medium">Status</th>
                <th class="px-6 py-4 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resources as $r)
            @php $avail = $r->quantity - ($r->borrowed_qty ?? 0); @endphp
            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                <td class="px-6 py-4 font-medium text-gray-900 text-sm">{{ $r->name }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $r->quantity }}</td>
                <td class="px-6 py-4 text-gray-700 text-sm">{{ $r->borrowed_qty ?? 0 }}</td>
                <td class="px-6 py-4">
                    <span class="font-bold text-sm {{ $avail > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $avail }}</span>
                </td>
                <td class="px-6 py-4">
                    @if($r->is_available && $avail > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Available</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">Unavailable</span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.resources.edit', $r) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium hover:bg-gray-200 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        <form action="{{ route('admin.resources.destroy', $r) }}" method="POST" class="inline" onsubmit="return confirm('Delete this resource?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400 text-sm">No resources yet. Add your first resource!</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

    {{-- Phone Cards --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($resources as $r)
        @php $avail = $r->quantity - ($r->borrowed_qty ?? 0); @endphp
        <div class="p-4 space-y-2">
            <div class="flex justify-between items-start">
                <div class="font-medium text-gray-900 text-sm">{{ $r->name }}</div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border shrink-0 ml-2
                    {{ $r->is_available && $avail > 0 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                    {{ $r->is_available && $avail > 0 ? 'Available' : 'Unavailable' }}
                </span>
            </div>
            <div class="grid grid-cols-3 gap-2 text-xs text-center">
                <div class="bg-gray-50 rounded-xl p-2">
                    <span class="text-gray-400 block">Total</span>
                    <p class="text-gray-900 font-bold text-sm">{{ $r->quantity }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-2">
                    <span class="text-gray-400 block">Borrowed</span>
                    <p class="text-gray-900 font-bold text-sm">{{ $r->borrowed_qty ?? 0 }}</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-2">
                    <span class="text-gray-400 block">Available</span>
                    <p class="font-bold text-sm {{ $avail > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $avail }}</p>
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <a href="{{ route('admin.resources.edit', $r) }}" class="flex-1 text-center px-3 py-2 rounded-lg bg-gray-100 text-gray-600 text-xs font-medium hover:bg-gray-200 transition">Edit</a>
                <form action="{{ route('admin.resources.destroy', $r) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this resource?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 rounded-lg bg-red-50 text-red-600 text-xs font-medium hover:bg-red-100 transition">Delete</button>
                </form>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">No resources yet. Add your first resource!</div>
        @endforelse
    </div>

</div>
@endsection
