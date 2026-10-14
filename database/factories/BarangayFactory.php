<?php
namespace Database\Factories;
use App\Models\Barangay;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangayFactory extends Factory
{
    protected $model = Barangay::class;
    public function definition(): array
    {
        return [
            'barangay_ref_num' => 'BRGY-' . str_pad(fake()->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
            'barangay_name' => fake()->unique()->city() . ' Barangay',
            'secret_key_hash' => strtoupper(bin2hex(random_bytes(8))),
            'key_status' => 'Active',
        ];
    }
}
