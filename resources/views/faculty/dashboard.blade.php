@extends('layouts.app')

@section('title', 'Faculty Dashboard')
@section('header', 'My Dashboard')
@section('subheader', 'Your borrowing activity at a glance')

@section('content')
{{-- Welcome Overview --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 lg:p-8 mb-6">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-4 mb-3">
                <div>
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900">Welcome, {{ Auth::user()->name }}! 👋</h2>
                    <p class="text-sm text-gray-500 mt-0.5">{{ \Carbon\Carbon::now()->setTimezone('Asia/Manila')->format('l, F d, Y') }}</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mt-3 leading-relaxed">
                ICCT Cainta Campus MIS — Request MIS tools, check your borrow history, and see what's available instantly.
            </p>


        </div>
    </div>
</div>

{{-- Overdue Alert Banner --}}
@if($overdueCount > 0)
<div id="overdue-banner" class="flex items-center gap-3 bg-red-50 border-l-4 border-red-500 rounded-xl px-5 py-4 mb-6">
    <svg class="w-6 h-6 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <div class="flex-1">
        <p class="text-sm font-bold text-red-800">You have {{ $overdueCount }} overdue item{{ $overdueCount > 1 ? 's' : '' }}.</p>
        <p class="text-xs text-red-600 mt-0.5">Please return {{ $overdueCount > 1 ? 'them' : 'it' }} as soon as possible to avoid penalties.</p>
        @if($overdueItems->count())
        <div class="mt-2">
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
    <a href="{{ route('faculty.history') }}" class="shrink-0 px-4 py-2 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition whitespace-nowrap">View Items</a>
</div>
@endif

{{-- Approved Alert Banner --}}
@php
    $approvedCount = \App\Models\Borrow::where('user_id', auth()->id())->where('status', 'approved')
        ->where(function($q) { $q->whereNull('due_at')->orWhere('due_at', '>', now()); })
        ->count();
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
    </div>
    <a href="{{ route('faculty.history') }}" class="shrink-0 px-4 py-2 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition whitespace-nowrap">View Items</a>
</div>
@endif

<script>
(function() {
    var el = document.getElementById('approved-alert');
    var ms = {{ $dismissSeconds }} * 1000;
    var approveUrl = '{{ route('faculty.history') }}';
    var lastShownCount = {{ $approvedCount }};

    function createBanner(count) {
        var container = document.querySelector('.content-area') || document.querySelector('[class*="mb-6"]') || document.querySelector('.container') || document.querySelector('main');
        if (!container) return;
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

// ─── Overdue banner real-time ───
(function() {
    var overdueContainer = document.getElementById('overdue-banner');
    var overdueBanner = null;
    var lastOverdueCount = {{ $overdueCount }};

    function renderOverdueBanner(data) {
        if (data.count <= 0) {
            if (overdueBanner) {
                overdueBanner.style.transition = 'opacity 0.5s, transform 0.5s';
                overdueBanner.style.opacity = '0';
                overdueBanner.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    if (overdueBanner) overdueBanner.remove();
                    overdueBanner = null;
                }, 500);
            }
            lastOverdueCount = 0;
            return;
        }

        if (overdueBanner) {
            // Update existing
            var countEl = overdueBanner.querySelector('.font-bold.text-red-800');
            if (countEl) {
                countEl.textContent = 'You have ' + data.count + ' overdue item' + (data.count > 1 ? 's' : '') + '.';
            }
            var listEl = overdueBanner.querySelector('.mt-2');
            if (listEl) {
                if (data.items && data.items.length > 0) {
                    listEl.innerHTML = data.items.map(function(i) {
                        return '<div class="text-xs text-red-700 font-medium flex items-center gap-1"><span>• ' + escapeHtml(i.name) + '</span>' +
                            (i.due_at ? '<span class="text-red-400">(due ' + i.due_at + ')</span>' : '') + '</div>';
                    }).join('');
                } else {
                    listEl.innerHTML = '';
                }
            }
            return;
        }

        // Create new banner
        var container = document.querySelector('[class*="mb-6"]');
        if (!container) container = document.querySelector('main');
        if (!container) return;

        var div = document.createElement('div');
        div.id = 'overdue-banner';
        div.className = 'flex items-center gap-3 bg-red-50 border-l-4 border-red-500 rounded-xl px-5 py-4 mb-6';
        div.style.opacity = '0';
        div.style.transform = 'translateY(-10px)';
        div.style.transition = 'opacity 0.5s, transform 0.5s';

        var itemsHtml = '';
        if (data.items && data.items.length > 0) {
            itemsHtml = '<div class="mt-2">' + data.items.map(function(i) {
                return '<div class="text-xs text-red-700 font-medium flex items-center gap-1"><span>• ' + escapeHtml(i.name) + '</span>' +
                    (i.due_at ? '<span class="text-red-400">(due ' + i.due_at + ')</span>' : '') + '</div>';
            }).join('') + '</div>';
        }

        div.innerHTML = '<svg class="w-6 h-6 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div class="flex-1"><p class="text-sm font-bold text-red-800">You have ' + data.count + ' overdue item' + (data.count > 1 ? 's' : '') + '.</p><p class="text-xs text-red-600 mt-0.5">Please return ' + (data.count > 1 ? 'them' : 'it') + ' as soon as possible to avoid penalties.</p>' + itemsHtml + '</div>' +
            '<a href="{{ route("faculty.history") }}" class="shrink-0 px-4 py-2 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition whitespace-nowrap">View Items</a>';

        container.parentNode.insertBefore(div, container);
        overdueBanner = div;
        overdueBanner.offsetHeight;
        requestAnimationFrame(function() {
            div.style.opacity = '1';
            div.style.transform = 'translateY(0)';
        });
    }

    function pollOverdue() {
        fetch('/faculty/overdue-check')
            .then(function(r) { return r.json(); })
            .then(function(data) {
                lastOverdueCount = data.count;
                renderOverdueBanner(data);
            })
            .catch(function() {});
    }

    // Remove the server-rendered static overdue banner (it's replaced by JS)
    if (overdueContainer) {
        overdueContainer.remove();
        overdueContainer = null;
    }

    // Initial render with server data — convert Blade items to JSON inline
    var initialData = {
        count: {{ $overdueCount }},
        items: [
            @php $first = true; @endphp
            @foreach($overdueItems as $item)
            @if(!$first),@endif
            {
                name: '{{ $item->quantity }}x {{ addslashes($item->resource->name) }}',
                due_at: '{{ $item->localDueAt() ? $item->localDueAt()->format('M d, Y h:i A') : '' }}'
            }@php $first = false; @endphp
            @endforeach
        ]
    };
    renderOverdueBanner(initialData);

    // Poll every 20 seconds
    setInterval(pollOverdue, 20000);
})();
</script>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-5 mb-6 lg:mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Total Borrows</p>
                <p class="text-2xl lg:text-3xl font-bold text-gray-900 mt-1">{{ $myBorrows }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl bg-indigo-50 flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Active</p>
                <p class="text-2xl lg:text-3xl font-bold text-blue-600 mt-1">{{ $myActive }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Pending</p>
                <p class="text-2xl lg:text-3xl font-bold {{ $myPending > 0 ? 'text-amber-500' : 'text-gray-900' }} mt-1">{{ $myPending }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl {{ $myPending > 0 ? 'bg-amber-50' : 'bg-gray-50' }} flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 {{ $myPending > 0 ? 'text-amber-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-[10px] lg:text-xs font-medium text-gray-500 uppercase tracking-wider">Available Resources</p>
                <p class="text-2xl lg:text-3xl font-bold text-green-600 mt-1">{{ $availableResources }}</p>
            </div>
            <div class="w-8 h-8 lg:w-10 lg:h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-4 h-4 lg:w-5 lg:h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Thank You --}}
<div class="text-center mt-8">
    <p class="text-base text-gray-400">Thank you for using <span class="font-semibold text-gray-600">ICCT Cainta Campus MIS</span>.</p>
</div>

<script>
function deleteNotif(id, btn) {
    fetch('/notifications/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
    .then(function(r) { return r.json(); })
    .then(function(d) {
        if (d.success) {
            var el = btn.closest('[class*="px-5"]');
            if (el) { el.style.opacity = '0'; setTimeout(function() { el.remove(); }, 300); }
        }
    });
}
</script>
@endsection
