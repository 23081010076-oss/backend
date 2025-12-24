<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('scholarship_applications', function (Blueprint $table) {
            // Pre-Assessment fields
            $table->decimal('gpa', 3, 2)->nullable()->after('recommendation_path');
            $table->boolean('has_other_scholarship')->nullable()->after('gpa');
            $table->bigInteger('parent_income')->nullable()->after('has_other_scholarship');
            $table->string('university')->nullable()->after('parent_income');
            
            // Motivation letter text (untuk input teks, bukan file)
            $table->text('motivation_letter_text')->nullable()->after('motivation_letter');
        });

        // Ubah enum status untuk menambahkan 'draft'
        DB::statement("ALTER TABLE scholarship_applications MODIFY COLUMN status ENUM('draft','submitted','review','accepted','rejected') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan enum status ke semula
        DB::statement("ALTER TABLE scholarship_applications MODIFY COLUMN status ENUM('submitted','review','accepted','rejected') DEFAULT 'submitted'");

        Schema::table('scholarship_applications', function (Blueprint $table) {
            $table->dropColumn([
                'gpa',
                'has_other_scholarship',
                'parent_income',
                'university',
                'motivation_letter_text',
            ]);
        });
    }
};
