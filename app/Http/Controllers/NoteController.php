<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Matiere;
use App\Models\Inscription;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    public function index()
    {
        $notes = Note::with(['inscription.eleve', 'matiere', 'enseignant'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Admin/Notes/Index', [
            'notes' => $notes,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Notes/Create', [
            'matieres' => Matiere::all(),
            'inscriptions' => Inscription::with('eleve')->get(),
            'enseignants' => Enseignant::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'nullable|exists:enseignants,id',
            'trimestre' => 'required|string',
            'note' => 'required|numeric|min:0|max:20',
            'date_saisie' => 'nullable|date',
        ], [
            'inscription_id.required' => 'Le champ élève est obligatoire.',
            'matiere_id.required' => 'Le champ matière est obligatoire.',
            'trimestre.required' => 'Le champ trimestre est obligatoire.',
        ]);

        // Si aucune date n'est fournie, on met la date du jour
        if (empty($validated['date_saisie'])) {
            $validated['date_saisie'] = now()->toDateString();
        }

        Note::create($validated);

        return redirect()->route('notes.index')->with('success', 'Note ajoutée avec succès.');
    }

    public function edit(Note $note)
    {
        return Inertia::render('Admin/Notes/Edit', [
            'note' => $note->load(['matiere', 'inscription.eleve', 'enseignant']),
            'matieres' => Matiere::all(),
            'inscriptions' => Inscription::with('eleve')->get(),
            'enseignants' => Enseignant::all(),
        ]);
    }

    public function update(Request $request, Note $note)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'matiere_id' => 'required|exists:matieres,id',
            'enseignant_id' => 'nullable|exists:enseignants,id',
            'trimestre' => 'required|string',
            'note' => 'required|numeric|min:0|max:20',
            'date_saisie' => 'nullable|date',
        ]);

        $note->update($validated);

        return redirect()->route('notes.index')->with('success', 'Note mise à jour avec succès.');
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note supprimée avec succès.');
    }
}
