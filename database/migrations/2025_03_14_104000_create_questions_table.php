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
        Schema::create('questions', function (Blueprint $table) {
            $table->id(); // Auto-incrementing question ID
            $table->unsignedBigInteger('exam_id');
            $table->text('question_text');
            $table->json('options')->nullable(); // Store multiple choices (optional)
            $table->timestamps();

            // Foreign key relationships
            $table->foreign('exam_id')->references('id')->on('exams')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
