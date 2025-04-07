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
        Schema::table('responses', function (Blueprint $table) {
            $table->unsignedBigInteger('attempt_id')->after('user_cid'); // Add attempt_id column
            $table->foreign('attempt_id')->references('id')->on('attempts')->onDelete('cascade'); // Add foreign key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responses', function (Blueprint $table) {
            $table->dropForeign(['attempt_id']);
            $table->dropColumn('attempt_id');
        });
    }
};
