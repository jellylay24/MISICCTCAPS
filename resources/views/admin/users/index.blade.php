@extends('layouts.app')

@section('title', 'User Management')
@section('header', 'User Management')
@section('subheader', 'Manage faculty and staff accounts')

@section('content')
<div class="flex items-center justify-between mb-4">
    <div></div>
    <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-[#1a237e] text-white text-sm font-semibold rounded-xl hover:bg-[#283593] transition gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Create Account
    </a>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    {{-- Desktop & Tablet Table --}}
    <div class="hidden md:block overflow-x-auto">
    <table class="w-full">
        <thead>
            <tr class="text-left text-sm text-gray-500 border-b border-gray-100">
                <th class="px-6 py-4 font-medium">User</th>
                <th class="px-6 py-4 font-medium">Role</th>
                <th class="px-6 py-4 font-medium">Active Borrows</th>
                <th class="px-6 py-4 font-medium">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $u)
            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ substr($u->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate ">{{ $u->name }}</div>
                            <div class="text-xs text-gray-400 truncate ">{{ $u->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                        {{ $u->role === 'admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                        {{ ucfirst($u->role) }}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <span class="text-sm font-medium {{ $u->active_borrows_count > 0 ? 'text-blue-600' : 'text-gray-500' }}">{{ $u->active_borrows_count }}</span>
                </td>
                <td class="px-6 py-4">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.edit', $u) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100 transition">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                        @if($u->id !== auth()->id())
                        <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline" onsubmit="return confirm('Delete this user? All their records will be removed.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">No users found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

    {{-- Phone Cards --}}
    <div class="md:hidden divide-y divide-gray-100">
        @forelse($users as $u)
        <div class="p-4 space-y-2">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-sm font-bold shrink-0">
                    {{ substr($u->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-medium text-gray-900 truncate">{{ $u->name }}</div>
                    <div class="text-xs text-gray-400 truncate">{{ $u->email }}</div>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border shrink-0
                    {{ $u->role === 'admin' ? 'bg-indigo-50 text-indigo-700 border-indigo-200' : 'bg-blue-50 text-blue-700 border-blue-200' }}">
                    {{ ucfirst($u->role) }}
                </span>
            </div>
            <div class="flex items-center justify-end text-xs">
                <div>
                    <span class="text-gray-400">Borrows:</span>
                    <span class="font-medium {{ $u->active_borrows_count > 0 ? 'text-blue-600' : 'text-gray-500' }} ml-1">{{ $u->active_borrows_count }}</span>
                </div>
            </div>
            <div class="flex gap-2 pt-1">
                <a href="{{ route('admin.users.edit', $u) }}" class="flex-1 text-center px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium hover:bg-indigo-100 transition">Edit</a>
                @if($u->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this user? All their records will be removed.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-3 py-2 rounded-lg bg-red-50 text-red-700 text-xs font-medium hover:bg-red-100 transition">Delete</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400 text-sm">No users found.</div>
        @endforelse
    </div>
</div>
@if($users instanceof \Illuminate\Pagination\LengthAwarePaginator && $users->hasPages())
<div class="mt-4">
    {{ $users->links() }}
</div>
@endif
@endsection
