<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriveAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'drive_name',
        'drive_email',
        'google_id',
        'avatar',
        'google_token',
        'google_refresh_token',
        'google_token_expires_in',
        'access_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
