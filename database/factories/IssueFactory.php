<?php
namespace Database\Factories;
use App\Models\Issue;
use App\Models\Category;
use App\Models\PriorityLevel;
use Illuminate\Database\Eloquent\Factories\Factory;

class IssueFactory extends Factory
{
    protected $model = Issue::class;
    public function definition(): array
    {
        return [
            'category_id' => Category::factory(),
            'issue_ref_num' => 'ISS-' . strtoupper(uniqid()),
            'description' => fake()->unique()->sentence(4),
            'default_priority_level_id' => PriorityLevel::factory(),
            'status' => 'Active',
        ];
    }
}
