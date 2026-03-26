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
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();

            $table->string('code')->unique(); // Kode unik (misal: BERKAH10)
            $table->enum('type', ['fixed', 'percent']); // Tipe potongan (Rp atau %)
            $table->decimal('amount', 12, 2); // Nilai potongan

            // Batasan Penggunaan
            $table->integer('max_uses')->nullable(); // Kuota maksimal (opsional)
            $table->integer('used_count')->default(0); // Hitungan berapa kali dipakai

            // Periode Berlaku
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
