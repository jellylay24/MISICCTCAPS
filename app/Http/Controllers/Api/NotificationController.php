<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $user = request()->user();

        $notifications = $user->notifications()->latest()->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'type' => class_basename($notification->type),
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'created_at' => $notification->created_at,
            ];
        });

        return response()->json($notifications);
    }

    public function unreadCount(): JsonResponse
    {
        $count = request()->user()->unreadNotifications()->count();

        return response()->json(['count' => $count]);
    }

    public function markRead($notification): JsonResponse
    {
        $notif = request()->user()->notifications()->findOrFail($notification);
        $notif->markAsRead();

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllRead(): JsonResponse
    {
        request()->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
