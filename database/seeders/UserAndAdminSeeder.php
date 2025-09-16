<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;

class UserAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Normal user
        User::updateOrCreate(
            ['email' => 'user@user.com'],
            [
                'name' => 'Normal User',
                'password' => Hash::make('user@user.com'),
            ]
        );

        // Admin user
        Admin::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin@admin.com'),
            ]
        );
    }
}
