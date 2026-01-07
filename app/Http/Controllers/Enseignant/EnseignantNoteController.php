<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Matiere;
use App\Models\Inscription;
use App\Models\Enseignant;
use App\Models\Salle;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnseignantNoteController extends Controller
{
    /**
     * Afficher la liste des classes/matières de l'enseignant
     */
    public function index()
    {
        $enseignant = $this->getEnseignantConnecte();
        
        // Récupérer les matières enseignées par cet enseignant
        $matieres = Matiere::with(['niveau'])
            ->where('enseignant_id', $enseignant->id)
            ->orderBy('nomMatiere')
            ->get();
        
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', true)->first();
        
        // Récupérer les salles où l'enseignant donne cours (basé sur les matières et niveaux)
        $niveauIds = $matieres->pluck('niveau_id')->unique();
        
        $salles = Salle::with(['niveau'])
            ->whereIn('niveau_id', $niveauIds)
            ->when($anneeActive, function ($query) use ($anneeActive) {
                // Filtrer par les inscriptions de l'année active
                return $query->whereHas('inscriptions', function ($q) use ($anneeActive) {
                    $q->where('annee_scolaire_id', $anneeActive->id)
                      ->where('etat', 'active');
                });
            })
            ->orderBy('nomSalle')
            ->get()
            ->map(function ($salle) use ($matieres) {
                // Ajouter les matières enseignées dans cette salle
                $salle->matieres_enseignees = $matieres
                    ->where('niveau_id', $salle->niveau_id)
                    ->values();
                return $salle;
            });
        
        return Inertia::render('Enseignant/Notes/Index', [
            'enseignant' => $enseignant,
            'matieres' => $matieres,
            'salles' => $salles,
            'anneeActive' => $anneeActive,
        ]);
    }
    
    /**
     * Afficher les élèves d'une classe pour la saisie des notes
     */
    public function showEleves(Request $request, $salleId, $matiereId)
    {
        $enseignant = $this->getEnseignantConnecte();
        
        // Vérifier que l'enseignant enseigne bien cette matière
        $matiere = Matiere::where('id', $matiereId)
            ->where('enseignant_id', $enseignant->id)
            ->firstOrFail();
        
        $salle = Salle::with(['niveau'])->findOrFail($salleId);
        
        // Vérifier que la matière correspond au niveau de la salle
        if ($matiere->niveau_id !== $salle->niveau_id) {
            abort(403, 'Cette matière n\'est pas enseignée dans cette classe.');
        }
        
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', true)->firstOrFail();
        
        // Récupérer les inscriptions actives pour cette salle et année
        $inscriptions = Inscription::with(['eleve'])
            ->where('salle_id', $salleId)
            ->where('annee_scolaire_id', $anneeActive->id)
            ->where('etat', 'active')
            ->orderBy('eleve_id')
            ->get()
            ->map(function ($inscription) use ($matiereId) {
                // Récupérer les notes existantes pour cette matière
                $inscription->notes = Note::where('inscription_id', $inscription->id)
                    ->where('matiere_id', $matiereId)
                    ->get()
                    ->keyBy('trimestre');
                return $inscription;
            });
        
        $trimestre = $request->get('trimestre', '1er');
        
        return Inertia::render('Enseignant/Notes/Saisie', [
            'enseignant' => $enseignant,
            'matiere' => $matiere,
            'salle' => $salle,
            'anneeActive' => $anneeActive,
            'inscriptions' => $inscriptions,
            'trimestre' => $trimestre,
            'trimestres' => ['1er', '2ème', '3ème'],
        ]);
    }
    
    /**
     * Sauvegarder les notes des élèves
     */
    public function storeNotes(Request $request)
    {
        $enseignant = $this->getEnseignantConnecte();
        
        $validated = $request->validate([
            'salle_id' => 'required|exists:salles,id',
            'matiere_id' => 'required|exists:matieres,id',
            'trimestre' => 'required|in:1er,2ème,3ème',
            'notes' => 'required|array',
            'notes.*.inscription_id' => 'required|exists:inscriptions,id',
            'notes.*.note' => 'nullable|numeric|min:0|max:20',
        ]);
        
        // Vérifier que l'enseignant enseigne bien cette matière
        $matiere = Matiere::where('id', $validated['matiere_id'])
            ->where('enseignant_id', $enseignant->id)
            ->firstOrFail();
        
        // Vérifier que la salle correspond au niveau de la matière
        $salle = Salle::findOrFail($validated['salle_id']);
        if ($matiere->niveau_id !== $salle->niveau_id) {
            return back()->withErrors(['error' => 'Cette matière n\'est pas enseignée dans cette classe.']);
        }
        
        DB::beginTransaction();
        
        try {
            $notesCreees = 0;
            $notesMisesAJour = 0;
            
            foreach ($validated['notes'] as $noteData) {
                if ($noteData['note'] !== null && $noteData['note'] !== '') {
                    // Vérifier si une note existe déjà
                    $noteExistante = Note::where('inscription_id', $noteData['inscription_id'])
                        ->where('matiere_id', $validated['matiere_id'])
                        ->where('trimestre', $validated['trimestre'])
                        ->first();
                    
                    if ($noteExistante) {
                        // Mettre à jour la note existante
                        $noteExistante->update([
                            'note' => $noteData['note'],
                            'enseignant_id' => $enseignant->id,
                            'date_saisie' => now()->format('Y-m-d'),
                        ]);
                        $notesMisesAJour++;
                    } else {
                        // Créer une nouvelle note
                        Note::create([
                            'inscription_id' => $noteData['inscription_id'],
                            'matiere_id' => $validated['matiere_id'],
                            'enseignant_id' => $enseignant->id,
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
            
            return redirect()
                ->route('enseignant.notes.eleves', [
                    'salle' => $validated['salle_id'],
                    'matiere' => $validated['matiere_id'],
                    'trimestre' => $validated['trimestre']
                ])
                ->with('success', $message);
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur saisie notes enseignant: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement des notes: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Consulter l'historique des notes pour une matière
     */
    public function historiqueMatiere($matiereId)
    {
        $enseignant = $this->getEnseignantConnecte();
        
        // Vérifier que l'enseignant enseigne bien cette matière
        $matiere = Matiere::with(['niveau'])
            ->where('id', $matiereId)
            ->where('enseignant_id', $enseignant->id)
            ->firstOrFail();
        
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', true)->firstOrFail();
        
        // Récupérer toutes les notes pour cette matière et cet enseignant
        $notes = Note::with([
                'inscription.eleve',
                'inscription.salle.niveau'
            ])
            ->where('matiere_id', $matiereId)
            ->where('enseignant_id', $enseignant->id)
            ->whereHas('inscription', function ($query) use ($anneeActive) {
                $query->where('annee_scolaire_id', $anneeActive->id);
            })
            ->orderBy('trimestre')
            ->orderBy('inscription_id')
            ->get()
            ->groupBy('trimestre');
        
        // Calculer les statistiques
        $statistiques = [];
        foreach ($notes as $trimestre => $notesTrimestre) {
            $notesArray = $notesTrimestre->pluck('note')->filter()->toArray();
            
            if (count($notesArray) > 0) {
                $statistiques[$trimestre] = [
                    'total_eleves' => count($notesArray),
                    'moyenne_classe' => round(array_sum($notesArray) / count($notesArray), 2),
                    'note_max' => max($notesArray),
                    'note_min' => min($notesArray),
                    'notes_sup_10' => count(array_filter($notesArray, function($note) {
                        return $note >= 10;
                    })),
                ];
            }
        }
        
        return Inertia::render('Enseignant/Notes/Historique', [
            'enseignant' => $enseignant,
            'matiere' => $matiere,
            'anneeActive' => $anneeActive,
            'notesParTrimestre' => $notes,
            'statistiques' => $statistiques,
            'trimestres' => ['1er', '2ème', '3ème'],
        ]);
    }
    
    /**
     * Afficher les statistiques générales de l'enseignant
     */
    public function statistiques()
    {
        $enseignant = $this->getEnseignantConnecte();
        
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', true)->firstOrFail();
        
        // Récupérer les matières de l'enseignant
        $matieres = Matiere::with(['niveau'])
            ->where('enseignant_id', $enseignant->id)
            ->get();
        
        // Récupérer les statistiques par matière
        $statistiquesMatieres = [];
        $totalEleves = 0;
        $totalNotes = 0;
        $moyenneGenerale = 0;
        
        foreach ($matieres as $matiere) {
            $notes = Note::where('matiere_id', $matiere->id)
                ->where('enseignant_id', $enseignant->id)
                ->whereHas('inscription', function ($query) use ($anneeActive) {
                    $query->where('annee_scolaire_id', $anneeActive->id);
                })
                ->get();
            
            if ($notes->count() > 0) {
                $notesArray = $notes->pluck('note')->filter()->toArray();
                $moyenne = round(array_sum($notesArray) / count($notesArray), 2);
                
                $statistiquesMatieres[] = [
                    'matiere' => $matiere,
                    'total_notes' => count($notesArray),
                    'moyenne' => $moyenne,
                    'note_max' => max($notesArray),
                    'note_min' => min($notesArray),
                    'taux_reussite' => round(count(array_filter($notesArray, function($note) {
                        return $note >= 10;
                    })) / count($notesArray) * 100, 1)
                ];
                
                $totalEleves += count($notesArray);
                $totalNotes += array_sum($notesArray);
            }
        }
        
        if ($totalEleves > 0) {
            $moyenneGenerale = round($totalNotes / $totalEleves, 2);
        }
        
        return Inertia::render('Enseignant/Notes/Statistiques', [
            'enseignant' => $enseignant,
            'anneeActive' => $anneeActive,
            'statistiquesMatieres' => $statistiquesMatieres,
            'moyenneGenerale' => $moyenneGenerale,
            'totalEleves' => $totalEleves,
        ]);
    }
    
    /**
     * Modifier une note individuelle
     */
    public function editNote($noteId)
    {
        $enseignant = $this->getEnseignantConnecte();
        
        if (!$enseignant) {
            abort(403, 'Accès refusé. Vous n\'êtes pas un enseignant.');
        }
        
        // Récupérer la note avec toutes les relations nécessaires
        $note = Note::with([
            'inscription.eleve', 
            'matiere', 
            'inscription.salle.niveau'
        ])
        ->where('id', $noteId)
        ->where('enseignant_id', $enseignant->id)
        ->firstOrFail();
        
        // Récupérer les autres notes de cet élève dans cette matière
        $otherNotes = Note::where('inscription_id', $note->inscription_id)
            ->where('matiere_id', $note->matiere_id)
            ->where('id', '!=', $noteId)
            ->orderBy('trimestre')
            ->get();
        
        // Extraire les données pour simplifier l'accès dans la vue
        $data = [
            'enseignant' => $enseignant,
            'note' => $note,
            'otherNotes' => $otherNotes,
            'trimestres' => ['1er', '2ème', '3ème'],
            // Données supplémentaires pour faciliter l'accès
            'eleve' => $note->inscription->eleve,
            'matiere' => $note->matiere,
            'classe' => [
                'nomSalle' => $note->inscription->salle->nomSalle,
                'niveau' => $note->inscription->salle->niveau
            ]
        ];
        
        return Inertia::render('Enseignant/Notes/EditNote', $data);
    }
    
    /**
     * Mettre à jour une note individuelle
     */
    public function updateNote(Request $request, $noteId)
    {
        $enseignant = $this->getEnseignantConnecte();
        
        if (!$enseignant) {
            abort(403, 'Accès refusé.');
        }
        
        // Vérifier que la note appartient à cet enseignant
        $note = Note::where('id', $noteId)
            ->where('enseignant_id', $enseignant->id)
            ->firstOrFail();
        
        $validated = $request->validate([
            'note' => [
                'required',
                'numeric',
                'min:0',
                'max:20',
                'regex:/^\d+(\.\d{1,2})?$/'
            ],
            'trimestre' => 'required|in:1er,2ème,3ème',
            'comment' => 'nullable|string|max:500',
        ]);
        
        // Vérifier s'il existe déjà une note pour cet élève dans cette matière et trimestre
        $noteExistante = Note::where('inscription_id', $note->inscription_id)
            ->where('matiere_id', $note->matiere_id)
            ->where('trimestre', $validated['trimestre'])
            ->where('id', '!=', $noteId)
            ->first();
        
        if ($noteExistante) {
            return back()->withErrors(['error' => 'Une note existe déjà pour cette matière et ce trimestre.']);
        }
        
        $note->update([
            'note' => $validated['note'],
            'trimestre' => $validated['trimestre'],
            'date_saisie' => now()->format('Y-m-d'),
            'commentaire' => $validated['comment'] ?? null,
        ]);
        
        return redirect()
            ->route('enseignant.notes.historique', $note->matiere_id)
            ->with('success', 'Note mise à jour avec succès.');
    }
    
    /**
     * Récupérer l'enseignant connecté
     */
    private function getEnseignantConnecte()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return null;
            }
            
            // Vérifier le rôle
            if ($user->role !== 'enseignant') {
                return null;
            }
            
            // Récupérer l'enseignant associé
            $enseignant = Enseignant::where('user_id', $user->id)->first();
            
            if (!$enseignant) {
                return null;
            }
            
            return $enseignant;
            
        } catch (\Exception $e) {
            Log::error('Erreur récupération enseignant: ' . $e->getMessage());
            return null;
        }
    }
}