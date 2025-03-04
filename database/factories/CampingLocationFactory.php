<?php

namespace Database\Factories;

use App\Models\CampingLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampingLocation>
 */
class CampingLocationFactory extends Factory
{
    protected $model = CampingLocation::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city(), // Nama kota
            'image_url' => "https://picsum.photos/640/480?random=" . $this->faker->unique()->numberBetween(1, 1000),
            'total_camps' => $this->faker->numberBetween(1, 10), // Jumlah camp random
        ];
    }
}
