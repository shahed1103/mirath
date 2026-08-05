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
Schema::create('api_request_logs', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->string('method', 10);

    $table->string('endpoint');

    $table->unsignedSmallInteger('status_code');

    $table->unsignedInteger('response_time'); // milliseconds

    $table->ipAddress('ip')->nullable();

    $table->text('user_agent')->nullable();   // جديد

    $table->timestamps();

    $table->index(['created_at']);
    $table->index(['endpoint']);
    $table->index(['status_code']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_request_logs');
    }
};
