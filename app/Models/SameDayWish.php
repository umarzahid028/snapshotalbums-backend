<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SameDayWish extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'icon',
        'messages',
        'image',
        'is_active',
        'order',
    ];

    protected $casts = [
        'messages' => 'array',
        'is_active' => 'boolean',
    ];
}
