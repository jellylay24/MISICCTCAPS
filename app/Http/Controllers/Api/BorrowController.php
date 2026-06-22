<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowController extends Controller
{
    public function index(): JsonResponse
    {
        $user = request()->user();

        $borrows = Borrow::when(!$user->isAdmin(), function ($q) use ($user) {
            return $q->where('user_id', $user->id);
        })
        ->with('user', 'resource')
        ->latest()
        ->get()
        ->map(function ($borrow) {
            return [
                'id' => $borrow->id,
                'user' => $borrow->user->name,
                'resource' => $borrow->resource->name,
                'status' => $borrow->status,
                'borrow_date' => $borrow->localBorrowedAt(),
                'due_date' => $borrow->localDueAt(),
                'returned_date' => $borrow->localReturnedAt(),
                'remarks' => $borrow->remarks,
                'created_at' => $borrow->created_at,
            ];
        });

        return response()->json($borrows);
    }

    public function active(): JsonResponse
    {
        $user = request()->user();

        $borrows = Borrow::whereIn('status', ['pending', 'approved'])
            ->when(!$user->isAdmin(), function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })
            ->with('user', 'resource')
            ->latest()
            ->get()
            ->map(function ($borrow) {
                return [
                    'id' => $borrow->id,
                    'user' => $borrow->user->name,
                    'resource' => $borrow->resource->name,
                    'status' => $borrow->status,
                    'borrow_date' => $borrow->localBorrowedAt(),
                    'due_date' => $borrow->localDueAt(),
                    'remarks' => $borrow->remarks,
                ];
            });

        return response()->json($borrows);
    }

    public function history(): JsonResponse
    {
        $user = request()->user();

        $borrows = Borrow::whereIn('status', ['returned', 'rejected'])
            ->when(!$user->isAdmin(), function ($q) use ($user) {
                return $q->where('user_id', $user->id);
            })
            ->with('user', 'resource')
            ->latest()
            ->get()
            ->map(function ($borrow) {
                return [
                    'id' => $borrow->id,
                    'user' => $borrow->user->name,
                    'resource' => $borrow->resource->name,
                    'status' => $borrow->status,
                    'borrow_date' => $borrow->localBorrowedAt(),
                    'due_date' => $borrow->localDueAt(),
                    'returned_date' => $borrow->localReturnedAt(),
                ];
            });

        return response()->json($borrows);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'borrow_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:borrow_date',
            'remarks' => 'nullable|string',
        ]);

        $resource = Resource::findOrFail($validated['resource_id']);

        if ($resource->available < 1) {
            return response()->json(['message' => 'Resource is not available for borrowing'], 400);
        }

        // Convert local input to UTC for storage
        $localBorrow = Carbon::parse($validated['borrow_date'], 'Asia/Manila');
        $localDue = Carbon::parse($validated['due_date'], 'Asia/Manila');

        $borrow = Borrow::create([
            'user_id' => $request->user()->id,
            'resource_id' => $resource->id,
            'borrow_date' => $localBorrow->setTimezone('UTC'),
            'due_date' => $localDue->setTimezone('UTC'),
            'remarks' => $validated['remarks'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Borrow request submitted successfully',
            'borrow' => $borrow->load('resource'),
        ], 201);
    }

    public function approve(Borrow $borrow): JsonResponse
    {
        $borrow->update(['status' => 'approved']);
        $borrow->resource->decrement('available');

        return response()->json(['message' => 'Borrow request approved']);
    }

    public function reject(Request $request, Borrow $borrow): JsonResponse
    {
        $request->validate(['remarks' => 'nullable|string']);

        $borrow->update([
            'status' => 'rejected',
            'remarks' => $request->remarks ?? 'Request rejected by admin',
        ]);

        return response()->json(['message' => 'Borrow request rejected']);
    }

    public function markReturned(Borrow $borrow): JsonResponse
    {
        $borrow->update([
            'status' => 'returned',
            'returned_date' => now(),
        ]);
        $borrow->resource->increment('available');

        return response()->json(['message' => 'Resource marked as returned']);
    }
}
