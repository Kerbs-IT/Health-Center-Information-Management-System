<?php

namespace App\Http\Controllers;

use App\Models\notifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        $page = 'Notification';
        $isActive = true;
        $notifications = notifications::where('user_id', Auth::id())
            ->where("status","!=",'Archived')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $unreadCount = notifications::where('user_id', Auth::id())
            ->unread()
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'page', 'isActive'));
    }

    /**
     * Get unread notifications count (for badge/icon)
     */
    public function getUnreadCount()
    {
        $count = notifications::where('user_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications (for dropdown/bell icon)
     */
    public function getRecent()
    {
        $notifications = notifications::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($notifications);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = notifications::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        notifications::where('user_id', Auth::id())
            ->unread()
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $notification = notifications::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->update([
            'status'=>'Archived'
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead()
    {
        notifications::where('user_id', Auth::id())
            ->read()
            ->delete();

        return response()->json(['success' => true]);
    }
}
