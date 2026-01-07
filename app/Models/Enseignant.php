<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enseignant extends Model
{
    use HasFactory;

    protected $fillable = [
        'matriculeEnseig',
        'nom',
        'prenom',
        'grade',
        'specialite',
        'email',
        'telephone',
        'sexe',
        'adresse',
        'user_id',
    ];

    protected $casts = [
        'user_id' => 'integer',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matieres()
    {
        return $this->hasMany(Matiere::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    
    protected static function booted()
    {
        static::deleting(function ($enseignant) {
            if ($enseignant->user) {
                $enseignant->user->delete(); //supprime le user lie
            }
        });
    }
}