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
        DB::statement("UPDATE users SET status = CASE
            WHEN status = 'active' THEN 1
            ELSE 0
        END");

        // Now alter the column to boolean
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('status')->default(true)->change();
        });
    }

    public function down(): void
    {
        // Revert back to string
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active')->change();
        });
    }
};
