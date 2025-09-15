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
            $table->integer('total_files')->default(0)->after('event_date');   // total files in album
            $table->integer('total_guests')->default(0)->after('total_files'); // total guests
        });
    }

    public function down(): void
    {
        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('total_files');
            $table->dropColumn('total_guests');
        });
    }
};
