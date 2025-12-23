<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Umkm', function (Blueprint $table) {
            $table->id();
            $table->string('nama_usaha');
            $table->text('alamat');
            $table->string('kontak')->nullable();
            $table->boolean('status_halal')->default(true);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->text('deskripsi')->nullable();
            $table->string('foto_usaha')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('Umkm');
    }
};