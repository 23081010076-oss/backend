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
        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->date('report_date');
            $table->integer('progress_percentage')->default(0)->comment('0-100');
            $table->text('notes')->nullable();
            $table->string('attachment_url')->nullable();
            $table->date('next_report_date')->nullable();
            $table->integer('frequency')->default(14)->comment('Days between reports');
            $table->timestamps();
            $table->index('enrollment_id');
            $table->index('report_date');
            $table->index('next_report_date');
        });

        // Add columns to enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            $table->date('last_progress_report_date')->nullable()->after('certificate_date');
            $table->date('next_progress_report_date')->nullable()->after('last_progress_report_date');
            $table->integer('report_frequency')->default(14)->after('next_progress_report_date')->comment('Days between reports');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['last_progress_report_date', 'next_progress_report_date', 'report_frequency']);
        });
        Schema::dropIfExists('progress_reports');
    }
};
