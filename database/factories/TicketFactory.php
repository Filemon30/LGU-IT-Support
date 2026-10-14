<?php
namespace Database\Factories;
use App\Models\Ticket;
use App\Models\Requester;
use App\Models\Issue;
use App\Models\PriorityLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;
    public function definition(): array
    {
        return [
            'ticket_ref_num' => 'TKT-' . date('Y') . '-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'requester_id' => Requester::factory(),
            'issue_id' => Issue::factory(),
            'description' => fake()->paragraph(),
            'priority_level_id' => PriorityLevel::factory(),
            'assigned_to' => null,
            'ticket_status' => 'Pending',
        ];
    }
    public function pending(): static { return $this->state(fn() => ['ticket_status' => 'Pending']); }
    public function confirmed(): static { return $this->state(fn() => ['ticket_status' => 'Confirmed']); }
    public function inProgress(): static { return $this->state(fn() => ['ticket_status' => 'On Progress']); }
    public function resolved(): static { return $this->state(fn() => ['ticket_status' => 'Resolved', 'resolved_at' => now()]); }
    public function cancelled(): static { return $this->state(fn() => ['ticket_status' => 'Cancelled', 'cancelled_at' => now()]); }
    public function softDeleted(): static { return $this->state(fn() => ['ticket_status' => 'Soft Delete']); }
}
