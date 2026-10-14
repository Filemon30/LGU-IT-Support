<?php
namespace Database\Factories;
use App\Models\UserInformation;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserInformationFactory extends Factory
{
    protected $model = UserInformation::class;
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'middle_name' => fake()->optional()->firstName(),
            'suffix' => null,
            'gender' => fake()->randomElement(['Male', 'Female']),
            'birth_date' => fake()->date('Y-m-d', '2000-01-01'),
            'barangay' => fake()->city(),
            'city' => 'Biringan City',
            'province' => 'Sorsogon',
            'contact_number' => fake()->numerify('09#########'),
        ];
    }
}
