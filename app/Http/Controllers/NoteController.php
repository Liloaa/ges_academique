<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Matiere;
use App\Models\Inscription;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $inscriptionId = $request->get('inscription_id');
        $trimestre = $request->get('trimestre');

        $query = Note::with([
            'inscription.eleve',
            'inscription.salle.niveau',
            'matiere',
            'enseignant'
        ]);

        if ($inscriptionId) {
            $query->where('inscription_id', $inscriptionId);
        }

        if ($trimestre) {
            $query->where('trimestre', $trimestre);
        }

        $notes = $query->orderBy('matiere_id')
            ->orderBy('trimestre')
            ->get();

        // Grouper par inscription et trimestre pour un affichage plus clair
        $groupedNotes = $notes->groupBy('inscription_id')->map(function ($inscriptionNotes) {
            return $inscriptionNotes->groupBy('trimestre');
        });

        return Inertia::render('Admin/Notes/Index', [
            'notes' => $notes,
            'groupedNotes' => $groupedNotes,
            'filters' => [
                'inscription_id' => $inscriptionId,
                'trimestre' => $trimestre,
            ],
            'trimestres' => ['1er', '2ème', '3ème'],
        ]);
    }

    /**
     * Création de notes pour une inscription spécifique
     */
    public function create(Request $request)
    {
        $inscriptionId = $request->get('inscription_id');
        $trimestre = $request->get('trimestre', '1er');

        if (!$inscriptionId) {
            return redirect()->route('notes.index')
                ->with('error', 'Aucun élève sélectionné.');
        }

        $inscription = Inscription::with(['eleve', 'salle.niveau'])->findOrFail($inscriptionId);

        // Récupérer les matières du niveau de l'élève
        $matieres = Matiere::where('niveau_id', $inscription->salle->niveau_id)
            ->orderBy('nomMatiere')
            ->get();

        // Récupérer les notes existantes pour pré-remplir
        $notesExistantes = Note::where('inscription_id', $inscriptionId)
            ->where('trimestre', $trimestre)
            ->get()
            ->keyBy('matiere_id');

        return Inertia::render('Admin/Notes/Create', [
            'inscription' => $inscription,
            'matieres' => $matieres,
            'enseignants' => Enseignant::orderBy('nom')->get(),
            'trimestre' => $trimestre,
            'trimestres' => ['1er', '2ème', '3ème'],
            'notesExistantes' => $notesExistantes,
        ]);
    }

    /**
     * Stocker plusieurs notes en une fois
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'trimestre' => 'required|in:1er,2ème,3ème',
            'notes' => 'required|array',
            'notes.*.matiere_id' => 'required|exists:matieres,id',
            'notes.*.note' => 'required|numeric|min:0|max:20',
            'notes.*.enseignant_id' => 'nullable|exists:enseignants,id',
        ]);

        DB::beginTransaction();

        try {
            $notesCreees = 0;
            $notesMisesAJour = 0;

            foreach ($validated['notes'] as $noteData) {
                if ($noteData['note'] !== null && $noteData['note'] !== '') {
                    // Vérifier si une note existe déjà
                    $noteExistante = Note::where('inscription_id', $validated['inscription_id'])
                        ->where('matiere_id', $noteData['matiere_id'])
                        ->where('trimestre', $validated['trimestre'])
                        ->first();

                    if ($noteExistante) {
                        // Mettre à jour la note existante
                        $noteExistante->update([
                            'note' => $noteData['note'],
                            'enseignant_id' => $noteData['enseignant_id'],
                            'date_saisie' => now()->format('Y-m-d'),
                        ]);
                        $notesMisesAJour++;
                    } else {
                        // Créer une nouvelle note
                        Note::create([
                            'inscription_id' => $validated['inscription_id'],
                            'matiere_id' => $noteData['matiere_id'],
                            'enseignant_id' => $noteData['enseignant_id'],
                            'trimestre' => $validated['trimestre'],
                            'note' => $noteData['note'],
                            'date_saisie' => now()->format('Y-m-d'),
                        ]);
                        $notesCreees++;
                    }
                }
            }

            DB::commit();

            $message = "Notes enregistrées avec succès.";
            if ($notesCreees > 0) {
                $message .= " $notesCreees nouvelle(s) note(s) créée(s).";
            }
            if ($notesMisesAJour > 0) {
                $message .= " $notesMisesAJour note(s) mise(s) à jour.";
            }

            return redirect()->route('notes.index', ['inscription_id' => $validated['inscription_id']])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement des notes: ' . $e->getMessage()]);
        }
    }

    public function show($id)
    {
        // Pour l'affichage d'une note individuelle si nécessaire
        $note = Note::with(['inscription.eleve', 'matiere', 'enseignant'])->findOrFail($id);
        return Inertia::render('Admin/Notes/Show', [
            'note' => $note,
        ]);
    }

    /**
     * Édition groupée pour un élève et un trimestre
     */
    public function edit(Request $request, $inscriptionId)
    {
        $trimestre = $request->get('trimestre', '1er');

        $inscription = Inscription::with(['eleve', 'salle.niveau'])->findOrFail($inscriptionId);

        // Récupérer les matières du niveau de l'élève
        $matieres = Matiere::where('niveau_id', $inscription->salle->niveau_id)
            ->orderBy('nomMatiere')
            ->get();

        // Récupérer toutes les notes pour cet élève
        $notes = Note::where('inscription_id', $inscriptionId)
            ->get()
            ->groupBy('trimestre');

        return Inertia::render('Admin/Notes/Edit', [
            'inscription' => $inscription,
            'matieres' => $matieres,
            'enseignants' => Enseignant::orderBy('nom')->get(),
            'trimestres' => ['1er', '2ème', '3ème'],
            'notesGrouped' => $notes,
            'trimestreActuel' => $trimestre,
        ]);
    }

    /**
     * Mettre à jour les notes groupées
     */
    public function update(Request $request, $inscriptionId)
{
    $validated = $request->validate([
        'notes' => 'required|array',
        'notes.*.id' => 'nullable|exists:notes,id',
        'notes.*.matiere_id' => 'required|exists:matieres,id',
        'notes.*.trimestre' => 'required|in:1er,2ème,3ème',
        'notes.*.note' => 'required|numeric|min:0|max:20',
        'notes.*.enseignant_id' => 'nullable|exists:enseignants,id',
    ]);

    DB::beginTransaction();

    try {
        $notesCreees = 0;
        $notesMisesAJour = 0;

        foreach ($validated['notes'] as $noteData) {
            if ($noteData['note'] !== null && $noteData['note'] !== '') {
                if ($noteData['id']) {
                    // Vérifier que la note appartient bien à cette inscription
                    $note = Note::where('id', $noteData['id'])
                        ->where('inscription_id', $inscriptionId)
                        ->first();

                    if ($note) {
                        $note->update([
                            'note' => $noteData['note'],
                            'enseignant_id' => $noteData['enseignant_id'],
                            'date_saisie' => now()->format('Y-m-d'),
                        ]);
                        $notesMisesAJour++;
                    }
                } else {
                    // Créer une nouvelle note
                    Note::create([
                        'inscription_id' => $inscriptionId,
                        'matiere_id' => $noteData['matiere_id'],
                        'enseignant_id' => $noteData['enseignant_id'],
                        'trimestre' => $noteData['trimestre'],
                        'note' => $noteData['note'],
                        'date_saisie' => now()->format('Y-m-d'),
                    ]);
                    $notesCreees++;
                }
            }
        }

        DB::commit();

        $message = "Notes mises à jour avec succès.";
        if ($notesCreees > 0) {
            $message .= " $notesCreees nouvelle(s) note(s) créée(s).";
        }
        if ($notesMisesAJour > 0) {
            $message .= " $notesMisesAJour note(s) mise(s) à jour.";
        }

        return redirect()->route('notes.index', ['inscription_id' => $inscriptionId])
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur mise à jour notes: ' . $e->getMessage()); // Maintenant corrigé
        return back()->withErrors(['error' => 'Erreur lors de la mise à jour des notes: ' . $e->getMessage()]);
   
    }
}

    public function destroy($inscription)
    {
        $note = Note::findOrFail($inscription);
        $inscriptionId = $note->inscription_id;
        $note->delete();

        return redirect()->route('notes.index', ['inscription_id' => $inscriptionId])
            ->with('success', 'Note supprimée avec succès.');
    }

    /**
     * API pour récupérer les notes d'un élève
     */
    public function getNotesByInscription($inscriptionId)
    {
        $notes = Note::with(['matiere', 'enseignant'])
            ->where('inscription_id', $inscriptionId)
            ->get()
            ->groupBy('trimestre');

        return response()->json($notes);
    }
}