<?php

namespace Database\Factories;

use App\Models\Criteria;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Criteria>
 */
class CriteriaFactory extends Factory
{
    protected $model = Criteria::class;

    private static $aspects = [ 'Keamanan',
        'Kenyamanan',
        'Kebersihan',
        'Kemudahan Transportasi'];

    private static $index = 0;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if(self::$index >= count(self::$aspects)){
            return [];
        }
        return [
            'name' => self::$aspects[self::$index++], // Nama kriteria
            //
        ];
    }
}
