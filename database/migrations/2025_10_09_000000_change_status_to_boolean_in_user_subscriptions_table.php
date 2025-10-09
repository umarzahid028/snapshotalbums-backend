<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // First, update existing data to boolean values
        DB::statement("UPDATE user_subscriptions SET status = CASE
            WHEN status IN ('active', 'trialing') THEN 1
            ELSE 0
        END");

        // Now alter the column to boolean
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->boolean('status')->default(false)->change();
        });
    }

    public function down(): void
    {
        // Revert back to enum
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->enum('status', ['active', 'canceled', 'past_due', 'expired', 'trialing'])->default('trialing')->change();
        });
    }
};
