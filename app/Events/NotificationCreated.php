<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $userId,
        public string $notificationTitle,
        public string $notificationMessage,
        public string $refNum,
        public ?string $targetUrl = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('notifications'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'notification-created';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'notification_title' => $this->notificationTitle,
            'notification_message' => $this->notificationMessage,
            'notification_ref_num' => $this->refNum,
            'target_url' => $this->targetUrl,
        ];
    }
}
