<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EleveNoteController extends Controller
{
    public function index()
    {
        // On récupère l'élève connecté (lié à User)
        $eleve = Auth::user()->eleve;

        // Si aucun élève n’est lié à cet utilisateur (sécurité)
        if (!$eleve) {
            abort(403, 'Accès non autorisé');
        }

        // On récupère uniquement les notes qui concernent cet élève
        $notes = Note::with(['matiere', 'enseignant'])
            ->whereHas('inscription', function ($query) use ($eleve) {
                $query->where('eleve_id', $eleve->id);
            })
            ->orderBy('trimestre')
            ->get();

        return Inertia::render('Eleve/Notes/Index', [
            'notes' => $notes,
            'eleve' => $eleve,
        ]);
    }

    public function show(Note $note)
{
    $eleve = Auth::user()->eleve;

    if (!$eleve || $note->inscription->eleve_id !== $eleve->id) {
        abort(403, 'Vous ne pouvez pas accéder à cette note.');
    }

    return Inertia::render('Eleve/Notes/Show', [
        'note' => $note->load(['matiere', 'enseignant']),
    ]);
}
}