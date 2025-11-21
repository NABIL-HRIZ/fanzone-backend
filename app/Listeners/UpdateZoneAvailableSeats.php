<?php

namespace App\Listeners;

use App\Events\ZoneSeatsUpdated;
use App\Mail\ZoneFullWarning;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UpdateZoneAvailableSeats
{
 public function handle(ZoneSeatsUpdated $event)
    {
        $zone = $event->zone;

        $zone->available_seats -= $event->quantity;
    
        $zone->save();
    }
}
