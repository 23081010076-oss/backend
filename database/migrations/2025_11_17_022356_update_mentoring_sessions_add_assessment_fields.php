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
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            $table->enum('need_assessment_status', ['pending', 'completed'])->default('pending')->after('status')->comment('Assessment completion status');
            $table->json('assessment_form_data')->nullable()->after('need_assessment_status')->comment('Assessment form responses');
            $table->string('coaching_files_path')->nullable()->after('assessment_form_data')->comment('Path to coaching materials');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentoring_sessions', function (Blueprint $table) {
            $table->dropColumn(['need_assessment_status', 'assessment_form_data', 'coaching_files_path']);
        });
    }
};
