<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampingSite extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location_id',
        'image_url',
        'rating',
        'link',
        'reviews',
        'phone',
        'location'
    ];
    protected $casts = [
        'text_reviews' => 'array',
        'total_sentimen' => 'array',
    ];

    public function bookmarkedbyUsers()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'camping_site_id', 'user_id');
    }
    public function campingLocation()
    {
        return $this->belongsTo(CampingLocation::class, 'location_id');
    }
    public function campingSiteScores()
    {
        return $this->hasMany(CampingSiteScore::class, 'camping_site_id');
    }
    public function ahpResults()
    {
        return $this->hasMany(AhpResult::class, 'camping_site_id');
    }
}
