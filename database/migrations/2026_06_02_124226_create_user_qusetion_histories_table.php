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
        Schema::create('user_qusetion_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            // $table->integer('appeared_count') ->default(1); 
            // $table->timestamp('last_seen_at') ->nullable(); 
            // $table->boolean('answered_correctly') ->nullable(); 
            $table->unique([ 'user_id', 'question_id' ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_qusetion_histories');
    }
};
