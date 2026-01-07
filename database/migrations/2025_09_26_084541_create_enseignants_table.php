<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enseignants', function (Blueprint $table) {
            $table->id();
            $table->string('matriculeEnseig', 50)->unique();
            $table->string('nom');
            $table->string('prenom');
            $table->string('grade', 50)->nullable();
            $table->string('specialite', 50)->nullable(); 
            $table->string('email')->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('sexe', 10)->nullable();
            $table->text('adresse')->nullable();

            // relation user
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enseignants');
    }
};