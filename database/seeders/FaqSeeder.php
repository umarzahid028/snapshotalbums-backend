<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FaqSeeder extends Seeder
{
    public function run()
    {
        DB::table('faqs')->insert([
            [
                'question' => 'How does Snapshot Albums work?',
                'answer' => 'We connect your existing or new Google Drive™ account to your Snapshot Albums account. When you create a new album on our dashboard, it creates a new folder within your Google Drive™ space. Once your guests upload images and videos through your album-specific QR code or web link, Snapshot Albums places the files directly into your Google Drive™ folder. All for free!',
                'category' => 'General',
                'order' => 1,
                'is_active' => 1,
            ],
            [
                'question' => 'Is Snapshot Albums free?',
                'answer' => 'Yes! Snapshot Albums is free to use. Collect images and videos from your guests for free. Paid users have access to 10+ Canva™ QR code flyer templates, have access to share the photos and videos guests uploaded in a public gallery, as well as the ability to create more than one album.',
                'category' => 'General',
                'order' => 2,
                'is_active' => 1,
            ],
            [
                'question' => 'Where do my photos and videos go?',
                'answer' => 'Snapshot Albums connects to your Google Drive™ account and stores your pictures and videos safely on the cloud. We do not host any of the files uploaded.',
                'category' => 'General',
                'order' => 3,
                'is_active' => 1,
            ],
            [
                'question' => 'How many pictures and videos can my guests upload to my album?',
                'answer' => 'Snapshot Albums uses your connected Google Drive™ account to store the uploaded pictures and videos. The only limit is the available space in your Drive account. You can check the amount of space you have available and add space to your Google Drive™ account here.',
                'category' => 'General',
                'order' => 4,
                'is_active' => 1,
            ],
            [
                'question' => 'What if I don’t have enough Google Drive™ space?',
                'answer' => 'Google Drive™ gives you 15 GBs of space for free with each new account. We find that some people decide to create a new Google account for their event. You can also add storage space to an already existing account for $2/month.',
                'category' => 'Storage',
                'order' => 5,
                'is_active' => 1,
            ],
            [
                'question' => 'Will I be notified if I am out of space in my album?',
                'answer' => 'Google automatically alerts users when they are near or at their assigned storage limit for their individual accounts and shared drives. If you are concerned about space, you can always add storage space to an already existing account for $2/month.',
                'category' => 'Storage',
                'order' => 6,
                'is_active' => 1,
            ],
            [
                'question' => 'Can I share the album with the guests after my event?',
                'answer' => 'Yes! When you create your album and become a paid user, you can enable sharing. This provides you with an album share link that all your guests can use to see the pictures and videos they’ve uploaded. All uploaded files can be reviewed and/or removed by you, the owner of the album. This link can be found on your dashboard, titled: "Gallery".',
                'category' => 'Sharing',
                'order' => 7,
                'is_active' => 1,
            ],
            [
                'question' => 'Can I create more than one album?',
                'answer' => 'Yes! As a paid user you can create as many albums as you\'d like. Simply tap or click "Create Album" on the dashboard once logged in, provide the folder name, event name and date. Then your album-specific QR code and web link will be generated.',
                'category' => 'Albums',
                'order' => 8,
                'is_active' => 1,
            ],
            [
                'question' => 'Can I do this myself with Google Drive™?',
                'answer' => 'Google Drive™ alone doesn’t give you an easy way for other people to upload images and videos into a folder you create. Snapshot Albums makes it easy to set up the folder and provide your guests with the link or QR code to upload their images and videos directly to your folder WITHOUT being able to see what others have shared. All on one easy-to-use dashboard for free! Try it now.',
                'category' => 'General',
                'order' => 9,
                'is_active' => 1,
            ],
        ]);
    }
}
