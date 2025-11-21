<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Reservation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Mail',
        );
    }

    /**
     * Get the message content definition.
     */
 

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

      public function build()
    {
        $qrCode = QrCode::format('svg')->size(200)->generate('reservation_' . $this->reservation->id);

        return $this->subject("Ticket de réservation FanZone")
                    ->view('ticket', [
                        'reservation' => $this->reservation,
                        'qrCode' => $qrCode,
                    ]);
    }
}
