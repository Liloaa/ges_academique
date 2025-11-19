<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FiliereController extends Controller
{
    public function index()
    {
        $filieres = Filiere::orderBy('id', 'desc')->get();
        return Inertia::render('Admin/Filieres/Index', [
            'filieres' => $filieres
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Filieres/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomFiliere' => 'required|unique:filieres,nomFiliere',
            'description' => 'nullable|string',
        ]);

        Filiere::create($request->only('nomFiliere', 'description'));
        return redirect()->route('filieres.index')->with('success', 'Filière ajoutée avec succès.');
    }

    public function edit(Filiere $filiere)
    {
        return Inertia::render('Admin/Filieres/Edit', [
            'filiere' => $filiere
        ]);
    }

    public function update(Request $request, Filiere $filiere)
    {
        $request->validate([
            'nomFiliere' => 'required|unique:filieres,nomFiliere,' . $filiere->id,
            'description' => 'nullable|string',
        ]);

        $filiere->update($request->only('nomFiliere', 'description'));
        return redirect()->route('filieres.index')->with('success', 'Filière mise à jour avec succès.');
    }

    public function destroy(Filiere $filiere)
    {
        $filiere->delete();
        return redirect()->route('filieres.index')->with('success', 'Filière supprimée avec succès.');
    }
}
