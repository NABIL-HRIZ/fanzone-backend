<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Matche;
use App\Models\Reservation;


class Zone extends Model
{
    use HasFactory;

     protected $fillable = [
        'match_id',
        'name',
        'city',
        'address',
        'latitude',
        'longitude',
        'capacity',
        'available_seats',
        'type',
        'description',
        'image',
    ];

     

    public function match()
    {
        return $this->belongsTo(Matche::class);
    }

    public function tickets()
{
    return $this->hasMany(Reservation::class, 'zone_id');
}
}
