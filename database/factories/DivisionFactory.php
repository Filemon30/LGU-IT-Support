<?php
namespace Database\Factories;
use App\Models\Division;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

class DivisionFactory extends Factory
{
    protected $model = Division::class;
    public function definition(): array
    {
        return [
            'office_id' => Office::factory(),
            'division_ref_num' => 'DIV-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'division_name' => fake()->unique()->words(2, true) . ' Division',
            'secret_key_hash' => strtoupper(bin2hex(random_bytes(8))),
            'key_status' => 'Active',
            'status' => 'Active',
        ];
    }
}
