<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Les attributs attribuables en masse.
     */
    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'password',
        'role',
        'force_password_change',
        'two_factor_enabled',
    ];

    /**
     * Les attributs à cacher lors de la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs à caster.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'force_password_change' => 'boolean',
        'two_factor_enabled' => 'boolean',
    ];

    /**
     * Accessor pour avoir le nom complet.
     */
    public function getFullNameAttribute(): string
    {
        return ucfirst($this->prenom) . ' ' . strtoupper($this->nom);
    }

    /**
     * Vérifie si l'utilisateur est administrateur.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est enseignant.
     */
    public function isEnseignant(): bool
    {
        return $this->role === 'enseignant';
    }

    /**
     * Vérifie si l'utilisateur est élève.
     */
    public function isEleve(): bool
    {
        return $this->role === 'eleve';
    }
    
    /**
     * Relation : un utilisateur possède un élève
     */
    public function eleve()
    {
        return $this->hasOne(Eleve::class);
    }

    /**
     * Relation : un utilisateur possède un enseignant
     */
    public function enseignant()
    {
        return $this->hasOne(Enseignant::class);
    }
}
