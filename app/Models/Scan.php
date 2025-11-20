<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Reservation;


class Scan extends Model
{
    use HasFactory;

        protected $fillable = [
            'agent_id',
            'ticket_id',
            'scan_time',
            'scan_status',
        ];

        public function agent()
        {
            return $this->belongsTo(User::class, 'agent_id');
        }

        public function ticket()
        {
            return $this->belongsTo(\App\Models\Reservation::class, 'ticket_id');
        }

}
