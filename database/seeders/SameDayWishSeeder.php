<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SameDayWish;

class SameDayWishSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wishes = [
            [
                'type' => 'Birthday',
                'icon' => 'Heart',
                'messages' => [
                    'Happy Birthday! 🎉 May your special day be filled with joy, laughter, and wonderful memories!',
                    'Wishing you a year ahead filled with happiness, success, and all your dreams coming true!',
                    'Another year older, another year wiser! Hope your birthday is as amazing as you are!',
                    'May this new year of life bring you endless joy and countless reasons to smile!'
                ],
                'image' => 'https://images.pexels.com/photos/140831/pexels-photo-140831.jpeg?auto=compress&cs=tinysrgb&w=300&h=200&fit=crop',
                'is_active' => true,
                'order' => 1
            ],
            [
                'type' => 'Wedding',
                'icon' => 'Heart',
                'messages' => [
                    'Congratulations on your wedding day! 🎊 Wishing you both a lifetime of love, happiness, and beautiful memories together!',
                    'May your marriage be filled with love, laughter, and countless adventures! Here\'s to forever!',
                    'Two hearts, one love! Wishing you both endless joy and a lifetime of wonderful moments together!',
                    'May your wedding day be the beginning of a beautiful journey filled with love and happiness!'
                ],
                'image' => 'https://images.pexels.com/photos/265856/pexels-photo-265856.jpeg?auto=compress&cs=tinysrgb&w=300&h=200&fit=crop',
                'is_active' => true,
                'order' => 2
            ],
            [
                'type' => 'Anniversary',
                'icon' => 'Star',
                'messages' => [
                    'Happy Anniversary! 🎉 Celebrating another year of love, laughter, and beautiful memories together!',
                    'May your love continue to grow stronger with each passing year! Here\'s to many more!',
                    'Wishing you both continued happiness and love as you celebrate another year together!',
                    'Another year of love, another year of memories! May your bond grow even stronger!'
                ],
                'image' => 'https://images.pexels.com/photos/1024993/pexels-photo-1024993.jpeg?auto=compress&cs=tinysrgb&w=300&h=200&fit=crop',
                'is_active' => true,
                'order' => 3
            ]
        ];

        foreach ($wishes as $wish) {
            SameDayWish::updateOrCreate(
                ['type' => $wish['type']],
                $wish
            );
        }
    }
}
