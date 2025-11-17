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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->enum('package_type', ['single_course', 'all_in_one'])->default('single_course')->after('plan');
            $table->integer('duration')->default(1)->after('package_type')->comment('Duration value (1, 3, 12)');
            $table->enum('duration_unit', ['months', 'years'])->default('months')->after('duration');
            $table->json('courses_ids')->nullable()->after('duration_unit')->comment('JSON array of course IDs');
            $table->decimal('price', 12, 2)->nullable()->after('courses_ids');
            $table->boolean('auto_renew')->default(false)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['package_type', 'duration', 'duration_unit', 'courses_ids']);
        });
    }
};
