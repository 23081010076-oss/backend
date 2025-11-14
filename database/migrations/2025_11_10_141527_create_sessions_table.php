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
        Schema::create('mentoring_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('users')->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->enum('type', ['academic', 'life_plan'])->default('academic');
            $table->datetime('schedule')->nullable();
            $table->string('meeting_link')->nullable();
            $table->enum('payment_method', ['qris', 'bank', 'va', 'manual'])->nullable();
            $table->enum('status', ['pending', 'completed', 'refunded', 'scheduled', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentoring_sessions');
    }
};
