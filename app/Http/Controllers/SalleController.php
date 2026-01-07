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
        $salles = Salle::with(['niveau', 'niveau.filiere'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Admin/Salles/Index', [
            'salles' => $salles,
        ]);
    }

    public function create()
    {
        // Récupérer seulement les niveaux qui n'ont pas encore de salle
        $niveauxSansSalle = Niveau::whereDoesntHave('salles')->get();

        return Inertia::render('Admin/Salles/Create', [
            'niveaux' => $niveauxSansSalle,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomSalle' => 'required|string|max:50|unique:salles,nomSalle',
            'capacite' => 'required|integer|min:1',
            'niveau_id' => 'required|exists:niveaux,id|unique:salles,niveau_id',
        ], [
            'niveau_id.unique' => 'Ce niveau a déjà une salle attribuée.',
            'nomSalle.unique' => 'Cette salle existe déjà.',
        ]);

        Salle::create($validated);

        return redirect()->route('salles.index')
            ->with('success', 'Salle créée avec succès');
    }

    public function edit(Salle $salle)
    {
        // Récupérer les niveaux sans salle + le niveau actuel de cette salle
        $niveauxSansSalle = Niveau::whereDoesntHave('salles')
            ->orWhere('id', $salle->niveau_id)
            ->get();

        return Inertia::render('Admin/Salles/Edit', [
            'salle' => $salle,
            'niveaux' => $niveauxSansSalle,
        ]);
    }

    public function update(Request $request, Salle $salle)
    {
        $validated = $request->validate([
            'nomSalle' => 'required|string|max:50|unique:salles,nomSalle,' . $salle->id,
            'capacite' => 'required|integer|min:1',
            'niveau_id' => 'required|exists:niveaux,id|unique:salles,niveau_id,' . $salle->id,
        ], [
            'niveau_id.unique' => 'Ce niveau a déjà une salle attribuée.',
            'nomSalle.unique' => 'Cette salle existe déjà.',
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