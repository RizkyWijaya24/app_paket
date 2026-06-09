<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_packet_id')->constrained('customer_packets')->onDelete('cascade');
            $table->decimal('jumlah_setoran', 12, 2);
            $table->date('tanggal_setor');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_ledgers');
    }
};
