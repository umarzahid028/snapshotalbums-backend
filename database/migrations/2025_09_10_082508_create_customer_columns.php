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
        Schema::table('users', function (Blueprint $table) {
            $table->string('drive_email')->nullable()->after('google_id');
            $table->string('drive_connect_name')->nullable()->after('drive_email');
            $table->text('access_token')->nullable()->after('drive_connect_name');
            $table->text('google_token')->nullable()->after('access_token');
            $table->text('google_refresh_token')->nullable()->after('google_token');
            $table->integer('google_token_expires_in')->nullable()->after('google_refresh_token');
            $table->text('stripe_customer_id')->nullable()->after('google_token_expires_in');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'access_token',
                'google_token',
                'google_refresh_token',
                'google_token_expires_in',
                'stripe_customer_id',
            ]);
        });
    }
};
