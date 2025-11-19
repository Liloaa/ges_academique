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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 150);           // titre de la notification
            $table->text('contenu');                // contenu de la notification
            $table->string('typeNotif', 50);             // type de notification (info, alerte, etc.)
            $table->boolean('lu')->default(false);  // statut lu/non lu
            $table->dateTime('date_notif');         // date de la notification

            // clé étrangère vers users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
