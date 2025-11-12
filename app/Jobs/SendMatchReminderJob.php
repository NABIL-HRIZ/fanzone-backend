<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Matche;
use Illuminate\Support\Facades\Mail;
use App\Mail\MatchReminderMail;

class SendMatchReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $email;
    public Matche $match;

    public function __construct(string $email, Matche $match)
    {
        $this->email = $email;
        $this->match = $match;
    }

    public function handle(): void
    {
        
        $mail = new MatchReminderMail($this->match);
        
       
        Mail::to($this->email)->send($mail);
    }
}