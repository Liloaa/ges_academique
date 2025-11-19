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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('sujet', 200)->nullable();       // sujet facultatif
            $table->text('contenu');                        // contenu du message
            $table->dateTime('date_envoi');                 // date et heure d'envoi
            $table->boolean('lu')->default(false);          // statut lu/non lu

            // Clés étrangères
            $table->foreignId('expediteur_id')->nullable()->constrained('users')->onDelete('set null');//l’historique des messages est garde même après suppression d’un user
            $table->foreignId('destinataire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('annee_id')->nullable()->constrained('annee_scolaires')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
