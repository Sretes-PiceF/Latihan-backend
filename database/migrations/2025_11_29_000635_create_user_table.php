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
        Schema::create('user', function (Blueprint $table) {
            $table->id()->serial;
            $table->string('username', 255)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'pelanggan']);
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->unsignedBigInteger('pelanggan_id')->nullable();
            $table->timestamps();

            //Foreign Key
            $table->foreign('admin_id')->references('id')->on('admin')->onDelete('cascade');
            $table->foreign('pelanggan_id')->references('id')->on('pelanggan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
