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
        'matche_id',
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
        'price'
    ];

     

    public function match()
    {
        
        return $this->belongsTo(Matche::class, 'matche_id');
    }

    public function tickets()
{
    return $this->hasMany(Reservation::class, 'zone_id');
}
}
