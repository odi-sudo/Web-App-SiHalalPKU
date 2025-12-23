<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Produk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained('Umkm')->onDelete('cascade');
            $table->string('nama_produk');
            $table->decimal('harga', 12, 2);
            $table->string('foto_produk')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('Produk');
    }
};