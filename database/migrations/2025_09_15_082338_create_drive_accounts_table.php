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
        Schema::create('drive_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // link to users
            $table->string('drive_name')->nullable();
            $table->string('drive_email')->unique(); // Google Drive email
            $table->string('google_id')->nullable();
            $table->string('avatar')->nullable();

            // Tokens
            $table->text('google_token')->nullable();
            $table->text('google_refresh_token')->nullable();
            $table->integer('google_token_expires_in')->nullable();
            $table->text('access_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drive_accounts');
    }
};
