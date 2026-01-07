<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sujet', 200)->nullable();
            $table->text('contenu');
            $table->dateTime('date_envoi');
            $table->boolean('lu')->default(false);

            // Clés étrangères
            $table->foreignId('expediteur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('destinataire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('annee_scolaire_id')->nullable()->constrained('annee_scolaires')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
