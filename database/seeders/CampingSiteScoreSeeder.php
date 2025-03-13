<?php

namespace Database\Seeders;

use App\Models\CampingSite;
use App\Models\CampingSiteScore;
use App\Models\Criteria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampingSiteScoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Pastikan ada beberapa camping sites & kriteria
        $campingSites = CampingSite::factory(5)->create();
        $criterias = Criteria::factory(4)->create(); // Misal, ada 4 kriteria

        foreach ($campingSites as $site) {
            foreach ($criterias as $criteria) {
                CampingSiteScore::factory()->create([
                    'camping_site_id' => $site->id,
                    'criterion_id' => $criteria->id,
                ]);
            }
        }
    }
}
