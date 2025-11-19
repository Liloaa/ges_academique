<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Niveau extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomNiveau',
        'description',
        'section',
        'filiere_id',
    ];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class);
    }

    public function salle() {
        return $this->hasMany(Salle::class);
    }

    public function matieres() {
        return $this->hasMany(Matiere::class);
    }

    public function inscriptions() {
        return $this->hasMany(Inscription::class);
    }
}