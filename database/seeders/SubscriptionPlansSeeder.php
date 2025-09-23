<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubscriptionPlansSeeder extends Seeder
{
    public function run()
    {
        DB::table('subscription_plans')->insert([
            [
                'name' => 'Basic',
                'slug' => Str::slug('Basic'),
                'no_of_ablums' => 1,
                'price' => 5.99,
                'description' => 'The basic level allows for full functionality of Snapshot Albums, but limits album creation to one. If you need access to create more than one album, consider the Premium level.',
                'is_popular' => 0,
                'duration_days' => 30,
                'features' => json_encode([
                    'Create one album',
                    'Custom album background image',
                    'Unlimited guest uploaders',
                    'Downloadable QR code generated for guests',
                    'User-friendly dashboard',
                    'Album never expires',
                    'Get started now',
                ]),
                'is_active' => 1,
            ],
            [
                'name' => 'Premium',
                'slug' => Str::slug('Premium'),
                'no_of_ablums' => null, // unlimited
                'price' => 9.99,
                'description' => 'Perfect for users that want to create albums for all of their events, or need different QR codes to keep their guest\'s uploads organized. A must have for Event Planners, Venues, Churches or anyone that hosts events. Collect guest\'s photos and videos and keep them organized!',
                'is_popular' => 1,
                'duration_days' => 30,
                'features' => json_encode([
                    'Create unlimited albums',
                    'Make your albums public for guests to see',
                    '10+ Printable Canva™ QR code templates',
                    'Cancel anytime',
                    'Unlimited guest uploaders',
                    'Custom album background images',
                    'Downloadable QR code generated for guests',
                    'User-friendly dashboard',
                    'Albums never expire',
                ]),
                'is_active' => 1,
            ],
        ]);
    }
}
