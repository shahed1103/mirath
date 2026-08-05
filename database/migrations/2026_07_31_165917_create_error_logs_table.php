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
Schema::create('error_logs', function (Blueprint $table) {

    $table->id();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained()
        ->nullOnDelete();

    $table->string('exception');

    $table->text('message');

    $table->unsignedSmallInteger('status_code')
        ->nullable();

    $table->string('endpoint')
        ->nullable();

    $table->string('method',10)
        ->nullable();

    $table->ipAddress('ip')
        ->nullable();

        $table->string('file')->nullable();
$table->unsignedInteger('line')->nullable();
    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
