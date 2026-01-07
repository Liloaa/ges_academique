<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Eleve;
use App\Models\Salle;
use App\Models\AnneeScolaire;
use App\Models\Niveau;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InscriptionController extends Controller
{
    public function index()
    {
        // Récupérer les inscriptions groupées par cycle via la salle->niveau
        $inscriptions = Inscription::with([
            'eleve', 
            'salle.niveau.filiere', 
            'annee'
        ])
        ->whereHas('salle.niveau')
        ->orderBy('date_inscription', 'desc')
        ->get();

        // Grouper par cycle
        $grouped = $inscriptions->groupBy(function ($inscription) {
            return $inscription->salle->niveau->cycle;
        });

        $cycles = ['primaire', 'college', 'lycee'];
        $inscriptionsGroupes = [];

        foreach ($cycles as $cycle) {
            $inscriptionsGroupes[$cycle] = $grouped->get($cycle, []);
        }

        return Inertia::render('Admin/Inscriptions/Index', [
            'inscriptionsGroupes' => $inscriptionsGroupes,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Inscriptions/Create', [
            'eleves' => Eleve::orderBy('nom')->get(),
            'salles' => Salle::with('niveau')->orderBy('nomSalle')->get(),
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
            'date_inscription.required' => 'La date d inscription est obligatoire.',
            'etat.required' => 'Le champ état est obligatoire.',
        ]);

        Inscription::create($data);

        return redirect()->route('inscriptions.index')
            ->with('success', 'Inscription enregistrée avec succès.');
    }

    public function edit(Inscription $inscription)
    {
        return Inertia::render('Admin/Inscriptions/Edit', [
            'inscription' => $inscription->load(['eleve', 'salle.niveau', 'annee']),
            'eleves' => Eleve::all(),
            'salles' => Salle::with('niveau')->get(),
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

    // Historique des inscriptions d'un élève 
    public function historique($id)
    {
        $eleve = Eleve::with(['inscriptions.salle.niveau', 'inscriptions.annee'])
            ->findOrFail($id);

        return Inertia::render('Admin/Eleves/Historique', [
            'eleve' => $eleve,
            'inscriptions' => $eleve->inscriptions
        ]);
    }
}