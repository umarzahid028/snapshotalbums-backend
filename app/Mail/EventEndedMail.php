<?php

namespace App\Mail;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventEndedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $album;

    /**
     * Create a new message instance.
     *
     * @param Album $album
     */
    public function __construct(Album $album)
    {
        $this->album = $album;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Event "' . $this->album->event_title . '" Has Concluded!')
            ->view('emails.event-ended')
            ->with([
                'album' => $this->album,
            ]);
    }
}
