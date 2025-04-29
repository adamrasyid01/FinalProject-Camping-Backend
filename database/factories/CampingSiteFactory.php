<?php

namespace Database\Factories;

use App\Models\CampingLocation;
use App\Models\CampingSite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CampingSite>
 */
class CampingSiteFactory extends Factory
{
    protected $model = CampingSite::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'name' => $this->faker->word . 'Camp',
            'location' => $this->faker->city . ', Jawa Timur', // ✅ Tambahkan ini
            'location_id' => CampingLocation::factory(),
            'image_url' => $this->faker->imageUrl(), // URL gambar acak
            'rating' => $this->faker->randomFloat(1, 3, 5), // Rating antara 3.0 - 5.0
        ];
    }
}
