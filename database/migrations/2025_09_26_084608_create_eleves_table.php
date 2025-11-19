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
        Schema::create('eleves', function (Blueprint $table) {
            $table->id();
            $table->string('matricule', 50)->unique();                   // matricule unique
            $table->string('nom');
            $table->string('prenom');
            $table->date('date_naissance')->nullable();                // date de naissance nullable
            $table->string('email')->nullable();                 
            $table->string('sexe', 10)->nullable();                      // sexe nullable
            $table->text('adresse')->nullable();                         // adresse nullable
            $table->string('telephone', 20)->nullable();                // téléphone nullable

            // relations
            $table->unsignedBigInteger('user_id')->nullable();

            // clés étrangères
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eleves');
    }
};