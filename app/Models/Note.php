<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'inscription_id',
        'matiere_id',
        'enseignant_id',
        'trimestre',
        'note',
        'date_saisie',
        'commentaire',
    ];

    protected $casts = [
        'note' => 'decimal:2',
        'date_saisie' => 'date:Y-m-d',
    ];

    public function inscription() {
        return $this->belongsTo(Inscription::class);
    }

    public function matiere() {
        return $this->belongsTo(Matiere::class);
    }

    public function enseignant() {
        return $this->belongsTo(Enseignant::class);
    }

    /**
     * Validation pour éviter les doublons
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $exists = self::where('inscription_id', $model->inscription_id)
                ->where('matiere_id', $model->matiere_id)
                ->where('trimestre', $model->trimestre)
                ->exists();

            if ($exists) {
                throw new \Exception('Une note existe déjà pour cette matière et ce trimestre.');
            }
        });

        static::updating(function ($model) {
            $exists = self::where('inscription_id', $model->inscription_id)
                ->where('matiere_id', $model->matiere_id)
                ->where('trimestre', $model->trimestre)
                ->where('id', '!=', $model->id)
                ->exists();

            if ($exists) {
                throw new \Exception('Une note existe déjà pour cette matière et ce trimestre.');
            }
        });
    }
}