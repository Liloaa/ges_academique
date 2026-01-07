<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomSalle',
        'capacite',
        'niveau_id',
    ];

    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    public function inscriptions()
    {
        return $this->hasMany(Inscription::class);
    }

    // Effectif actuel
    public function getEffectifAttribute()
    {
        $anneeActive = AnneeScolaire::where('active', 1)->first();

        if (!$anneeActive) {
            return 0;
        }

        return $this->inscriptions()
            ->where('annee_scolaire_id', $anneeActive->id)
            ->count();
    }

    // Taux de remplissage %
    public function getTauxRemplissageAttribute()
    {
        if ($this->capacite == 0) {
            return 0;
        }

        return round(($this->effectif / $this->capacite) * 100, 2);
    }
}
