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
        Schema::create('interactions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('moleculeA_id');
            $table->unsignedBigInteger('moleculeB_id');
            $table->text('description')->nullable();
            $table->foreign('moleculeA_id')->references('id')->on('molecules')->onDelete('cascade');
            $table->foreign('moleculeB_id')->references('id')->on('molecules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
