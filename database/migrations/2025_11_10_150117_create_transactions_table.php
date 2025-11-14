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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_code')->unique();
            $table->enum('type', ['course_enrollment', 'subscription', 'mentoring_session', 'scholarship_application']);
            $table->morphs('transactionable'); // polymorphic relation
            $table->decimal('amount', 15, 2);
            $table->enum('payment_method', ['qris', 'bank_transfer', 'virtual_account', 'credit_card', 'manual']);
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'refunded'])->default('pending');
            $table->text('payment_details')->nullable(); // JSON for payment gateway response
            $table->string('payment_proof')->nullable(); // for manual payment
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
