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
Schema::create('study_plan_days', function (Blueprint $table) {
            $table->id();

            $table->foreignId('study_plan_id')
                ->constrained()
                ->cascadeOnDelete();

            // 0 = Sunday
            // 1 = Monday
            // ...
            // 6 = Saturday

            $table->tinyInteger('day_number');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_plan_days');
    }
};
