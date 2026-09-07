<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->string('judul')->after('pembimbing_id');
        });
    }

    public function down(): void
    {
        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->dropColumn('judul');
        });
    }
};
