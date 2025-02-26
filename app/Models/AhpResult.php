<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AhpResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'camping_site_id',
        'final_score',
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    public function campingSite(){
        return $this->belongsTo(CampingSite::class, 'camping_site_id');
    }
}
