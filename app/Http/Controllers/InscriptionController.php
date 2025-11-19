<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\Salle;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InscriptionController extends Controller
{
    public function index(Request $request)
    {
        $annee_id = $request->get('annee_scolaire_id');
        $salle_id = $request->get('salle_id');
        $etat = $request->get('etat');

        $inscriptions = Inscription::with(['eleve', 'salle', 'annee'])
            ->when($annee_id, fn($q) => $q->where('annee_scolaire_id', $annee_id))
            ->when($salle_id, fn($q) => $q->where('salle_id', $salle_id))
            ->when($etat, fn($q) => $q->where('etat', $etat))
            ->orderBy('date_inscription', 'desc')
            ->get();

        $filtre = [
            'annees' => AnneeScolaire::all(),
            'salles' => Salle::all(),
        ];

        return Inertia::render('Admin/Inscriptions/Index', [
            'inscriptions' => $inscriptions,
            'filtre' => $filtre,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Inscriptions/Create', [
            'eleves' => Eleve::orderBy('nom')->get(),
            'salles' => Salle::orderBy('nomSalle')->get(),
            'annees' => AnneeScolaire::orderBy('libelle')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'eleve_id' => 'required|exists:eleves,id',
            'salle_id' => 'required|exists:salles,id',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id',
            'date_inscription' => 'required|date',
            'etat' => 'required|string|max:20',
        ], [
            'eleve_id.required' => 'Le champ élève est obligatoire.',
            'salle_id.required' => 'Le champ salle est obligatoire.',
            'annee_scolaire_id.required' => 'Le champ année scolaire est obligatoire.',
            'date_inscription.required' => 'La date d’inscription est obligatoire.',
            'etat.required' => 'Le champ état est obligatoire.',
        ]);

        Inscription::create($data);

        return redirect()->route('inscriptions.index')
            ->with('success', 'Inscription enregistrée avec succès.');
    }

    public function edit(Inscription $inscription)
    {
        return Inertia::render('Admin/Inscriptions/Edit', [
            'inscription' => $inscription->load(['eleve', 'salle', 'annee']),
            'eleves' => Eleve::all(),
            'salles' => Salle::all(),
            'annees' => AnneeScolaire::all(),
        ]);
    }

    public function update(Request $request, Inscription $inscription)
    {
        $data = $request->validate([
            'salle_id' => 'required|exists:salles,id',
            'annee_scolaire_id' => 'required|exists:annee_scolaires,id',
            'etat' => 'required|string|max:20',
        ]);

        $inscription->update($data);

        return redirect()->route('inscriptions.index')
            ->with('success', 'Inscription mise à jour.');
    }

    public function destroy(Inscription $inscription)
    {
        $inscription->delete();
        return back()->with('success', 'Inscription supprimée.');
    }

    // Historique des inscriptions d’un élève 
    public function historique($id)
    {
        $eleve = Eleve::with(['inscriptions.salle', 'inscriptions.annee'])
            ->findOrFail($id);

        return Inertia::render('Admin/Eleves/Historique', [
            'eleve' => $eleve
        ]);
    }
}
