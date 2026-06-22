<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResourceController;
use App\Http\Controllers\Api\BorrowController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Mobile app API routes for ICCT MIS Flutter app
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->name('api.verification.verify');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Resources
    Route::get('/resources', [ResourceController::class, 'index']);
    Route::get('/resources/{resource}', [ResourceController::class, 'show']);
    Route::post('/resources', [ResourceController::class, 'store'])->middleware('admin');
    Route::put('/resources/{resource}', [ResourceController::class, 'update'])->middleware('admin');
    Route::delete('/resources/{resource}', [ResourceController::class, 'destroy'])->middleware('admin');

    // Borrows
    Route::get('/borrows', [BorrowController::class, 'index']);
    Route::get('/borrows/active', [BorrowController::class, 'active']);
    Route::get('/borrows/history', [BorrowController::class, 'history']);
    Route::post('/borrows', [BorrowController::class, 'store']);
    Route::post('/borrows/{borrow}/approve', [BorrowController::class, 'approve'])->middleware('admin');
    Route::post('/borrows/{borrow}/reject', [BorrowController::class, 'reject'])->middleware('admin');
    Route::post('/borrows/{borrow}/returned', [BorrowController::class, 'markReturned'])->middleware('admin');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);

    // Admin only
    Route::get('/users', [\App\Http\Controllers\Api\UserManagementController::class, 'index'])->middleware('admin');
    Route::get('/users/{user}', [\App\Http\Controllers\Api\UserManagementController::class, 'show'])->middleware('admin');
    Route::put('/users/{user}', [\App\Http\Controllers\Api\UserManagementController::class, 'update'])->middleware('admin');
    Route::delete('/users/{user}', [\App\Http\Controllers\Api\UserManagementController::class, 'destroy'])->middleware('admin');
});
