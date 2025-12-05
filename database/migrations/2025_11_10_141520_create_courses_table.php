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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->enum('type', ['bootcamp', 'course'])->default('course');
            $table->string('instructor')->nullable();
            $table->enum('level', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->string('duration')->nullable(); // e.g., "4 weeks", "20 hours"
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('access_type', ['free', 'regular', 'premium'])->default('free');
            $table->string('certificate_url')->nullable();
            $table->text('video_url')->nullable()->comment('Video introduction/preview URL');
            $table->string('video_duration')->nullable()->comment('Intro video duration HH:MM:SS');
            $table->integer('total_videos')->default(0)->comment('Total number of curriculum videos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
