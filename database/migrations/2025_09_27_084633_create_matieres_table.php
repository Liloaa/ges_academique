<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('matieres', function (Blueprint $table) {
            $table->id();
            $table->string('nomMatiere', 50);
            $table->float('coefficient')->default(1);

            // Relations
            $table->unsignedBigInteger('filiere_id')->nullable();
            $table->unsignedBigInteger('niveau_id')->nullable();
            $table->unsignedBigInteger('enseignant_id')->nullable();

            // Clés étrangères
            $table->foreign('filiere_id')->references('id')->on('filieres')->onDelete('set null');
            $table->foreign('niveau_id')->references('id')->on('niveaux')->onDelete('set null');
            $table->foreign('enseignant_id')->references('id')->on('enseignants')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};
