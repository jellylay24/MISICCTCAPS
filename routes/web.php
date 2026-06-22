<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\ResourceController as AdminResourceController;
use App\Http\Controllers\Admin\BorrowController as AdminBorrowController;
use App\Http\Controllers\Api\BorrowUpdatesController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Faculty\ResourceController as FacultyResourceController;
use App\Http\Controllers\Faculty\BorrowController as FacultyBorrowController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Real-time borrow polling endpoint
    Route::get('/borrow-updates', [BorrowUpdatesController::class, 'poll'])->name('borrow.updates');
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
    Route::get('/notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');

    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::post('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clearAll');

    // Admin Routes
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('resources', AdminResourceController::class);
        Route::get('/borrows', [AdminBorrowController::class, 'index'])->name('borrows.index');
        Route::get('/borrows/pending', [AdminBorrowController::class, 'pending'])->name('borrows.pending');
        Route::get('/borrows/active', [AdminBorrowController::class, 'active'])->name('borrows.active');
        Route::get('/borrows/history', [AdminBorrowController::class, 'history'])->name('borrows.history');
        Route::get('/borrows/export', [AdminBorrowController::class, 'export'])->name('borrows.export');
        Route::match(['post', 'patch'], '/borrows/{borrow}/approve', [AdminBorrowController::class, 'approve'])->name('borrows.approve');
        Route::match(['post', 'patch'], '/borrows/{borrow}/reject', [AdminBorrowController::class, 'reject'])->name('borrows.reject');
        Route::match(['post', 'patch'], '/borrows/{borrow}/returned', [AdminBorrowController::class, 'markReturned'])->name('borrows.markReturned');

        // User Management
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

    });

    // Faculty Routes
    Route::middleware(['role:faculty'])->prefix('faculty')->name('faculty.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/resources', [FacultyResourceController::class, 'index'])->name('resources.index');
        Route::get('/borrows/create/{resource}', [FacultyBorrowController::class, 'create'])->name('borrows.create');
        Route::post('/borrows', [FacultyBorrowController::class, 'store'])->name('borrows.store');
        Route::get('/history', [FacultyBorrowController::class, 'history'])->name('history');
        Route::get('/history/export', [FacultyBorrowController::class, 'export'])->name('history.export');
    });

    // Route for polling approved items (any authenticated user)
    Route::get('/faculty/approved-check', function () {
        $count = \App\Models\Borrow::where('user_id', auth()->id())->where('status', 'approved')
            ->where(function($q) { $q->whereNull('due_at')->orWhere('due_at', '>', now()); })
            ->count();
        $items = \App\Models\Borrow::with('resource')->where('user_id', auth()->id())->where('status', 'approved')
            ->where(function($q) { $q->whereNull('due_at')->orWhere('due_at', '>', now()); })
            ->get()->map(function ($b) { return $b->quantity . 'x ' . $b->resource->name; });
        return response()->json(['count' => $count, 'items' => $items]);
    })->middleware(['auth'])->name('faculty.approved-check');

    // Route for polling overdue items (any authenticated user)
    Route::get('/faculty/overdue-check', function () {
        $now = now();
        $count = \App\Models\Borrow::where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_at', '<', $now)
            ->count();
        $items = \App\Models\Borrow::with('resource')->where('user_id', auth()->id())
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_at', '<', $now)
            ->get()->map(function ($b) {
                return [
                    'name' => $b->quantity . 'x ' . $b->resource->name,
                    'due_at' => $b->due_at ? \Carbon\Carbon::parse($b->due_at)->setTimezone('Asia/Manila')->format('M d, Y h:i A') : null,
                ];
            });
        return response()->json(['count' => $count, 'items' => $items]);
    })->middleware(['auth'])->name('faculty.overdue-check');
});

// Registration → verify prompt (guest only, no auth needed)
Route::get('/register/verify-prompt', function () {
    $email = session('email');
    if (!$email) {
        return redirect()->route('register');
    }
    return view('auth.verify-prompt', compact('email'));
})->name('register.verify-prompt');

require __DIR__.'/auth.php';
