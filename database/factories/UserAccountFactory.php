<?php
namespace Database\Factories;
use App\Models\UserAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserAccountFactory extends Factory
{
    protected $model = UserAccount::class;
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'password_hash' => Hash::make('password'),
            'last_login_at' => null,
        ];
    }
}
