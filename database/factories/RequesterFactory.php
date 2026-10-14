<?php
namespace Database\Factories;
use App\Models\Requester;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequesterFactory extends Factory
{
    protected $model = Requester::class;
    public function definition(): array
    {
        return [
            'requester_type' => 'Barangay',
            'barangay_id' => null,
            'division_id' => null,
            'status' => 'Active',
        ];
    }
    public function barangay(): static { return $this->state(fn() => ['requester_type' => 'Barangay']); }
    public function officeDivision(): static { return $this->state(fn() => ['requester_type' => 'Office Division']); }
}
