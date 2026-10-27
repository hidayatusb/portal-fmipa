<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judul_skripsi', function (Blueprint $table) {
            $table->id();
            $table->string('nim');
            $table->string('nama_mahasiswa');
            $table->text('judul');
            $table->string('pembimbing1');
            $table->string('pembimbing2')->nullable();
            $table->timestamps();

            $table->index('nim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judul_skripsi');
    }
};
