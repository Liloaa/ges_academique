<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomFiliere',
        'description',
    ];

    /**
     * Une filière peut avoir plusieurs niveaux (ex: 2nde S, 2nde L, etc.)
     */
    public function niveaux()
    {
        return $this->hasMany(Niveau::class, 'filiere_id');
    }

    /**
     * Une filière peut avoir plusieurs matières
     */
    public function matieres()
    {
        return $this->hasMany(Matiere::class, 'filiere_id');
    }

}
