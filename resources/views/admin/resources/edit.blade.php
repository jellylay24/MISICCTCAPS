@extends('layouts.app')

@section('title', 'Edit Resource')
@section('header', 'Edit Resource')
@section('subheader', 'Update resource information')

@section('content')
<div class="max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.resources.update', $resource) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Resource Name</label>
                <input type="text" name="name" value="{{ old('name', $resource->name) }}" required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3"
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition">{{ old('description', $resource->description) }}</textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" name="quantity" value="{{ old('quantity', $resource->quantity) }}" min="0" required
                    class="w-full border border-gray-200 rounded-xl px-4 py-3 bg-gray-50 text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#1a237e] focus:border-transparent transition">
                @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-[#1a237e] text-white font-medium py-3 px-4 rounded-xl hover:bg-[#283593] transition text-sm">
                    Update Resource
                </button>
                <a href="{{ route('admin.resources.index') }}" class="px-6 py-3 rounded-xl bg-gray-100 text-gray-600 font-medium hover:bg-gray-200 transition text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
