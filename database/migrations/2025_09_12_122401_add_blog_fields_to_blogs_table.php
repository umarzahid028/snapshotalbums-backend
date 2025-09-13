<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->string('excerpt')->nullable()->after('description');
            $table->string('author')->nullable()->after('excerpt');
            $table->string('author_email')->nullable()->after('author');
            $table->string('status')->default('draft')->after('author_email');
            $table->string('category')->nullable()->after('status');
            $table->string('tags')->nullable()->after('category');
        });
    }

    public function down()
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['excerpt', 'author', 'author_email', 'status', 'category', 'tags']);
        });
    }
};
