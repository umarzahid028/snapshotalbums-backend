<?php

namespace App\Mail;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public Album $album;
    public $daysUntilEvent;

    /**
     * Create a new message instance.
     */
    public function __construct(Album $album, int $daysUntilEvent = 7)
    {
        $this->album = $album;
        $this->daysUntilEvent = $daysUntilEvent;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Event "' . $this->album->event_title . '" is ' . $this->daysUntilEvent . ' Days Away!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event-reminder',
            with: [
                'album' => $this->album,
                'daysUntilEvent' => $this->daysUntilEvent,
                'eventUrl' => config('app.frontend_url') . '/event/' . $this->album->id,
                'dashboardUrl' => config('app.frontend_url') . '/dashboard',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
