<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrow;
use App\Models\Resource;
use App\Models\Notification;
use Illuminate\Http\Request;

class BorrowController extends Controller
{
    public function index()
    {
        $borrows = Borrow::with(['user', 'resource'])
            ->orderBy('created_at', 'desc')
            ->get();
        $latestId = Borrow::max('id') ?? 0;
        return view('admin.borrows.index', compact('borrows', 'latestId'));
    }

    public function pending()
    {
        $borrows = Borrow::with(['user', 'resource'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();
        $latestId = Borrow::max('id') ?? 0;
        return view('admin.borrows.pending', compact('borrows', 'latestId'));
    }

    public function approve(Borrow $borrow)
    {
        $resource = $borrow->resource;
        $available = $resource->availableQuantity();

        if ($borrow->quantity > $available) {
            return back()->with('error', 'Not enough available quantity.');
        }

        $now = now();
        $dueAt = $borrow->duration_minutes
            ? $now->copy()->addMinutes($borrow->duration_minutes)
            : $now->copy()->addHours(2); // fallback 2hrs if no duration set

        $borrow->update([
            'status' => 'approved',
            'borrowed_at' => $now,
            'due_at' => $dueAt,
        ]);

        Notification::create([
            'user_id' => $borrow->user_id,
            'title' => 'Borrow Request Approved',
            'message' => "Your request for {$borrow->quantity}x {$resource->name} has been approved.",
        ]);

        return back()->with('success', 'Borrow request approved.');
    }

    public function reject(Request $request, Borrow $borrow)
    {
        $borrow->update([
            'status' => 'rejected',
            'notes' => $request->notes,
        ]);

        Notification::create([
            'user_id' => $borrow->user_id,
            'title' => 'Borrow Request Rejected',
            'message' => "Your request for {$borrow->quantity}x {$borrow->resource->name} has been rejected." .
                ($request->notes ? " Reason: {$request->notes}" : ''),
        ]);

        return back()->with('success', 'Borrow request rejected.');
    }

    public function markReturned(Borrow $borrow)
    {
        $borrow->update([
            'status' => 'returned',
            'returned_at' => now(),
        ]);

        Notification::create([
            'user_id' => $borrow->user_id,
            'title' => 'Item Returned',
            'message' => "{$borrow->quantity}x {$borrow->resource->name} has been marked as returned.",
        ]);

        return back()->with('success', 'Item marked as returned.');
    }

    public function active()
    {
        $borrows = Borrow::with(['user', 'resource'])
            ->whereIn('status', ['approved', 'borrowed'])
            ->orderBy('created_at', 'desc')
            ->get();
        $latestId = Borrow::max('id') ?? 0;
        return view('admin.borrows.active', compact('borrows', 'latestId'));
    }

    public function history()
    {
        $borrows = Borrow::with(['user', 'resource'])
            ->whereIn('status', ['returned', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();
        $latestId = Borrow::max('id') ?? 0;
        return view('admin.borrows.history', compact('borrows', 'latestId'));
    }

    public function export()
    {
        $borrows = Borrow::with(['user', 'resource'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'all-borrows-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($borrows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'User', 'Resource', 'Quantity', 'Status',
                'Requested', 'Borrowed', 'Due Date', 'Returned'
            ]);
            foreach ($borrows as $b) {
                fputcsv($file, [
                    $b->user->name,
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
