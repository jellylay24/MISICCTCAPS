@extends('layouts.app')

@section('title', 'Notifications')
@section('header', 'Notifications')
@section('subheader', 'System updates and alerts')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center flex-wrap gap-2">
        <span class="text-sm text-gray-500">{{ $notifications->total() }} notification(s)</span>
        @if($notifications->total() > 0)
        <div class="flex gap-2">
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all read</button>
            </form>
            <form action="{{ route('notifications.clearAll') }}" method="POST" onsubmit="return confirm('Clear all notifications?')">
                @csrf
                <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Clear all</button>
            </form>
        </div>
        @endif
    </div>
    <div class="divide-y divide-gray-50" id="notifications-list">
        @forelse($notifications as $n)
        <div class="px-6 py-4 {{ $n->is_read ? '' : 'bg-indigo-50/40' }}" data-id="{{ $n->id }}">
            <div class="flex items-start gap-3">
                @if(!$n->is_read)
                <div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 shrink-0"></div>
                @else
                <div class="w-2 h-2 rounded-full bg-transparent mt-2 shrink-0"></div>
                @endif
                <div class="min-w-0 flex-1">
                    <div class="flex justify-between items-start">
                        <div class="text-sm {{ $n->is_read ? 'text-gray-700' : 'font-semibold text-gray-900' }}">{{ $n->title }}</div>
                        <div class="text-xs text-gray-400 shrink-0 ml-4">{{ $n->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</div>
                    </div>
                    @if($n->message)
                    <div class="text-sm text-gray-500 mt-1">{{ $n->message }}</div>
                    @endif
                </div>
                <button onclick="deleteNotification({{ $n->id }}, this)" class="shrink-0 text-gray-300 hover:text-red-500 transition p-1 -mr-1" title="Delete">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <div class="px-6 py-12 text-center">
            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <p class="text-gray-400 text-sm">No notifications.</p>
        </div>
        @endforelse

        @if($notifications->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</div>

<script>
function deleteNotification(id, btn) {
    if (!confirm('Delete this notification?')) return;
    fetch('/notifications/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            var el = btn.closest('[data-id]');
            el.style.transition = 'opacity 0.3s';
            el.style.opacity = '0';
            setTimeout(function() { el.remove(); location.reload(); }, 300);
        }
    });
}

var pollInterval = null;
var lastNotifId = {{ $notifications->first()->id ?? 0 }};

function pollNewNotifications() {
    fetch('/notifications/latest?after=' + lastNotifId)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var list = document.getElementById('notifications-list');
            var emptyMsg = list.querySelector('.text-center');
            if (emptyMsg && d.notifications.length > 0) {
                emptyMsg.outerHTML = '';
            }
            d.notifications.reverse().forEach(function(n) {
                var dot = n.is_read
                    ? '<div class="w-2 h-2 rounded-full bg-transparent mt-2 shrink-0"></div>'
                    : '<div class="w-2 h-2 rounded-full bg-indigo-500 mt-2 shrink-0"></div>';
                var badge = n.is_read ? '' : '<div class="text-sm font-semibold text-gray-900">' + escapeHtml(n.title) + '</div>';
                var normal = n.is_read ? '<div class="text-sm text-gray-700">' + escapeHtml(n.title) + '</div>' : '';
                var msg = n.message ? '<div class="text-sm text-gray-500 mt-1">' + escapeHtml(n.message) + '</div>' : '';
                var hl = n.is_read ? '' : ' bg-indigo-50/40';
                var html = '<div class="px-6 py-4' + hl + '" data-id="' + n.id + '">' +
                    '<div class="flex items-start gap-3">' + dot +
                    '<div class="min-w-0 flex-1">' +
                    '<div class="flex justify-between items-start">' +
                    (badge || normal) +
                    '<div class="text-xs text-gray-400 shrink-0 ml-4">' + n.created_at + '</div>' +
                    '</div>' + msg + '</div>' +
                    '<button onclick="deleteNotification(' + n.id + ', this)" class="shrink-0 text-gray-300 hover:text-red-500 transition p-1 -mr-1" title="Delete">' +
                    '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>' +
                    '</button></div></div>';
                var temp = document.createElement('div');
                temp.innerHTML = html;
                list.insertBefore(temp.firstElementChild, list.firstChild);
                if (n.id > lastNotifId) lastNotifId = n.id;
            });
            if (d.notifications.length > 0) {
                var headerCount = document.querySelector('.text-sm.text-gray-500');
                if (headerCount) {
                    var current = parseInt(headerCount.textContent);
                    headerCount.textContent = (current + d.notifications.length) + ' notification(s)';
                }
            }
        });
}

function escapeHtml(str) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
}

pollInterval = setInterval(pollNewNotifications, 15000);
</script>
@endsection
