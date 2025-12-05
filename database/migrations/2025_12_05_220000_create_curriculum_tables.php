<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel untuk kurikulum course dan progress tracking
     */
    public function up(): void
    {
        // ==========================================================
        // 1. Tabel course_curriculums - Daftar materi/kurikulum
        // ==========================================================
        Schema::create('course_curriculums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('section')->nullable()->comment('Nama bab/section, misal: Bab 1: Pengenalan');
            $table->integer('section_order')->default(0)->comment('Urutan section');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0)->comment('Urutan materi dalam section');
            $table->string('duration')->nullable()->comment('Estimasi durasi, contoh: 30 menit');
            $table->timestamps();

            // Index untuk performa query
            $table->index(['course_id', 'section_order', 'order']);
        });

        // ==========================================================
        // 2. Tabel curriculum_progress - Tracking materi yang sudah selesai
        // ==========================================================
        Schema::create('curriculum_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('curriculum_id')->constrained('course_curriculums')->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Unique constraint: satu enrollment tidak bisa complete materi yang sama 2x
            $table->unique(['enrollment_id', 'curriculum_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_progress');
        Schema::dropIfExists('course_curriculums');
    }
};
