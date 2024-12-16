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
        Schema::create('medicaments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('laboratoire')->nullable(); // Laboratoire fabricant
            $table->string('nom_produit')->nullable(); // Nom du produit (avec présentation)
            $table->string('conditionnement')->nullable(); // Conditionnement
            $table->decimal('prix_public', 10, 2)->nullable(); // Prix public en FCFA
            $table->string('pays')->nullable(); // Pays d'origine
            $table->string('voie_administration')->nullable(); // Voie d'administration
            $table->string('forme')->nullable(); // Forme galénique
            $table->string('type')->nullable(); // Type de médicament
            $table->string('genre')->nullable(); // Genre
            $table->string('atc')->nullable(); // Classification ATC
            $table->string('dci')->nullable(); // DCI / Principes actifs
            $table->text('definition')->nullable(); // Définition produit / classe thérapeutique
            $table->string('conditionnement_detail')->nullable(); // Détails supplémentaires du conditionnement
            $table->text('excipients')->nullable(); // Liste des excipients
            $table->date('expiration_amm')->nullable(); // Date d'expiration de l'AMM
            $table->string('numero_amm')->nullable(); // Numéro de l'AMM
            $table->timestamps(); // Timestamps pour created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicaments');
    }
};
