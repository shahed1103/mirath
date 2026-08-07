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
 Schema::create('study_tasks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('study_plan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('book_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('chapter_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('user_id')
             ->constrained()
             ->cascadeOnDelete();

            $table->date('task_date');

            $table->unsignedInteger('from_page');

            $table->unsignedInteger('reading_order')->default(0);


            $table->unsignedInteger('to_page');

            $table->unsignedInteger('pages');

            $table->boolean('completed')
                ->default(false);

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('study_tasks');
    }
};
