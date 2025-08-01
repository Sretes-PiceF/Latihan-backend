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
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_id', 16)->primary()->nullable(false);
            $table->string('product_name', 100)->nullable(false);
            $table->integer('product_stock')->nullable(false);
            $table->integer('product_price')->nullable(false);
            $table->string('categories_id', 16)->nullable(false);

            // Perbaiki: 'cascade' (bukan 'casecade')
            $table->foreign('categories_id')
                ->references('categories_id')
                ->on('categories_products')
                ->onDelete('cascade')  // <-- Ini yang diperbaiki
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
