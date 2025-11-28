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
        Schema::create('penyewaan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->string('tglsewa')->nullable();
            $table->string('tglkembali')->nullable();
            $table->enum('status_pembayaran', ['Lunas', 'Belum bayar', 'DP'])->nullable()->default('Belum bayar');
            $table->enum('status_kembali', ['Sudah kembali', 'Belum kembali'])->nullable()->default('Belum kembali');
            $table->integer('total_harga');
            $table->timestamps();

            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewaan');
    }
};
