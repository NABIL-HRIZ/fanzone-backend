<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Matche;

class MatchReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $email;
    public Matche $match;

   
    public function __construct(Matche $match)
    {
        $this->match = $match;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Match Reminder Mail',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.match_reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        
        return $this->subject("Rappel : Match à venir")
                    ->markdown('emails.match_reminder');
    }
}