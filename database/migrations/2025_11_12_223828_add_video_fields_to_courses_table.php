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
        Schema::table('courses', function (Blueprint $table) {
            $table->text('video_url')->nullable()->comment('Video embed URL or path');
            $table->string('video_duration')->nullable()->comment('Video duration HH:MM:SS');
            $table->integer('total_videos')->default(0)->comment('Total number of videos in course');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            if (Schema::hasColumn('courses', 'video_url')) {
                $table->dropColumn('video_url');
            }
            if (Schema::hasColumn('courses', 'video_duration')) {
                $table->dropColumn('video_duration');
            }
            if (Schema::hasColumn('courses', 'total_videos')) {
                $table->dropColumn('total_videos');
            }
        });
    }
};
