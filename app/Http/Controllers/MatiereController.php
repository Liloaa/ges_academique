<?php

namespace App\Http\Controllers;

use App\Models\Matiere;
use App\Models\Niveau;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = Matiere::with(['niveau', 'enseignant'])
            ->orderBy('id', 'desc')
            ->get();

        // Grouper par cycle
        $grouped = $matieres->groupBy(function ($matiere) {
            return $matiere->niveau->cycle;
        });

        $cycles = ['primaire', 'college', 'lycee'];
        $matieresGroupes = [];

        foreach ($cycles as $cycle) {
            $matieresGroupes[$cycle] = $grouped->get($cycle, []);
        }

        return Inertia::render('Admin/Matieres/Index', [
            'matieresGroupes' => $matieresGroupes,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Matieres/Create', [
            'niveaux' => Niveau::all(),
            'enseignants' => Enseignant::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomMatiere' => 'required|string|max:100',
            'coefficient' => 'required|numeric|min:0',
            'niveau_id' => 'required|exists:niveaux,id',
            'enseignant_id' => 'nullable|exists:enseignants,id',
        ]);

        Matiere::create($validated);

        return redirect()->route('matieres.index')->with('success', 'Matière ajoutée avec succès.');
    }

    public function edit(Matiere $matiere)
    {
        return Inertia::render('Admin/Matieres/Edit', [
            'matiere' => $matiere,
            'niveaux' => Niveau::all(),
            'enseignants' => Enseignant::all(),
        ]);
    }

    public function update(Request $request, Matiere $matiere)
    {
        $validated = $request->validate([
            'nomMatiere' => 'required|string|max:100',
            'coefficient' => 'required|numeric|min:0',
            'niveau_id' => 'required|exists:niveaux,id',
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