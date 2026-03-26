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
        Schema::table('app_settings', function (Blueprint $table) {
            $table->string('license_code')->nullable()->after('secondary_color');
            $table->string('mayar_product_id')->nullable()->after('license_code');
            $table->string('license_status')->default('inactive')->after('mayar_product_id');
            $table->timestamp('license_last_checked_at')->nullable()->after('license_status');
            $table->timestamp('license_expires_at')->nullable()->after('license_last_checked_at');
            $table->json('license_verified_payload')->nullable()->after('license_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_settings', function (Blueprint $table) {
            $table->dropColumn([
                'license_code',
                'mayar_product_id',
                'license_status',
                'license_last_checked_at',
                'license_expires_at',
                'license_verified_payload',
            ]);
        });
    }
};
