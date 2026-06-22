<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $user = request()->user();

        if ($user->isAdmin()) {
            return response()->json([
                'total_resources' => Resource::count(),
                'total_users' => User::count(),
                'pending_borrows' => Borrow::where('status', 'pending')->count(),
                'active_borrows' => Borrow::where('status', 'approved')->count(),
                'recent_borrows' => Borrow::with('user', 'resource')
                    ->latest()
                    ->take(10)
                    ->get()
                    ->map(function ($borrow) {
                        return [
                            'id' => $borrow->id,
                            'user' => $borrow->user->name,
                            'resource' => $borrow->resource->name,
                            'status' => $borrow->status,
                            'borrow_date' => $borrow->borrow_date,
                            'due_date' => $borrow->due_date,
                        ];
                    }),
            ]);
        }

        return response()->json([
            'available_resources' => Resource::where('status', 'available')->count(),
            'borrowed_resources' => Resource::where('status', 'borrowed')->count(),
            'my_borrows' => Borrow::where('user_id', $user->id)->count(),
            'active_borrows' => Borrow::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->count(),
            'recent_borrows' => Borrow::where('user_id', $user->id)
                ->with('resource')
                ->latest()
                ->take(5)
                ->get()
                ->map(function ($borrow) {
                    return [
                        'id' => $borrow->id,
                        'resource' => $borrow->resource->name,
                        'status' => $borrow->status,
                        'borrow_date' => $borrow->borrow_date,
                        'due_date' => $borrow->due_date,
                    ];
                }),
        ]);
    }
}
