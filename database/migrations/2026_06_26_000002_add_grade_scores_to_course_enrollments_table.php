<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->unsignedTinyInteger('attendance_score')->nullable()->after('enrolled_at');
            $table->unsignedTinyInteger('uts_score')->nullable()->after('attendance_score');
            $table->unsignedTinyInteger('uas_score')->nullable()->after('uts_score');
        });
    }

    public function down(): void
    {
        Schema::table('course_enrollments', function (Blueprint $table) {
            $table->dropColumn(['attendance_score', 'uts_score', 'uas_score']);
        });
    }
};
