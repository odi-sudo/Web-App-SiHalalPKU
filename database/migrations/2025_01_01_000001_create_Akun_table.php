<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('Akun', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nama');
            $table->enum('role', ['Administrator', 'Pengguna'])->default('Pengguna');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('Akun');
    }
};