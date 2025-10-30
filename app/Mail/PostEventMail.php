<?php

namespace App\Mail;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PostEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public Album $album;

    /**
     * Create a new message instance.
     */
    public function __construct(Album $album)
    {
        $this->album = $album;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Check Out All the Photos from "' . $this->album->event_title . '"!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.post-event',
            with: [
                'album' => $this->album,
                'eventUrl' => config('app.frontend_url') . '/event/' . $this->album->id,
                'qrCode' => $this->album->qrCode,
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
