<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomePageContent extends Model
{
    protected $fillable = [
        'section',
        'content',
        'is_active',
        'order'
    ];

    protected $casts = [
        'content' => 'array',
        'is_active' => 'boolean'
    ];
}
