<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnneeScolaire extends Model
{
    use HasFactory;

    protected $table = 'annee_scolaires';

    protected $fillable = [
        'libelle',
        'active',
    ];

    /**
     * Relation : une année scolaire possède plusieurs inscriptions
     */
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'annee_scolaire_id');
    }

    /**
     * Scope : récupérer l'année active
     * Utilisation : AnneeScolaire::active()->get();
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Méthode statique : récupérer l'année active facilement
     * Utilisation : AnneeScolaire::getActive();
     */
    public static function getActive()
    {
        return self::where('active', true)->first();
    }

    /**
     * Helper : renvoie TRUE si cette année est active
     * Utilisation : $annee->isActive();
     */
    public function isActive(): bool
    {
        return $this->active === true;
    }
}
