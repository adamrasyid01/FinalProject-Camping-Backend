<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Criteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function userPreferenceCriterias() {
        return $this->hasMany(UserPreferenceCriteria::class, 'criteria_id');
    }
    public function campingSiteScores(){
        return $this->belongsToMany(CampingSiteScore::class, 'criterion_id');
    }
}
