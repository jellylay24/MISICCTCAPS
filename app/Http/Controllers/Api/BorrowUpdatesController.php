<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use Illuminate\Http\Request;

class BorrowUpdatesController extends Controller
{
    public function poll(Request $request)
    {
        $latestId = (int) $request->query('since', 0);
        $role = $request->query('role', 'faculty');

        // New records since last check
        $new = Borrow::with(['user', 'resource'])
            ->where('id', '>', $latestId)
            ->orderBy('id', 'desc')
            ->get();

        // Recently updated records (status changes like approve/reject/return)
        $updated = Borrow::with(['user', 'resource'])
            ->where('id', '<=', $latestId)
            ->where('updated_at', '>', now()->subSeconds(60))
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        // For faculty: only show own records
        if ($role === 'faculty') {
            $userId = auth()->id();
            $new = $new->where('user_id', $userId);
            $updated = $updated->where('user_id', $userId);
        }

        $changes = [];
        $maxId = $latestId;

        foreach ($new as $b) {
            $changes[] = [
                'id' => $b->id,
                'action' => 'new',
                'status' => $b->status,
                'resource' => $b->resource?->name ?? 'Unknown',
                'user' => $b->user?->name ?? 'Unknown',
            ];
            if ($b->id > $maxId) $maxId = $b->id;
        }

        foreach ($updated as $b) {
            // Skip if already in new (avoid duplicates)
            if ($new->contains('id', $b->id)) continue;

            $changes[] = [
                'id' => $b->id,
                'action' => 'update',
                'status' => $b->status,
                'resource' => $b->resource?->name ?? 'Unknown',
                'user' => $b->user?->name ?? 'Unknown',
            ];
        }

        return response()->json([
            'changes' => $changes,
            'max_id' => $maxId,
            'count' => count($changes),
            'now' => now()->timestamp,
        ]);
    }
}
