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
        Schema::create('exam_qusetions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            $table->integer('order_number'); 
            $table->boolean('answered') ->default(false); 
            $table->foreignId('selected_choice_id')->constrained('question_choices')->onDelete('cascade');
            $table->boolean('is_correct') ->nullable(); 
            $table->float('earned_score') ->default(0); 
            $table->timestamp('answered_at') ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_qusetions');
    }
};
