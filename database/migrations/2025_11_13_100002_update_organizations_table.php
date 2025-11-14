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
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('location');
            }
            if (!Schema::hasColumn('organizations', 'phone')) {
                $table->string('phone')->nullable()->after('contact_email');
            }
            if (!Schema::hasColumn('organizations', 'website')) {
                $table->string('website')->nullable()->after('phone');
            }
            // Drop user_id constraint if it exists and make organizations independent
            if (Schema::hasColumn('organizations', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'contact_email')) {
                $table->dropColumn('contact_email');
            }
            if (Schema::hasColumn('organizations', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('organizations', 'website')) {
                $table->dropColumn('website');
            }
        });
    }
};
