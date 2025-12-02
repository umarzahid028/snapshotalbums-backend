<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventType;

class EventTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eventTypes = [
            [
                'title' => 'Weddings',
                'icon' => 'Heart',
                'description' => 'Capture every precious moment of your special day',
                'image' => 'https://images.pexels.com/photos/265856/pexels-photo-265856.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop',
                'features' => [
                    'Ceremony photos',
                    'Reception moments',
                    'Guest interactions',
                    'Behind-the-scenes'
                ],
                'is_active' => true,
                'order' => 1
            ],
            [
                'title' => 'Birthdays',
                'icon' => 'Cake',
                'description' => 'Celebrate life\'s milestones with friends and family',
                'image' => 'https://images.pexels.com/photos/140831/pexels-photo-140831.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop',
                'features' => [
                    'Party photos',
                    'Cake cutting',
                    'Gift opening',
                    'Fun moments'
                ],
                'is_active' => true,
                'order' => 2
            ],
            [
                'title' => 'Anniversaries',
                'icon' => 'Gift',
                'description' => 'Mark another year of love and togetherness',
                'image' => 'https://images.pexels.com/photos/1024993/pexels-photo-1024993.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop',
                'features' => [
                    'Romantic moments',
                    'Celebration photos',
                    'Memories',
                    'Special occasions'
                ],
                'is_active' => true,
                'order' => 3
            ],
            [
                'title' => 'Corporate Events',
                'icon' => 'Users',
                'description' => 'Professional gatherings and team celebrations',
                'image' => 'https://images.pexels.com/photos/1181391/pexels-photo-1181391.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop',
                'features' => [
                    'Team photos',
                    'Event coverage',
                    'Professional shots',
                    'Networking moments'
                ],
                'is_active' => true,
                'order' => 4
            ]
        ];

        foreach ($eventTypes as $eventType) {
            EventType::updateOrCreate(
                ['title' => $eventType['title']],
                $eventType
            );
        }
    }
}
