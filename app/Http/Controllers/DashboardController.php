<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\Borrow;
use App\Models\Notification;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->isAdmin()) {
            return $this->adminDashboard();
        }
        return $this->facultyDashboard();
    }

    private function adminDashboard()
    {
        $totalResources = Resource::count();
        $totalAvailable = Resource::sum('quantity')
            - Borrow::whereIn('status', ['approved', 'borrowed'])->sum('quantity');
        $activeBorrows = Borrow::whereIn('status', ['approved', 'borrowed'])->count();
        $pendingRequests = Borrow::where('status', 'pending')->count();
        $overdueCount = Borrow::whereIn('status', ['approved', 'borrowed'])
            ->where('due_at', '<', now())
            ->count();

        // Transactions this week (per day)
        $transactionsThisWeek = collect();
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $count = Borrow::whereDate('created_at', $day)->count();
            $transactionsThisWeek->push([
                'day' => $day->format('D'),
                'count' => $count,
                'full_date' => $day->format('M d'),
            ]);
        }
        $maxTx = max($transactionsThisWeek->max('count'), 1);

        // Most used resources (top 5 by borrow count)
        $mostUsed = Resource::withCount(['borrows' => function ($q) {
                $q->whereIn('status', ['approved', 'borrowed', 'returned']);
            }])
            ->orderBy('borrows_count', 'desc')
            ->take(5)
            ->get();
        $maxBorrows = $mostUsed->max('borrows_count') ?: 1;

        // Resources status breakdown
        $resources = Resource::all();
        $resourceStatus = $resources->map(function ($r) {
            $inUse = Borrow::where('resource_id', $r->id)
                ->whereIn('status', ['approved', 'borrowed'])
                ->sum('quantity');
            $available = $r->quantity - $inUse;
            return [
                'name' => $r->name,
                'total' => $r->quantity,
                'available' => max(0, $available),
                'in_use' => $inUse,
            ];
        });

        return view('admin.dashboard', compact(
            'totalResources', 'totalAvailable', 'activeBorrows',
            'pendingRequests', 'overdueCount',
            'transactionsThisWeek', 'maxTx',
            'mostUsed', 'maxBorrows',
            'resourceStatus'
        ));
    }

    private function facultyDashboard()
    {
        $myBorrows = Borrow::where('user_id', auth()->id())->count();
        $myActive = Borrow::where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'borrowed'])->count();
        $myPending = Borrow::where('user_id', auth()->id())
            ->where('status', 'pending')->count();
        $myHistory = Borrow::where('user_id', auth()->id())
            ->whereIn('status', ['returned', 'rejected'])->count();
        $availableResources = Resource::where('is_available', true)->count();
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()->take(5)->get();

        // Overdue items for this faculty member
        $overdueCount = Borrow::where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_at', '<', now())
            ->count();
        $overdueItems = Borrow::where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_at', '<', now())
            ->with('resource')
            ->get();

        return view('faculty.dashboard', compact(
            'myBorrows', 'myActive', 'myPending',
            'myHistory', 'availableResources', 'notifications',
            'overdueCount', 'overdueItems'
        ));
    }
}
