<?php
namespace Database\Factories;

use App\Models\CampingSite;
use App\Models\Criteria;
use App\Models\CampingSiteScore;
use Illuminate\Database\Eloquent\Factories\Factory;

class CampingSiteScoreFactory extends Factory
{
    protected $model = CampingSiteScore::class;

    public function definition()
    {
        // Pastikan ada data di tabel terkait
        $campingSite = CampingSite::inRandomOrder()->first() ?? CampingSite::factory()->create();
        $criterion = Criteria::inRandomOrder()->first() ?? Criteria::factory()->create();

        // Generate sentiment_percentage (-100 sampai 100)
        $sentimentPercentage = $this->faker->randomFloat(2, -100, 100);

        // Hitung ahp_score berdasarkan sentiment_percentage
        $ahpScore = $this->calculateAhpScore($sentimentPercentage);

        return [
            'camping_site_id' => $campingSite->id,
            'criterion_id' => $criterion->id,
            'sentiment_percentage' => $sentimentPercentage,
            'ahp_score' => $ahpScore,
        ];
    }

    // ✅ Optimasi fungsi untuk menentukan ahp_score berdasarkan sentiment_percentage
    private function calculateAhpScore($sentiment)
    {
        return max(min(floor($sentiment / 10), 10), -9); // Optimasi dengan floor()
    }
}
