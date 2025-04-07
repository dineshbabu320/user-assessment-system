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
        Schema::table('rating_upgrade_requests', function (Blueprint $table) {
            $table->string('rating')->after('token_id'); // Add the rating column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rating_upgrade_requests', function (Blueprint $table) {
            $table->dropColumn('rating');
        });
    }
};
