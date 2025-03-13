<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampingSiteScore extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'camping_site_id',
        'criterion_id',
        'sentiment_percentage',
        'ahp_score',
        'normalized_score',
    ];

    public function criterias()
    {
        return $this->belongsTo(Criteria::class, 'criterion_id');
    }
    public function campingSites()
    {
        return $this->belongsTo(CampingSite::class, 'camping_site_id');
    }

    public static function normalizeScores()
    {
        // Ambil semua data yang dikelompokkan berdasarkan camping_site_id
        $groupedScores = self::select('camping_site_id')
            ->groupBy('camping_site_id')
            ->get();

        foreach ($groupedScores as $group) {
            $scores = self::where('camping_site_id', $group->camping_site_id)->get();
            $totalScore = $scores->sum('ahp_score');

            foreach ($scores as $score) {
                $normalizedScore = ($totalScore != 0) ? $score->ahp_score / $totalScore : 0;
                $score->update(['normalized_score' => $normalizedScore]);
            }
        }
    }
}
