<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bimbingan_skripsi_id')->constrained('bimbingan_skripsi')->cascadeOnDelete();
            $table->foreignId('pembimbing_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal');
            $table->string('topik');
            $table->text('pembahasan')->nullable();
            $table->text('perbaikan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_bimbingan_skripsi');
    }
};
