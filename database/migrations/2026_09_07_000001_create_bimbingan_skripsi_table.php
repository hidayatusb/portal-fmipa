<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bimbingan_skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('pembimbing1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('pembimbing2_id')->constrained('users')->cascadeOnDelete();
            $table->string('pembimbing1_status')->default('pending');
            $table->string('pembimbing2_status')->default('pending');
            $table->timestamp('pembimbing1_responded_at')->nullable();
            $table->timestamp('pembimbing2_responded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bimbingan_skripsi');
    }
};
