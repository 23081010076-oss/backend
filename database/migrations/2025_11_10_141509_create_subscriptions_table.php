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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan')->default('free'); // free, regular, premium
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->enum('package_type', ['single_course', 'all_in_one'])->default('single_course');
            $table->integer('duration')->default(1); // Duration value (1, 3, 12)
            $table->enum('duration_unit', ['months', 'years'])->default('months');
            $table->json('courses_ids')->nullable(); // JSON array of course IDs
            $table->decimal('price', 12, 2)->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
