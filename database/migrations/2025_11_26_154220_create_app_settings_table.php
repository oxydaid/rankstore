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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();

            $table->string('site_name')->default('Minecraft Store');
            $table->text('site_description')->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('hero_background')->nullable(); // Gambar Background Hero

            $table->string('qris_image')->nullable();
            $table->string('tokopay_merchant_id')->nullable();
            $table->string('tokopay_secret_key')->nullable();
            $table->string('ariepulsa_api_key')->nullable();
            $table->string('ref_id_prefix')->nullable()->default('TRX');
            $table->integer('migration_fee_percent')->nullable()->default(20);

            $table->string('server_ip')->nullable(); // IP Server Minecraft
            $table->string('server_port')->default('19132')->nullable(); // Port (Default Bedrock)

            $table->string('wa_api_key')->nullable();
            $table->string('admin_phone')->nullable();
            $table->string('wa_sender_number')->nullable();
            $table->string('discord_webhook')->nullable();
            $table->string('discord_admin_id')->nullable();

            $table->json('social_media')->nullable();

            $table->string('primary_color')->default('#d97706');
            $table->string('secondary_color')->default('#581c87');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
