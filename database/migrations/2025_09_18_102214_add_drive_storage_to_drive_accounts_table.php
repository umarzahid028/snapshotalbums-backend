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
        Schema::table('drive_accounts', function (Blueprint $table) {
            $table->string('drive_storage')->nullable()->after('json_token');
            $table->string('used_storage')->nullable()->after('drive_storage');
            $table->string('status')->default('connected')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('drive_accounts', function (Blueprint $table) {
            $table->dropColumn('drive_storage');
            $table->dropColumn('used_storage');
            $table->dropColumn('status');
        });
    }
};
