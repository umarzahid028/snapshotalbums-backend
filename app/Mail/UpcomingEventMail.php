<?php

namespace App\Mail;

use App\Models\Album;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class UpcomingEventMail extends Mailable
{
    use Queueable, SerializesModels;

    public $album;
    public $daysUntil;

    /**
     * Create a new message instance.
     *
     * @param Album $album
     * @param int $daysUntil
     */
    public function __construct(Album $album, int $daysUntil)
    {
        $this->album = $album;
        $this->daysUntil = $daysUntil;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reminder: Your Event "' . $this->album->event_title . '" is Coming Up!')
            ->view('emails.upcoming-event')
            ->with([
                'album' => $this->album,
                'daysUntil' => $this->daysUntil,
            ]);
    }
}
