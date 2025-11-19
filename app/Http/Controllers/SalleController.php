<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalleController extends Controller
{
    public function index()
    {
        $salles = Salle::with('niveau')
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Admin/Salles/Index', [
            'salles' => $salles,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Salles/Create', [
            'niveaux' => Niveau::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomSalle' => 'required|string|max:50',
            'capacite' => 'required|integer|min:1',
            'niveau_id' => 'nullable|exists:niveaux,id',
        ]);

        Salle::create($validated);

        return redirect()->route('salles.index')
            ->with('success', 'Salle créée avec succès');
    }

    public function edit(Salle $salle)
    {
        return Inertia::render('Admin/Salles/Edit', [
            'salle' => $salle,
            'niveaux' => Niveau::all(),
        ]);
    }

    public function update(Request $request, Salle $salle)
    {
        $validated = $request->validate([
            'nomSalle' => 'required|string|max:50',
            'capacite' => 'required|integer|min:1',
            'niveau_id' => 'nullable|exists:niveaux,id',
        ]);

        $salle->update($validated);

        return redirect()->route('salles.index')
            ->with('success', 'Salle mise à jour');
    }

    public function destroy(Salle $salle)
    {
        $salle->delete();

        return redirect()->route('salles.index')
            ->with('success', 'Salle supprimée');
    }
}
