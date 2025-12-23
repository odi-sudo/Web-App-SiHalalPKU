<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Tambahkan import ini

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Review', function (Blueprint $table) {
            $table->id();
            $table->foreignId('akun_id')->constrained('Akun')->onDelete('cascade');
            $table->foreignId('umkm_id')->constrained('Umkm')->onDelete('cascade');
            $table->integer('rating')->unsigned()->default(1);
            $table->text('ulasan')->nullable();
            $table->timestamps();
        });

    
        DB::statement('ALTER TABLE "Review" ADD CONSTRAINT check_rating_range CHECK (rating >= 1 AND rating <= 5)');
    }

    public function down(): void
    {
        Schema::dropIfExists('Review');
    }
};