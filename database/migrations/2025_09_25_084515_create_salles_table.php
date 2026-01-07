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
        Schema::create('salles', function (Blueprint $table) {
            $table->id();
            $table->string('nomSalle', 50);           // Nom de la salle
            $table->integer('capacite');              // Capacité max
            $table->unsignedBigInteger('niveau_id')->nullable()->unique();  // Relation avec niveau

            // Contraintes de clé étrangère
            $table->foreign('niveau_id')->references('id')->on('niveaux')->onDelete('set null');

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salles');
    }
};
