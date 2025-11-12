<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Zone;

class Matche extends Model
{
    use HasFactory;

     protected $fillable = [
        'team_one_title',
        'team_one_image',
        'team_two_title',
        'team_two_image',
        'match_date',
        'stadium',
        'description',
    ];

    protected $casts = [
        'match_date' => 'datetime',
    ];

    
     public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
