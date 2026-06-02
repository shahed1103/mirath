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
        Schema::create('user_chapter_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            $table->boolean('is_open')->default(false);
            // $table->boolean('is_unlocked')->default(false); 
            // $table->boolean('is_completed')->default(false); 
            // $table->float('current_score'); 
            // $table->float('current_level_score')->default(500); 
            // $table->integer('attempts_count')->default(0); 
            $table->unique([ 'user_id', 'chapter_id' ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_chapter_progress');
    }
};
