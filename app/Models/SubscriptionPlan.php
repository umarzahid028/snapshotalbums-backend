<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'no_of_ablums',
        'price',
        'duration_days',
        'features',
        'is_active',
        'is_popular',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
        'is_popular' => 'boolean',
    ];

    // Boot method to handle model events
    protected static function booted()
    {
        static::creating(function ($plan) {
            // Generate slug from name if not already set
            if (empty($plan->slug)) {
                $plan->slug = Str::slug($plan->name);

                // Optional: ensure uniqueness
                $originalSlug = $plan->slug;
                $counter = 1;
                while (self::where('slug', $plan->slug)->exists()) {
                    $plan->slug = $originalSlug . '-' . $counter++;
                }
            }
        });
    }
}
