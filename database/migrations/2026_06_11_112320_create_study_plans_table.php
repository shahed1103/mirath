<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('study_plans', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained()
        ->onDelete('cascade');

    $table->enum('type', [
        'daily_amount',
        'fixed_duration'
    ]);

    $table->integer('daily_chapters')->nullable();

    $table->integer('duration_days')->nullable();

    $table->time('notification_time');

    $table->boolean('is_offline')->default(false);

    $table->enum('status', [
        'active',
        'completed'
    ])->default('active');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plans');
    }
};
