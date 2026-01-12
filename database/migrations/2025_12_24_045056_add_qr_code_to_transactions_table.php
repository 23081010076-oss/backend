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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('qr_code_url')->nullable()->after('payment_proof'); // URL QR code untuk QRIS
            $table->text('qr_string')->nullable()->after('qr_code_url'); // String data untuk generate QR
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['qr_code_url', 'qr_string']);
        });
    }
};
