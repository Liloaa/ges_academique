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

    public function inscription()
    {
        return $this->hasMany(Inscription::class);
    }

    // Effectif actuel
    public function getEffectifAttribute()
    {
        return $this->inscriptions()->count();
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
