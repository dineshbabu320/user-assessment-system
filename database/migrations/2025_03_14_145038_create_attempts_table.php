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
        Schema::create('attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('user_cid');
            $table->foreign('user_cid')->references('cid')->on('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->tinyInteger('attempt_number'); // 1st, 2nd, or 3rd attempt
            $table->timestamp('attempted_at')->useCurrent();
            $table->enum('status', ['passed', 'failed']);
            $table->timestamp('cooldown_until')->nullable(); // 7 days (1st fail), 14 days (2nd fail)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempts');
    }
};
