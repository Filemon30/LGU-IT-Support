<?php
namespace Database\Factories;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeFactory extends Factory
{
    protected $model = Office::class;
    public function definition(): array
    {
        return [
            'office_ref_num' => 'OFF-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'office_name' => fake()->unique()->company() . ' Office',
            'status' => 'Active',
        ];
    }
}
