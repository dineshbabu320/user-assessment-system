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
            Schema::create('responses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('user_cid');
                $table->foreign('user_cid')->references('cid')->on('users')->onDelete('cascade');
                $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
                $table->text('response_text'); // Stores user's answer
                $table->enum('status', ['pending', 'correct', 'incorrect'])->default('pending'); // For manual evaluation
                $table->string('evaluated_by')->nullable(); // Should reference CID, not ID
                $table->foreign('evaluated_by')->references('cid')->on('users')->onDelete('set null'); // Corrected
                $table->timestamp('evaluated_at')->nullable();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responses');
    }
};
