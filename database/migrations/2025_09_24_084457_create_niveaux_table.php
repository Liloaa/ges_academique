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
        Schema::create('niveaux', function (Blueprint $table) {
            $table->id();
            $table->string('nomNiveau', 100)->unique();   // ex: 11ème A, 2nde S B
            $table->text('description')->nullable();

            $table->enum('cycle', ['primaire', 'college', 'lycee']);

            $table->foreignId('filiere_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('niveaux');
    }
};
