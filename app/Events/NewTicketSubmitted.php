<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTicketSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $ticketRefNum,
        public string $requesterName,
        public string $requesterType,
        public string $categoryName,
        public string $issueName,
        public string $priorityName,
        public string $ticketStatus,
        public string $createdAt
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('tickets'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new-ticket';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket_ref_num' => $this->ticketRefNum,
            'requester_name' => $this->requesterName,
            'requester_type' => $this->requesterType,
            'category_name' => $this->categoryName,
            'issue_name' => $this->issueName,
            'priority_name' => $this->priorityName,
            'ticket_status' => $this->ticketStatus,
            'created_at' => $this->createdAt,
        ];
    }
}
