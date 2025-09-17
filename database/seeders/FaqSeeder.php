<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FaqSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $faqs = [
            [
                'question' => 'How does Snapshot Albums work?',
                'answer' => 'You connect a Google Drive account (existing or new) and Snapshot Albums will automatically sync your photos and videos to it.',
                'category' => 'General',
                'order' => 1,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Is Snapshot Albums free?',
                'answer' => 'You connect a Google Drive account (existing or new) and can start uploading your files for free up to your Google Drive storage limit.',
                'category' => 'Pricing',
                'order' => 2,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Will I be notified if I am out of space in my album?',
                'answer' => 'Google will automatically alert you when you\'re close to your storage limit.',
                'category' => 'Pricing',
                'order' => 2,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'question' => 'Where do my photos and videos go?',
                'answer' => 'They are stored in your Google Drive account. Snapshot Albums only manages syncing them.',
                'category' => 'Account',
                'order' => 2,
                'is_active' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('faqs')->insert($faqs);
    }
}
