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
 Schema::create('meetings', function (Blueprint $table) {

    $table->id();









     $table->string('title');
     $table->string('meeting_link')->nullable();;
     $table->string('room_name');
     $table->string('type');
$table->date('scheduled_date')->nullable();
$table->time('scheduled_time')->nullable();
    // $table->uuid('room_id')->unique();
     $table->text('description')->nullable();
     $table->foreignId('created_by')
    ->constrained('users')
    ->cascadeOnDelete();
$table->timestamp('reminder_sent_at')
                ->nullable();
    // $table->foreignId('created_by')
    //     ->constrained('users')
    //     ->cascadeOnDelete();

    // $table->timestamp('started_at')->nullable();
    // $table->timestamp('ended_at')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
