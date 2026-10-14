<?php
namespace Database\Factories;
use App\Models\Category;
use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;
    public function definition(): array
    {
        return [
            'category_name' => fake()->unique()->word() . ' Category',
            'description' => fake()->sentence(),
            'icon' => 'ti ti-folder',
            'color_id' => Color::factory(),
            'status' => 'Active',
        ];
    }
}
