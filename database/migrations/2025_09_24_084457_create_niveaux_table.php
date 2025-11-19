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
            $table->unique(['nomNiveau', 'section']);
            $table->text('description')->nullable();
            $table->enum('section', ['primaire', 'college', 'lycee']);

            $table->foreignId('filiere_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();           // created_at et updated_at
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
