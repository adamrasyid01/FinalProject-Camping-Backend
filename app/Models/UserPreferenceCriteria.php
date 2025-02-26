<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreferenceCriteria extends Model
{
    use HasFactory, SoftDeletes;

    public function userPreferences() {
        return $this->belongsToMany(UserPreference::class, 'user_preference_id');
    }
    public function criterias() {
        return $this->belongsToMany(Criteria::class, 'criteria_id');
    }
}
