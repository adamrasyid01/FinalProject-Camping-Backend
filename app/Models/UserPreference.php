<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreference extends Model
{
    use HasFactory, SOftDeletes;
    protected $fillable = [
        'user_id',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function userPreferenceCriterias() {
        return $this->hasMany(UserPreferenceCriteria::class, 'user_preference_id');
    }
}
