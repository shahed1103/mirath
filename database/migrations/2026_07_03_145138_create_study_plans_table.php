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
                ->cascadeOnDelete();

            // duration | daily_pages
            $table->enum('plan_type', ['duration', 'daily_pages']);

            // إذا كان نوع الخطة daily_pages
            $table->unsignedInteger('daily_pages')->nullable();
            // إذا كان نوع الخطة duration
            $table->unsignedInteger('target_days')->nullable();

            $table->time('notification_time');

            $table->boolean('offline')->default(false);

            $table->date('start_date');

            $table->date('end_date')->nullable();

            $table->enum('status', [
                'active',
                'completed',
                'cancelled'
            ])->default('active');


            $table->unsignedInteger('total_pages');

            $table->unsignedInteger('total_books');
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
