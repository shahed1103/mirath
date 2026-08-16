<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_plan_reminder_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('study_plan_id')
                ->constrained('study_plans')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->date('reminder_date');

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->unique([
                'study_plan_id',
                'reminder_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_plan_reminder_logs');
    }
};