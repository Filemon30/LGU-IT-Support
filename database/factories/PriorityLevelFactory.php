<?php
namespace Database\Factories;
use App\Models\PriorityLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriorityLevelFactory extends Factory
{
    protected $model = PriorityLevel::class;
    public function definition(): array
    {
        $name = fake()->randomElement(['Critical', 'High', 'Medium', 'Low']);
        return [
            'priority_name' => $name,
            'priority_description' => $name . ' priority level',
            'response_target_minutes' => match($name) { 'Critical' => 15, 'High' => 60, 'Medium' => 240, 'Low' => 480, default => 240 },
            'resolution_target_minutes' => match($name) { 'Critical' => 240, 'High' => 480, 'Medium' => 1440, 'Low' => 2880, default => 1440 },
        ];
    }
    public function critical(): static { return $this->state(fn() => ['priority_name' => 'Critical']); }
    public function high(): static { return $this->state(fn() => ['priority_name' => 'High']); }
    public function medium(): static { return $this->state(fn() => ['priority_name' => 'Medium']); }
    public function low(): static { return $this->state(fn() => ['priority_name' => 'Low']); }
}
