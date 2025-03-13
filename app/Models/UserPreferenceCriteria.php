<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreferenceCriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_preference_id', 'criteria_id', 'weight', 'normalized_weight'];
    public function userPreference()
    {
        return $this->belongsTo(UserPreference::class, 'user_preference_id');
    }
    public function criteria()
    {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }
    public static function normalizeWeights($user_preference_id)
    {
        $kriteria = self::where('user_preference_id', $user_preference_id)->get();
        $totalWeight = $kriteria->sum('weight');

        foreach ($kriteria as $item) {
            $normalizedWeight = ($totalWeight > 0) ? $item->weight / $totalWeight : 0;
            $item->update(['normalized_weight' => $normalizedWeight]);
        }
    }
}
