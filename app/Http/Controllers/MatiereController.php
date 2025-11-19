<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = Matiere::with(['filiere', 'niveau', 'enseignant'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Admin/Matieres/Index', [
            'matieres' => $matieres,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Matieres/Create', [
            'filieres' => Filiere::all(),
            'niveaux' => Niveau::all(),
            'enseignants' => Enseignant::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomMatiere' => 'required|string|max:100',
            'coefficient' => 'required|numeric|min:0',
            'filiere_id' => 'nullable|exists:filieres,id',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'enseignant_id' => 'nullable|exists:enseignants,id',
        ]);

        Matiere::create($validated);

        return redirect()->route('matieres.index')->with('success', 'Matière ajoutée avec succès.');
    }

    public function edit(Matiere $matiere)
    {
        return Inertia::render('Admin/Matieres/Edit', [
            'matiere' => $matiere,
            'filieres' => Filiere::all(),
            'niveaux' => Niveau::all(),
            'enseignants' => Enseignant::all(),
        ]);
    }

    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nomMatiere' => 'required|string|max:100',
            'coefficient' => 'required|numeric|min:0',
            'filiere_id' => 'nullable|exists:filieres,id',
            'niveau_id' => 'nullable|exists:niveaux,id',
            'enseignant_id' => 'nullable|exists:enseignants,id',
        ]);

        $matiere->update($validated);

        return redirect()->route('matieres.index')->with('success', 'Matière mise à jour.');
    }

    public function destroy(Matiere $matiere)
    {
        $matiere->delete();

        return redirect()->route('matieres.index')->with('success', 'Matière supprimée.');
    }
}
