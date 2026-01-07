<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('inscription_id')
                ->constrained('inscriptions')
                ->onDelete('cascade');

            $table->foreignId('matiere_id')
                ->constrained('matieres')
                ->onDelete('cascade');

            $table->foreignId('enseignant_id')
                ->nullable()
                ->constrained('enseignants')
                ->onDelete('set null');

            $table->enum('trimestre', ['1er', '2ème', '3ème'])
                ->default('1er');

            $table->decimal('note',4, 2)->comment('Note suur 20'); 
            $table->date('date_saisie')->nullable();

            $table->text('commentaire')->nullable();

            $table->timestamps();

            // Empêche les doublons : même élève, même matière, même trimestre
            $table->unique(['inscription_id', 'matiere_id', 'trimestre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};