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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: BCA, QRIS, GoPay
            $table->string('code')->nullable();

            $table->enum('fee_type', ['fixed', 'percent', 'mixed'])->default('fixed');
            $table->decimal('fee_percent', 5, 2)->default(0); // %
            $table->decimal('fee_flat', 12, 2)->default(0); // Rp

            $table->string('account_number')->nullable(); // No. Rekening / No. HP
            $table->string('account_holder')->nullable(); // Atas Nama

            $table->text('description')->nullable(); // Instruksi transfer
            $table->string('logo')->nullable(); // Logo bank
            $table->boolean('is_active')->default(true);
            $table->boolean('is_manual')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
