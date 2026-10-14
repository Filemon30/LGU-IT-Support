<?php
namespace Database\Factories;
use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;
    public function definition(): array
    {
        return ['role_name' => 'Staff'];
    }
    public function admin(): static { return $this->state(fn() => ['role_name' => 'Admin']); }
    public function staff(): static { return $this->state(fn() => ['role_name' => 'Staff']); }
}
