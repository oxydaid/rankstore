<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();

            // Relasi ke Order
            $table->foreignId('order_id')->nullable()->constrained('orders')->cascadeOnDelete();
            $table->foreignId('migration_request_id')->nullable()->constrained('migration_requests')->nullOnDelete();

            // Data Penting dari AriePulsa
            $table->string('kode_deposit')->unique(); // Kunci utama pencocokan Callback
            $table->string('metode')->default('QRISREALTIME');

            // Keuangan
            $table->decimal('nominal_request', 12, 2); // Yang kita minta (3000)
            $table->decimal('admin_fee', 12, 2); // Fee dari AriePulsa (781)
            $table->decimal('total_bayar', 12, 2); // Total harus transfer (3781)

            // Data QR
            $table->text('qr_link')->nullable(); // Link gambar QR
            $table->text('qr_string')->nullable(); // String QR (jika ada)

            $table->string('status')->default('Pending'); // Pending, Success, Failed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
