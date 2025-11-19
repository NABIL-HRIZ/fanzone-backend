<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Zone;



class Reservation extends Model
{
    use HasFactory;
protected $table = 'reservation_tickets';
  protected $fillable = [
        'user_id',
        'zone_id',
        'number_of_tickets',
        'total_price',
        'payment_status',  
        'qr_code_path',
        'ticket_pdf_path',
        'reservation_date',
        'stripe_payment_intent_id',
        'stripe_session_id',
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function fanZone()
{
   
    return $this->belongsTo(Zone::class, 'zone_id');
}

}
