<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;   // NOTE: WelcomeMail (pas WelcomeEmail)
use App\Models\User;

class SendWelcomeEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // ------ Déclarer la propriété ici (publique pour la sérialisation) ------
    public User $user; // (ou `public $user;` si tu préfères sans typage)

    /**
     * Create a new job instance.
     */
    public function __construct(User $user)
    {
        // on assigne la propriété dans le constructeur
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Attention au nom du Mailable — ici WelcomeMail
        Mail::to($this->user->email)->send(new WelcomeMail($this->user));
    }
}
