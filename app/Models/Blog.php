<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'excerpt',
        'author',
        'author_email',
        'tags',
        'status',
        'category',
        'image',
        'meta_title',
        'meta_description',
    ];

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($blog) {
    //         if (empty($blog->slug)) {
    //             $blog->slug = Str::slug($blog->title);
    //         }
    //     });
    // }
}
