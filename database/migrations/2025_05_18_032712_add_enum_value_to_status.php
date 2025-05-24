<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add 'in_progress' to the ENUM values for status
        DB::statement("ALTER TABLE attempts MODIFY status ENUM('in_progress', 'waiting_for_manual_evaluation', 'passed', 'failed')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'in_progress' from the ENUM values for status
        DB::statement("ALTER TABLE attempts MODIFY status ENUM('waiting_for_manual_evaluation', 'passed', 'failed')");
    }
};
