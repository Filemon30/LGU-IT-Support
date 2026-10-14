<?php

namespace App\Traits;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;

trait SendsNotifications
{
    private function createNotification($userId, $title, $message, $targetUrl = null, $broadcast = true)
    {
        $refNum = 'NOTIF-'.str_pad(random_int(1, 9999999999), 10, '0', STR_PAD_LEFT);

        $notification = Notification::create([
            'notification_ref_num' => $refNum,
            'user_id' => $userId,
            'notification_title' => $title,
            'notification_message' => $message,
            'target_url' => $targetUrl,
            'notification_status' => 'Unread',
        ]);

        if ($broadcast) {
            event(new NotificationCreated(
                $userId,
                $title,
                $message,
                $refNum,
                $targetUrl,
            ));
        }

        return $notification;
    }

    private function notifyAllAdmins($title, $message, $excludeUserId = null, $targetUrl = null)
    {
        $adminRoleIds = Role::whereIn('role_name', ['Admin', 'Super Admin'])->pluck('role_id');

        $adminIds = User::whereIn('role_id', $adminRoleIds)
            ->where('status', 'Active')
            ->when($excludeUserId, fn ($q) => $q->where('user_id', '!=', $excludeUserId))
            ->pluck('user_id');

        foreach ($adminIds as $userId) {
            $this->createNotification($userId, $title, $message, $targetUrl, false);
        }

        event(new NotificationCreated(
            0,
            $title,
            $message,
            'NOTIF-BROADCAST',
            $targetUrl,
        ));
    }
}
