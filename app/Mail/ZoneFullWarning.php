<?php

namespace App\Mail;

use App\Models\Zone;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ZoneFullWarning extends Mailable
{
    use Queueable, SerializesModels;

    public $zone;
    public $type;

    public function __construct(Zone $zone, $type = 'full')
    {
        $this->zone = $zone;
        $this->type = $type;
    }

    public function build()
    {
        $subject = $this->type === 'full'
            ? "⚠️ Zone \"{$this->zone->name}\" est COMPLÈTE"
            : "⚠️ Zone \"{$this->zone->name}\" est presque pleine";

        return $this->subject($subject)
                    ->view('emails.zone_warning');
    }
}
