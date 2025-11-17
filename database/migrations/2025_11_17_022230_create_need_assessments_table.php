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
        Schema::create('need_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentoring_session_id')->unique()->constrained('mentoring_sessions')->onDelete('cascade');
            $table->json('form_data')->nullable()->comment('Assessment form responses');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('need_assessments');
    }
};
