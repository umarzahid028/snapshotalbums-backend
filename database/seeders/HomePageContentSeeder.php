<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HomePageContent;

class HomePageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contents = [
            [
                'section' => 'hero',
                'content' => [
                    'headline' => 'Collect more photos of your wedding using QR Codes',
                    'subheadline' => 'Guests scan your unique QR Code to upload photos, videos, and guest book messages to your event gallery. Relive the day through their eyes.',
                    'cta_text' => 'Try for FREE',
                    'cta_link' => '/pricing',
                    'features' => [
                        'No app install needed for guests',
                        'Unlimited number of guests can view and upload to event gallery',
                        'Easily share photos with everyone afterwards'
                    ]
                ],
                'is_active' => true,
                'order' => 1
            ],
            [
                'section' => 'features',
                'content' => [
                    'headline' => 'Collect photos to remember for a lifetime',
                    'subheadline' => 'We help you get the pictures your loved ones will take of all the romantic, funny, and special moments.',
                    'items' => [
                        [
                            'icon' => 'Camera',
                            'title' => 'Event gallery',
                            'description' => 'All photos and videos are sent to the event gallery for easy viewing, sharing and download.',
                            'details' => [
                                'No more hunting down guests to get photos and videos',
                                'Set photos or videos to private so only you can see',
                                'All photos and videos saved in original quality'
                            ]
                        ],
                        [
                            'icon' => 'Smartphone',
                            'title' => 'Customizable guest page',
                            'description' => 'The guest page is fully customizable by you. Use your wedding colors, a picture of you and your partner, and leave a welcome message.',
                            'details' => [
                                'No app install needed. Works in the browser',
                                'Guest name associated with every item uploaded',
                                'All photos and videos saved in original quality'
                            ]
                        ],
                        [
                            'icon' => 'MessageCircle',
                            'title' => 'Digital guestbook',
                            'description' => 'Capture heartfelt messages in video, audio, or written format from your loved ones, including those who could not attend.',
                            'details' => [
                                'Greet guests with a custom audio message before they add to your audio and video guestbook',
                                'No max length for written messages',
                                'No time limit for audio or video messages'
                            ]
                        ],
                        [
                            'icon' => 'Play',
                            'title' => 'Live slideshow',
                            'description' => 'Entertain everyone at the reception with a live slideshow. All photos and videos are added to the slideshow in real time.',
                            'details' => [
                                'Encourages guests to take more photos',
                                'Supports photos and videos',
                                'Customize slide playback speed, background color and background animation'
                            ]
                        ],
                        [
                            'icon' => 'Shield',
                            'title' => 'Moderate what guests can do',
                            'description' => 'Protect your event from strangers and limit what your guests can do.',
                            'details' => [
                                'Turn off uploads from guest',
                                'Password protect gallery uploads and viewing',
                                'Allow guest to see everything or only moderated items'
                            ]
                        ]
                    ]
                ],
                'is_active' => true,
                'order' => 2
            ],
            [
                'section' => 'how_it_works',
                'content' => [
                    'headline' => 'How Snapshot Albums Works',
                    'subheadline' => 'Receive photos & videos in 3 easy steps',
                    'steps' => [
                        [
                            'number' => 1,
                            'icon' => 'Calendar',
                            'title' => 'Create an event',
                            'description' => 'Give your event a name and date'
                        ],
                        [
                            'number' => 2,
                            'icon' => 'QrCode',
                            'title' => 'Share event with guests',
                            'description' => 'Print the provided QR Code for guests to scan'
                        ],
                        [
                            'number' => 3,
                            'icon' => 'Heart',
                            'title' => 'Enjoy the uploads',
                            'description' => 'Scroll your event gallery to relive those precious moments'
                        ]
                    ]
                ],
                'is_active' => true,
                'order' => 3
            ],
            [
                'section' => 'cta',
                'content' => [
                    'headline' => 'Ready to Capture Your Special Moments?',
                    'subheadline' => 'Join thousands of couples and families who trust Snapshot Albums to preserve their most precious memories. Start your free trial today!',
                    'cta_text' => 'Start Free Trial',
                    'cta_link' => '/pricing',
                    'features' => [
                        [
                            'icon' => 'Check',
                            'title' => 'No Setup Required',
                            'description' => 'Get started in minutes with our simple setup process'
                        ],
                        [
                            'icon' => 'Users',
                            'title' => 'Unlimited Guests',
                            'description' => 'Invite as many guests as you want to your event'
                        ],
                        [
                            'icon' => 'Camera',
                            'title' => 'High Quality Storage',
                            'description' => 'All photos stored in original quality forever'
                        ]
                    ]
                ],
                'is_active' => true,
                'order' => 4
            ]
        ];

        foreach ($contents as $content) {
            HomePageContent::updateOrCreate(
                ['section' => $content['section']],
                $content
            );
        }
    }
}
