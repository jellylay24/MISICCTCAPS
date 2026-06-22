<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Resource;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BorrowController extends Controller
{
    public function create(Resource $resource)
    {
        return view('faculty.borrows.create', compact('resource'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'quantity' => 'required|integer|min:1',
            'duration' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1|max:1440',
            'room' => 'nullable|string|max:100',
        ]);

        $resource = Resource::findOrFail($validated['resource_id']);
        $available = $resource->availableQuantity();

        if ($validated['quantity'] > $available) {
            return back()->with('error', 'Not enough available quantity.');
        }

        Borrow::create([
            'user_id' => auth()->id(),
            'resource_id' => $validated['resource_id'],
            'quantity' => $validated['quantity'],
            'duration_minutes' => $validated['duration_minutes'],
            'room' => $validated['room'] ?? null,
            'status' => 'pending',
        ]);

        return redirect()->route('faculty.history')
            ->with('success', 'Borrow request submitted for approval.');
    }

    public function history()
    {
        $borrows = Borrow::with('resource')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        $latestId = Borrow::where('user_id', auth()->id())->max('id') ?? 0;
        return view('faculty.borrows.history', compact('borrows', 'latestId'));
    }

    public function export()
    {
        $borrows = Borrow::with('resource')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'borrow-history-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($borrows) {
            $file = fopen('php://output', 'w');

            // CSV header row
            fputcsv($file, [
                'Resource', 'Quantity', 'Status',
                'Requested', 'Borrowed', 'Due Date', 'Returned'
            ]);

            foreach ($borrows as $b) {
                fputcsv($file, [
                    $b->resource->name,
                    $b->quantity,
                    ucfirst($b->status),
                    $b->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                    $b->localBorrowedAt()?->format('M d, Y h:i A') ?: 'N/A',
                    $b->localDueAt()?->format('M d, Y h:i A') ?: 'N/A',
                    $b->localReturnedAt()?->format('M d, Y h:i A') ?: 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
