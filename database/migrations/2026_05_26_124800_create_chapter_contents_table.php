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
        Schema::create('chapter_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->onDelete('cascade');
            // $table->string('type');
            // $table->string('url');
            $table->enum('type', ['pdf', 'video', 'audio']);
            $table->string('url')->nullable();
            $table->enum('upload_status', [
                'pending',
                'uploaded',
                'failed',
            ])->nullable();
            $table->unique(['chapter_id', 'type']);
            $table->unsignedInteger('total_progress_value');
            // 'type' => 'required|in:pdf,video,audio'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_contents');
    }
};
