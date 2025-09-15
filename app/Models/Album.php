<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_title',
        'event_type',
        'event_time',
        'location',
        'event_description',
        'max_photos_per_guest',
        'custom_welcome_message',
        'privacy_level',
        'allow_guest_uploads',
        'google_drive_folder_name',
        'google_drive_folder_id',
        'event_date',
        'status',
        'qrCode',
        'total_guests',
        'total_files',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function generateQrCode($album): string
    {
        $titlePart = strtoupper(substr(preg_replace('/\s+/', '', $album->event_title), 0, 3));

        $typePart = strtoupper(substr(preg_replace('/\s+/', '', $album->event_type ?? 'EV'), 0, 2));

        $uniqueNumber = random_int(1000, 9999);

        return $titlePart . $typePart .'-'. $uniqueNumber;
    }
}
