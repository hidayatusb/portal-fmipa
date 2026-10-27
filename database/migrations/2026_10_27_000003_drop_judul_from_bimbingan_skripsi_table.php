<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bimbingan_skripsi', function (Blueprint $table) {
            $table->dropColumn('judul');
        });
    }

    public function down(): void
    {
        Schema::table('bimbingan_skripsi', function (Blueprint $table) {
            $table->string('judul')->nullable()->after('mahasiswa_id');
        });
    }
};
