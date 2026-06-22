<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        $notification->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return back()->with('success', 'All notifications marked as read.');
    }

    public function destroy(Notification $notification)
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }
        $notification->delete();
        return response()->json(['success' => true]);
    }

    public function clearAll()
    {
        Notification::where('user_id', auth()->id())->delete();
        return back()->with('success', 'All notifications cleared.');
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->count();
        return response()->json(['count' => $count]);
    }

    public function latest(Request $request)
    {
        $afterId = $request->integer('after', 0);

        $notifications = Notification::where('user_id', auth()->id())
            ->where('id', '>', $afterId)
            ->latest()
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->title,
                    'message' => $n->message,
                    'is_read' => (bool) $n->is_read,
                    'created_at' => $n->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A'),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::where('user_id', auth()->id())
                ->where('is_read', false)
                ->count(),
        ]);
    }

}
