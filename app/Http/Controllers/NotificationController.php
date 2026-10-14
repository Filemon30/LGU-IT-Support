<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', session('user_id'))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $unreadCount = Notification::where('user_id', session('user_id'))
            ->where('notification_status', 'Unread')
            ->count();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount,
            ]);
        }

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('notification_id', $id)
            ->where('user_id', session('user_id'))
            ->firstOrFail();

        $notification->update([
            'notification_status' => 'Read',
            'read_at' => now(),
        ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read.',
            ]);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', session('user_id'))
            ->where('notification_status', 'Unread')
            ->update([
                'notification_status' => 'Read',
                'read_at' => now(),
            ]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read.',
            ]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function getUnreadCount()
    {
        $count = Notification::where('user_id', session('user_id'))
            ->where('notification_status', 'Unread')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
        ]);
    }
}
