<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('placement_test_results', function (Blueprint $table) {
            $table->string('nim')->primary();
            $table->string('nama');
            $table->unsignedInteger('nilai');
            $table->string('hasil'); // lulus / tidak lulus
            $table->string('keterangan')->nullable(); // link Google Drive sertifikat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('placement_test_results');
    }
};
