@extends('layouts.app')

@section('title', 'Borrow: ' . $resource->name)
@section('header', 'Borrow: ' . $resource->name)
@section('subheader', 'Submit a borrowing request')

@section('content')
@php
    $available = $resource->availableQuantity();
    $defaultMinutes = 120;
    $presetOptions = [15, 30, 45, 60, 120, 180, 240];
@endphp

<div class="max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        {{-- Resource Info --}}
        <div class="mb-6 pb-6 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">{{ $resource->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">{{ $resource->description ?? 'No description available.' }}</p>
            <div class="mt-4 flex items-center gap-2">
                <span class="text-sm text-gray-500">Available:</span>
                <span class="text-lg font-bold {{ $available > 0 ? 'text-green-600' : 'text-red-500' }}">{{ $available }}</span>
                <span class="text-sm text-gray-400">/ {{ $resource->quantity }}</span>
            </div>
        </div>

        @if($available > 0)
        <form action="{{ route('faculty.borrows.store') }}" method="POST">
            @csrf
            <input type="hidden" name="resource_id" value="{{ $resource->id }}">

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity to borrow</label>
                <input type="number" name="quantity" min="1" max="{{ $available }}" value="1" required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition"
                    placeholder="Enter quantity">
                @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Room Input --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Room</label>
                <input type="text" name="room" maxlength="100"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition"
                    placeholder="e.g., Building, Room">
                @error('room') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Duration Selection --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">How long will you borrow it?</label>
                <select name="duration" id="duration-select"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition text-sm appearance-none">
                    @foreach($presetOptions as $mins)
                        @php
                            if ($mins < 60) {
                                $label = $mins . ' minutes';
                            } else {
                                $h = $mins / 60;
                                $label = $h . ' hour' . ($h > 1 ? 's' : '');
                            }
                        @endphp
                        <option value="{{ $mins }}" {{ $defaultMinutes == $mins ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                    <option value="custom">Custom</option>
                </select>

                {{-- Custom duration (hidden by default) --}}
                <div id="custom-duration" class="hidden mt-3 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1 font-medium">Hours</label>
                            <input type="number" id="custom_hours" min="0" max="24" value="0"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition text-sm text-center">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-gray-500 mb-1 font-medium">Minutes</label>
                            <input type="number" id="custom_minutes" min="1" max="59" value="30"
                                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 bg-white text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition text-sm text-center">
                        </div>
                    </div>
                </div>

                {{-- Store duration in minutes for server-side due_at computation on approval --}}
                <input type="hidden" name="duration_minutes" id="duration_minutes" value="">

                {{-- Duration preview (for info only, not stored as due_at) --}}
                <div id="duration-preview" class="mt-3 text-xs text-gray-500">
                    Duration: <span id="duration-preview-time" class="font-medium text-gray-700">--</span>
                    <span class="text-gray-400">(timer starts upon admin approval)</span>
                </div>

                @error('duration_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-[#1a237e] text-white font-medium py-3 px-4 rounded-xl hover:bg-[#283593] transition text-sm">
                    Submit Borrow Request
                </button>
                <a href="{{ route('faculty.resources.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-600 font-medium hover:bg-gray-200 transition text-sm">
                    Cancel
                </a>
            </div>
        </form>
        @else
        <div class="text-center py-6">
            <div class="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <p class="text-red-500 font-medium">This resource is currently unavailable for borrowing.</p>
            <a href="{{ route('faculty.resources.index') }}" class="inline-flex items-center gap-2 mt-4 bg-gray-100 text-gray-600 font-medium px-6 py-3 rounded-xl hover:bg-gray-200 transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Resources
            </a>
        </div>
        @endif
    </div>
</div>

<script>
function computeDuration() {
    var select = document.getElementById('duration-select');
    var value = select.value;
    var totalMinutes;

    if (value === 'custom') {
        var hours = parseInt(document.getElementById('custom_hours').value) || 0;
        var minutes = parseInt(document.getElementById('custom_minutes').value) || 0;
        if (hours > 24) hours = 24;
        if (minutes > 59) minutes = 59;
        if (hours === 0 && minutes === 0) minutes = 1;
        totalMinutes = (hours * 60) + minutes;
    } else {
        totalMinutes = parseInt(value);
    }

    if (totalMinutes < 1) totalMinutes = 1;
    if (totalMinutes > 1440) totalMinutes = 1440;

    document.getElementById('duration_minutes').value = totalMinutes;

    var display;
    if (totalMinutes < 60) {
        display = totalMinutes + ' minutes';
    } else {
        var h = Math.floor(totalMinutes / 60);
        var m = totalMinutes % 60;
        display = h + ' hour' + (h > 1 ? 's' : '');
        if (m > 0) display += ' ' + m + ' min';
    }
    document.getElementById('duration-preview-time').textContent = display;
}

document.getElementById('duration-select').addEventListener('change', function() {
    var customDiv = document.getElementById('custom-duration');
    if (this.value === 'custom') {
        customDiv.classList.remove('hidden');
    } else {
        customDiv.classList.add('hidden');
    }
    computeDuration();
});

document.getElementById('custom_hours').addEventListener('input', computeDuration);
document.getElementById('custom_minutes').addEventListener('input', computeDuration);

computeDuration();
</script>
@endsection
