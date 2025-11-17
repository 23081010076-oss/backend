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
        Schema::create('coaching_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentoring_session_id')->constrained('mentoring_sessions')->onDelete('cascade');
            $table->string('file_name');
            $table->string('file_path');
            $table->enum('file_type', ['pdf', 'doc', 'docx', 'video', 'image', 'other'])->default('pdf');
            $table->string('uploaded_by')->nullable()->comment('User who uploaded');
            $table->timestamps();
            $table->index('mentoring_session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coaching_files');
    }
};
