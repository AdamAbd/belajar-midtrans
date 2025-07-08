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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique(); // ID Pesanan unik
            $table->bigInteger('amount');        // Jumlah pembayaran
            $table->string('status')->default('pending'); // Status: pending, success, failed
            $table->string('payment_method')->nullable();
            $table->string('snap_token')->nullable(); // Untuk menyimpan snap token
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
