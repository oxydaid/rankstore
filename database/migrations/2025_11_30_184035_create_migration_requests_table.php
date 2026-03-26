<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('migration_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // Data Player
            $table->string('old_gamertag');
            $table->string('new_gamertag');
            $table->foreignId('category_id')->constrained('categories');
            $table->string('whatsapp_number');
            $table->string('discord_username')->nullable();

            // Data Rank & Biaya
            $table->foreignId('rank_id')->constrained('ranks');
            $table->decimal('rank_price_snapshot', 12, 2); // Harga rank saat request dibuat
            $table->integer('fee_percent_snapshot'); // Persen fee saat request dibuat
            $table->decimal('total_amount', 12, 2); // Total yang harus dibayar

            // Pembayaran
            $table->foreignId('payment_method_id')->constrained('payment_methods');
            $table->string('payment_proof')->nullable(); // Manual
            $table->string('payment_url')->nullable(); // Gateway
            $table->string('trx_id')->nullable(); // Gateway Ref
            $table->decimal('admin_fee', 12, 2)->default(0); // Fee Gateway
            $table->string('server_invoice')->nullable();

            // Status & Legal
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('tos_agreed_at'); // Wajib setuju risiko

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('migration_requests');
    }
};
