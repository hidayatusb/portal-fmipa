<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignment_deadline_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('hours_before');
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['assignment_id', 'user_id', 'hours_before'], 'assignment_reminder_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_deadline_reminders');
    }
};
