<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_packets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->foreignId('packet_id')->constrained('packets')->onDelete('restrict');
            $table->unsignedInteger('kuantitas')->default(1)->comment('Jumlah unit paket yang diambil');
            $table->enum('status', ['aktif', 'lunas', 'batal'])->default('aktif');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_packets');
    }
};
