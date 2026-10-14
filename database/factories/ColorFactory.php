<?php
namespace Database\Factories;
use App\Models\Color;
use Illuminate\Database\Eloquent\Factories\Factory;

class ColorFactory extends Factory
{
    protected $model = Color::class;
    public function definition(): array
    {
        $name = fake()->randomElement(['Red', 'Blue', 'Green', 'Yellow', 'Purple', 'Gray']);
        return ['color_name' => $name, 'color_value' => '#' . substr(md5($name), 0, 6)];
    }
}
