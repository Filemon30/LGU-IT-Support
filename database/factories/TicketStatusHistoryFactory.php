<?php
namespace Database\Factories;
use App\Models\TicketStatusHistory;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketStatusHistoryFactory extends Factory
{
    protected $model = TicketStatusHistory::class;
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'old_status' => 'Pending',
            'new_status' => 'Confirmed',
            'changed_by' => User::factory(),
            'remarks' => fake()->sentence(),
        ];
    }
}
