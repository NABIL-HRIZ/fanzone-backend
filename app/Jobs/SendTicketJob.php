<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketMail;
use Illuminate\Support\Facades\Log;
class SendTicketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The recipient email.
     *
     * @var string
     */
    public $email;

    /**
     * The reservation model instance.
     *
     * @var \App\Models\Reservation
     */
    public $reservation;

    /**
     * Create a new job instance.
     */
    public function __construct(string $email, Reservation $reservation)
    {
        $this->email = $email;
        $this->reservation = $reservation;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SendTicketJob running', ['email' => $this->email, 'reservation_id' => $this->reservation->id ?? null]);

        // Using the Mail facade to send the TicketMail mailable.
        try {
            Mail::to($this->email)->send(new TicketMail($this->reservation));
            Log::info('SendTicketJob mail sent', ['email' => $this->email, 'reservation_id' => $this->reservation->id ?? null]);
        } catch (\Exception $e) {
            Log::error('SendTicketJob mail failed', ['email' => $this->email, 'reservation_id' => $this->reservation->id ?? null, 'error' => $e->getMessage()]);
            // rethrow so queue worker can mark job failed if desired
            throw $e;
        }
    }
}
