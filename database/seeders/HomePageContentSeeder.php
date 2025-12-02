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
                'section' => 'event_types',
                'content' => [
                    'headline' => 'Perfect for Every Special Occasion',
                    'subheadline' => 'From intimate celebrations to grand events, Snapshot Albums helps you capture every moment',
                    'types' => [
                        [
                            'icon' => 'Heart',
                            'title' => 'Weddings',
                            'description' => 'Capture every precious moment of your special day',
                            'image' => 'https://images.pexels.com/photos/265856/pexels-photo-265856.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop',
                            'features' => [
                                'Ceremony photos',
                                'Reception moments',
                                'Guest interactions',
                                'Behind-the-scenes'
                            ]
                        ],
                        [
                            'icon' => 'Cake',
                            'title' => 'Birthdays',
                            'description' => 'Celebrate life\'s milestones with friends and family',
                            'image' => 'https://images.pexels.com/photos/140831/pexels-photo-140831.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop',
                            'features' => [
                                'Party photos',
                                'Cake cutting',
                                'Gift opening',
                                'Fun moments'
                            ]
                        ],
                        [
                            'icon' => 'Gift',
                            'title' => 'Anniversaries',
                            'description' => 'Mark another year of love and togetherness',
                            'image' => 'https://images.pexels.com/photos/1024993/pexels-photo-1024993.jpeg?auto=compress&cs=tinysrgb&w=300&h=200&fit=crop',
                            'features' => [
                                'Romantic moments',
                                'Celebration photos',
                                'Memories',
                                'Special occasions'
                            ]
                        ],
                        [
                            'icon' => 'Users',
                            'title' => 'Corporate Events',
                            'description' => 'Professional gatherings and team celebrations',
                            'image' => 'https://images.pexels.com/photos/1181391/pexels-photo-1181391.jpeg?auto=compress&cs=tinysrgb&w=400&h=300&fit=crop',
                            'features' => [
                                'Team photos',
                                'Event coverage',
                                'Professional shots',
                                'Networking moments'
                            ]
                        ]
                    ]
                ],
                'is_active' => true,
                'order' => 4
            ],
            [
                'section' => 'same_day_wishes',
                'content' => [
                    'headline' => 'Same-Day Wishes & Messages',
                    'subheadline' => 'Share heartfelt messages and wishes instantly with your guests. Perfect for birthdays, weddings, and special celebrations.',
                    'wishes' => [
                        [
                            'type' => 'Birthday',
                            'icon' => 'Heart',
                            'image' => 'https://images.pexels.com/photos/140831/pexels-photo-140831.jpeg?auto=compress&cs=tinysrgb&w=300&h=200&fit=crop',
                            'messages' => [
                                'Happy Birthday! 🎉 May your special day be filled with joy, laughter, and wonderful memories!',
                                'Wishing you a year ahead filled with happiness, success, and all your dreams coming true!',
                                'Another year older, another year wiser! Hope your birthday is as amazing as you are!',
                                'May this new year of life bring you endless joy and countless reasons to smile!'
                            ]
                        ],
                        [
                            'type' => 'Wedding',
                            'icon' => 'Heart',
                            'image' => 'https://images.pexels.com/photos/265856/pexels-photo-265856.jpeg?auto=compress&cs=tinysrgb&w=300&h=200&fit=crop',
                            'messages' => [
                                'Congratulations on your wedding day! 🎊 Wishing you both a lifetime of love, happiness, and beautiful memories together!',
                                'May your marriage be filled with love, laughter, and countless adventures! Here\'s to forever!',
                                'Two hearts, one love! Wishing you both endless joy and a lifetime of wonderful moments together!',
                                'May your wedding day be the beginning of a beautiful journey filled with love and happiness!'
                            ]
                        ],
                        [
                            'type' => 'Anniversary',
                            'icon' => 'Star',
                            'image' => 'https://images.pexels.com/photos/1024993/pexels-photo-1024993.jpeg?auto=compress&cs=tinysrgb&w=300&h=200&fit=crop',
                            'messages' => [
                                'Happy Anniversary! 🎉 Celebrating another year of love, laughter, and beautiful memories together!',
                                'May your love continue to grow stronger with each passing year! Here\'s to many more!',
                                'Wishing you both continued happiness and love as you celebrate another year together!',
                                'Another year of love, another year of memories! May your bond grow even stronger!'
                            ]
                        ]
                    ]
                ],
                'is_active' => true,
                'order' => 5
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
                'order' => 6
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
