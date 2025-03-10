<?php

namespace App\Models;

use App\Helpers\ResponseFormatter;
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

    public function getLocationWithSites($id){
        $location = CampingLocation::with('campingSites')->find($id);

        if(!$location){
           ResponseFormatter::error('Data lokasi perkemahan tidak ditemukan', 404);
        }
        return ResponseFormatter::success($location, 'Data lokasi perkemahan berhasil diambil');
    }
}
