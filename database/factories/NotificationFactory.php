<?php
namespace Database\Factories;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;
    public function definition(): array
    {
        return [
            'notification_ref_num' => 'NOTIF-' . str_pad(fake()->unique()->numberBetween(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'user_id' => User::factory(),
            'notification_title' => fake()->sentence(3),
            'notification_message' => fake()->sentence(),
            'target_url' => null,
            'notification_status' => 'Unread',
            'read_at' => null,
        ];
    }
    public function read(): static { return $this->state(fn() => ['notification_status' => 'Read', 'read_at' => now()]); }
    public function unread(): static { return $this->state(fn() => ['notification_status' => 'Unread', 'read_at' => null]); }
}
