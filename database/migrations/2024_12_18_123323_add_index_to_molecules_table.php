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
        Schema::table('molecules', function (Blueprint $table) {
            //
            $table->index('libelle');
            $table->index('formule');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('molecules', function (Blueprint $table) {
            //
            $table->dropIndex(['libelle']);
            $table->dropIndex(['formule']);
        });
    }
};
