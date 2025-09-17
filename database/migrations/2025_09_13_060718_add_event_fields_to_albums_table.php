<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->string('qrCode')->nullable()->after('event_title'); 
            $table->string('event_type')->nullable()->after('qrCode'); 
            $table->string('location')->nullable()->after('event_type'); // Location
            $table->text('event_description')->nullable()->after('location'); // Event Description
            $table->integer('max_photos_per_guest')->nullable()->after('event_description'); // Max Photos per Guest
            $table->text('custom_welcome_message')->nullable()->after('max_photos_per_guest'); // Custom Welcome Message
            $table->enum('privacy_level', ['private', 'public'])->default('private')->after('custom_welcome_message'); // Privacy Level
            $table->boolean('allow_guest_uploads')->default(true)->after('privacy_level'); // Allow Guest Uploads
            $table->string('status')->default('active')->after('privacy_level'); 
        });
    }

    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn([
                'event_type',
                'event_time',
                'location',
                'event_description',
                'max_photos_per_guest',
                'custom_welcome_message',
                'privacy_level',
                'allow_guest_uploads',
                'status',
            ]);
        });
    }
};
