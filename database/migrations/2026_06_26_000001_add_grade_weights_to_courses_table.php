<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('weight_attendance')->default(10)->after('description');
            $table->unsignedTinyInteger('weight_assignment')->default(30)->after('weight_attendance');
            $table->unsignedTinyInteger('weight_uts')->default(30)->after('weight_assignment');
            $table->unsignedTinyInteger('weight_uas')->default(30)->after('weight_uts');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn([
                'weight_attendance',
                'weight_assignment',
                'weight_uts',
                'weight_uas',
            ]);
        });
    }
};
