<?php
namespace Database\Factories;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserInformation;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;
    public function definition(): array
    {
        return [
            'staff_ref_num' => '2026' . str_pad(fake()->unique()->numberBetween(10000000, 99999999), 8, '0', STR_PAD_LEFT),
            'status' => 'Active',
        ];
    }
    public function active(): static { return $this->state(fn() => ['status' => 'Active']); }
    public function disabled(): static { return $this->state(fn() => ['status' => 'Disabled']); }
    public function softDeleted(): static { return $this->state(fn() => ['status' => 'Soft Delete']); }
}
