<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampingLocation extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name',
        'image_url',
        'total_camps',
    ];
    public function campingSites() {
        return $this->hasMany(CampingSite::class, 'location_id');
    }
}
