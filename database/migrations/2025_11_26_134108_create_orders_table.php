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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique(); // Link rahasia: website.com/track/uuid

            // Info Pembeli
            $table->string('gamertag');
            $table->string('discord_username')->nullable();
            $table->string('whatsapp_number');

            // Relasi (Apa yang dibeli & bayar pakai apa)
            $table->foreignId('rank_id')->constrained();
            $table->foreignId('payment_method_id')->constrained();
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();

            // Logika Upgrade (Opsional)
            $table->boolean('is_upgrade')->default(false);
            $table->foreignId('previous_rank_id')->nullable()->constrained('ranks'); // Rank lama user
            $table->string('upgrade_proof')->nullable(); // SS profile user/bukti rank lama

            // Keuangan
            $table->decimal('total_amount', 12, 2); // Nominal yang harus dibayar
            $table->decimal('subtotal_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2);
            $table->decimal('admin_fee', 12, 2)->default(0);
            $table->string('payment_proof')->nullable(); // Bukti transfer user
            $table->string('payment_url')->nullable();
            $table->string('trx_id')->nullable();

            // Proses Admin
            $table->string('server_invoice')->nullable(); // Bukti dari admin server
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable(); // Catatan tambahan

            // Legal
            $table->timestamp('tos_agreed_at')->nullable(); // Waktu user centang TOS

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
