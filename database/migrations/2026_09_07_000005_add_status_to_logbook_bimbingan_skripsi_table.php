<?php

use App\Enums\BimbinganApprovalStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->string('status')->default(BimbinganApprovalStatus::Pending->value)->after('catatan');
            $table->timestamp('status_responded_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('logbook_bimbingan_skripsi', function (Blueprint $table) {
            $table->dropColumn(['status', 'status_responded_at']);
        });
    }
};
