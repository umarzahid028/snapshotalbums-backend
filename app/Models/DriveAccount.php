<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriveAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'drive_name',
        'drive_email',
        'google_id',
        'avatar',
        'google_token',
        'google_refresh_token',
        'google_token_expires_in',
        'access_token',
        'json_token',
        'drive_storage',
        'used_storage',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function album()
    {
        return $this->hasOne(Album::class, 'user_id', 'user_id');
    }
}
