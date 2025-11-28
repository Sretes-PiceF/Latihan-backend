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
        Schema::create('penyewaan_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('penyewaan_id');
            $table->string('product_id', 16)->nullable(false);
            $table->string('jumlah');
            $table->string('subharga');
            $table->timestamps();

            //Foreign Key
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('penyewaan_id')->references('id')->on('penyewaan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyewaan_detail');
    }
};
