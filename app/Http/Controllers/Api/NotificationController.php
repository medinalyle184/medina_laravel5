<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SampleNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class NotificationController extends Controller
{
    /**
     * Send a notification to a user
     */
    public function sendNotification(Request $request)
    {
        $user = $request->user() ?? User::find(1);

        $title = $request->input('title', 'New Notification');
        $message = $request->input('message', 'This is a sample notification');

        $user->notify(new SampleNotification($title, $message));

        return response()->json([
            'message' => 'Notification sent successfully',
            'user_id' => $user->id,
        ], 201);
    }

    /**
     * Get all notifications for the authenticated user
     */
    public function getNotifications(Request $request)
    {
        $notifications = $request->user()->notifications;

        return response()->json([
            'total' => $notifications->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Get only unread notifications
     */
    public function getUnreadNotifications(Request $request)
    {
        $unreadNotifications = $request->user()->unreadNotifications;

        return response()->json([
            'total' => $unreadNotifications->count(),
            'unread_notifications' => $unreadNotifications,
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id, Request $request)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => $notification,
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        $count = $request->user()->unreadNotifications->count();
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'All notifications marked as read',
            'count' => $count,
        ]);
    }

    /**
     * Delete a notification
     */
    public function deleteNotification($id, Request $request)
    {
        $notification = $request->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if (!$notification) {
            return response()->json([
                'message' => 'Notification not found',
            ], 404);
        }

        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultiple(Request $request)
    {
        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'string|default:"Notification"',
            'message' => 'string|default:"New notification"',
        ]);

        $users = User::whereIn('id', $validated['user_ids'])->get();
        $title = $validated['title'] ?? 'Notification';
        $message = $validated['message'] ?? 'New notification';

        Notification::send($users, new SampleNotification($title, $message));

        return response()->json([
            'message' => 'Notification sent to ' . count($users) . ' users',
            'users_count' => count($users),
        ], 201);
    }
}
