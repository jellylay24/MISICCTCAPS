<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index()
    {
        $resources = Resource::withSum(['borrows as borrowed_qty' => function($q) {
            $q->whereIn('status', ['approved', 'borrowed']);
        }], 'quantity')->get();
        return view('admin.resources.index', compact('resources'));
    }

    public function create()
    {
        return view('admin.resources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $validated['is_available'] = $validated['quantity'] > 0;
        Resource::create($validated);

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource added successfully.');
    }

    public function edit(Resource $resource)
    {
        return view('admin.resources.edit', compact('resource'));
    }

    public function update(Request $request, Resource $resource)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'quantity' => 'required|integer|min:0',
        ]);

        $validated['is_available'] = $validated['quantity'] > 0;
        $resource->update($validated);

        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource updated successfully.');
    }

    public function destroy(Resource $resource)
    {
        $resource->delete();
        return redirect()->route('admin.resources.index')
            ->with('success', 'Resource deleted successfully.');
    }
}
