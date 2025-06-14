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
        Schema::create('admin_subdivision_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('admin_cid');
            $table->string('subdivision_id');
            $table->timestamps();

            $table->unique(['admin_cid', 'subdivision_id']);
            $table->foreign('admin_cid')->references('cid')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_subdivision_notifications');
    }
};
