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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->integer('questions_answered'); 
            $table->integer('correct_answers');
            $table->integer('estimated_duration'); 
            $table->float('current_level_score'); 
            $table->boolean('success')->default(false);;
            $table->enum('status', [ 'active', 'finished']);
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->integer('points')->default(0);

            $table->foreignId('last_question_id')->nullable()->constrained('questions')->nullOnDelete();
            $table->foreignId('last_choice_id')->nullable()->constrained('question_choices')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
