<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(): JsonResponse
    {
        $resources = Resource::latest()->get()->map(function ($resource) {
            return [
                'id' => $resource->id,
                'name' => $resource->name,
                'description' => $resource->description,
                'category' => $resource->category,
                'status' => $resource->status,
                'quantity' => $resource->quantity,
                'available' => $resource->available,
                'created_at' => $resource->created_at,
            ];
        });

        return response()->json($resources);
    }

    public function show(Resource $resource): JsonResponse
    {
        return response()->json([
            'id' => $resource->id,
            'name' => $resource->name,
            'description' => $resource->description,
            'category' => $resource->category,
            'status' => $resource->status,
            'quantity' => $resource->quantity,
            'available' => $resource->available,
            'current_borrows' => $resource->borrows()
                ->whereIn('status', ['pending', 'approved'])
                ->with('user')
                ->get()
                ->map(function ($borrow) {
                    return [
                        'id' => $borrow->id,
                        'user' => $borrow->user->name,
                        'status' => $borrow->status,
                        'borrow_date' => $borrow->borrow_date,
                        'due_date' => $borrow->due_date,
                    ];
                }),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'quantity' => 'required|integer|min:1',
        ]);

        $resource = Resource::create($validated);

        return response()->json([
            'message' => 'Resource created successfully',
            'resource' => $resource,
        ], 201);
    }

    public function update(Request $request, Resource $resource): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:255',
            'quantity' => 'sometimes|integer|min:1',
            'status' => 'sometimes|in:available,borrowed,maintenance',
        ]);

        $resource->update($validated);

        return response()->json([
            'message' => 'Resource updated successfully',
            'resource' => $resource->fresh(),
        ]);
    }

    public function destroy(Resource $resource): JsonResponse
    {
        $resource->delete();

        return response()->json(['message' => 'Resource deleted successfully']);
    }
}
