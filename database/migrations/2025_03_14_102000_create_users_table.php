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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('vatsim_id')->unique(); // VATSIM ID
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('role_id')->constrained()->onDelete('cascade'); // Role assigned to user
            $table->foreignId('subdivision_id')->nullable()->constrained()->onDelete('set null'); // NULL for directors
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
