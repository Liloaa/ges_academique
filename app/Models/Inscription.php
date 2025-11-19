<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'eleve_id',
        'salle_id',
        'annee_scolaire_id',
        'date_inscription',
        'etat',
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function niveau() {
        return $this->belongsTo(Niveau::class);
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    public function annee()
    {
        return $this->belongsTo(AnneeScolaire::class, 'annee_scolaire_id');
    }

    public function notes() {
        return $this->hasMany(Note::class);
    }
}
