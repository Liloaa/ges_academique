<?php

namespace App\Http\Controllers;

use App\Models\Niveau;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NiveauController extends Controller
{
    public function index()
    {
        $niveaux = Niveau::with('filiere')->orderBy('id', 'desc')->get();

        return Inertia::render('Admin/Niveaux/Index', [
            'niveaux' => $niveaux
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Niveaux/Create', [
            'filieres' => Filiere::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomNiveau' => 'required|string|max:100|unique:niveaux,nomNiveau',
            'description' => 'nullable|string',
            'section' => 'required|in:primaire,college,lycee',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        Niveau::create($validated);

        return redirect()->route('niveaux.index')
            ->with('success', 'Niveau ajouté avec succès.');
    }

    public function edit(Niveau $niveau)
    {
        return Inertia::render('Admin/Niveaux/Edit', [
            'niveau' => $niveau,
            'filieres' => Filiere::all(),
        ]);
    }

    public function update(Request $request, Niveau $niveau)
    {
        $validated = $request->validate([
            'nomNiveau' => 'required|string|max:100|unique:niveaux,nomNiveau,' . $niveau->id,
            'description' => 'nullable|string',
            'section' => 'required|in:primaire,college,lycee',
            'filiere_id' => 'nullable|exists:filieres,id',
        ]);

        $niveau->update($validated);

        return redirect()->route('niveaux.index')
            ->with('success', 'Niveau modifié avec succès.');
    }

    public function destroy(Niveau $niveau)
    {
        $niveau->delete();

        return redirect()->route('niveaux.index')
            ->with('success', 'Niveau supprimé avec succès.');
    }
}
