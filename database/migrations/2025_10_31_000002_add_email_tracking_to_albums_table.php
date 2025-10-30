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
            $table->timestamp('last_reminder_email_sent_at')->nullable()->after('status');
            $table->timestamp('last_post_event_email_sent_at')->nullable()->after('last_reminder_email_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn(['last_reminder_email_sent_at', 'last_post_event_email_sent_at']);
        });
    }
};
