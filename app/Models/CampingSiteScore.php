<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampingSiteScore extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'camping_sites_id',
        'criterion_id',
        'sentiment_percentage',
        'ahp_score',
    ];

    public function criterias(){
        return $this->belongsToMany(Criteria::class, 'criterion_id');
    }
    public function campingSites(){
        return $this->belongsToMany(CampingSite::class, 'camping_site_id');
    }
}
