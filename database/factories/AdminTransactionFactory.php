<?php
namespace Database\Factories;
use App\Models\AdminTransaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminTransactionFactory extends Factory
{
    protected $model = AdminTransaction::class;
    public function definition(): array
    {
        return [
            'admin_transaction_ref' => str_pad(fake()->unique()->numberBetween(1, 9999999999), 10, '0', STR_PAD_LEFT),
            'handled_by' => User::factory(),
            'action_type' => 'Ticket Updated',
            'entity_type' => 'Ticket',
            'entity_id' => 1,
            'description' => fake()->sentence(),
            'old_values' => null,
            'new_values' => null,
        ];
    }
}
