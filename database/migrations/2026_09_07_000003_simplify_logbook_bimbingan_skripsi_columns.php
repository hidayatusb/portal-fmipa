<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->dropColumn(['topik', 'pembahasan', 'perbaikan', 'catatan']);
        });

        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->longText('catatan');
        });
    }

    public function down(): void
    {
        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->dropColumn('catatan');
        });

        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->string('topik');
            $table->text('pembahasan')->nullable();
            $table->text('perbaikan')->nullable();
            $table->text('catatan')->nullable();
        });
    }
};
