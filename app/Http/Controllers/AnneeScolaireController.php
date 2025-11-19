<?php

namespace App\Http\Controllers;

use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AnneeScolaireController extends Controller
{
    public function index()
    {
        $annees = AnneeScolaire::orderBy('id', 'desc')->get();
        return Inertia::render('Admin/AnneesScolaires/Index', [
            'annees' => $annees
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/AnneesScolaires/Create');
    }

    public function edit(AnneeScolaire $anneesscolaire)
    {
        return Inertia::render('Admin/AnneesScolaires/Edit', [
            'anneesscolaire' => $anneesscolaire
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'libelle' => 'required|unique:annee_scolaires,libelle'
        ]);

        AnneeScolaire::create($request->only('libelle'));
        return redirect()->route('anneesscolaires.index')->with('success', 'Année scolaire ajoutée avec succès');
    }

    public function update(Request $request, AnneeScolaire $anneesscolaire)
    {
        $request->validate([
            'libelle' => 'required|unique:annee_scolaires,libelle,' . $anneesscolaire->id,
        ]);

        $anneesscolaire->update(['libelle' => $request->libelle]);
        return redirect()->route('anneesscolaires.index')->with('success', 'Année scolaire mise à jour');
    }

    public function destroy(AnneeScolaire $anneesscolaire)
    {
        $anneesscolaire->delete();
        return redirect()->route('anneesscolaires.index')->with('success', 'Année supprimée');
    }

    public function setActive(AnneeScolaire $anneesscolaire)
    {
        // Désactiver toutes les autres années
        AnneeScolaire::where('active', true)->update(['active' => false]);

        // Activer celle-ci
        $anneesscolaire->update(['active' => true]);

        return redirect()->back()->with('success', 'Année scolaire activée');
    }
}
