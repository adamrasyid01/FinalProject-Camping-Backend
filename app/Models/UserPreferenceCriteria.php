<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreferenceCriteria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_preference_id', 'criteria_id', 'weight'];
    public function userPreference() {
        return $this->belongsTo(UserPreference::class, 'user_preference_id');
    }
    public function criteria() {
        return $this->belongsTo(Criteria::class, 'criteria_id');
    }
}
