<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('eleve_id')->constrained('eleves')->onDelete('cascade');
    $table->foreignId('salle_id')->constrained('salles')->onDelete('cascade');
    $table->foreignId('annee_scolaire_id')->constrained('annee_scolaires')->onDelete('cascade');

    $table->foreignId('niveau_id')->constrained('niveaux')->onDelete('cascade');

    $table->date('date_inscription');
    $table->string('etat', 20)->default('active');
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
