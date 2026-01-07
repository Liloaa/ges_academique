<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Inscription;
use App\Models\AnneeScolaire;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EleveDashboardController extends Controller
{
    public function index()
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        if (!$user->isEleve()) {
            abort(403, 'Accès non autorisé.');
        }
        
        // Récupérer l'élève lié à cet utilisateur
        $eleve = $user->eleve;
        
        if (!$eleve) {
            return Inertia::render('Eleve/Index', [
                'error' => 'Aucun profil élève trouvé pour votre compte.',
                'dashboardData' => null
            ]);
        }
        
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', true)->first();
        
        // Récupérer l'inscription active de l'élève pour l'année en cours
        $inscriptionActive = Inscription::with(['salle.niveau', 'annee'])
            ->where('eleve_id', $eleve->id)
            ->when($anneeActive, function ($query) use ($anneeActive) {
                return $query->where('annee_scolaire_id', $anneeActive->id);
            })
            ->where('etat', 'active')
            ->first();
        
        // Initialiser les données du tableau de bord
        $dashboardData = [
            'inscription' => $inscriptionActive,
            'moyenne_generale' => 0,
            'taux_reussite' => 0,
            'statistiques_notes' => [],
            'dernieres_notes' => []
        ];
        
        if ($inscriptionActive) {
            // Calculer la moyenne générale
            $moyenneGenerale = $this->calculerMoyenneGenerale($inscriptionActive->id);
            
            // Calculer le taux de réussite
            $tauxReussite = $this->calculerTauxReussite($inscriptionActive->id);
            
            // Récupérer les statistiques des notes
            $statistiquesNotes = $this->getStatistiquesNotes($inscriptionActive->id);
            
            // Récupérer les dernières notes
            $dernieresNotes = $this->getDernieresNotes($inscriptionActive->id);
            
            $dashboardData = [
                'inscription' => $inscriptionActive,
                'moyenne_generale' => $moyenneGenerale,
                'taux_reussite' => $tauxReussite,
                'statistiques_notes' => $statistiquesNotes,
                'dernieres_notes' => $dernieresNotes,
                'appreciation' => $this->getAppreciation($moyenneGenerale)
            ];
        }
        
        return Inertia::render('Eleve/Index', [
            'eleve' => $eleve,
            'dashboardData' => $dashboardData,
            'anneeActive' => $anneeActive
        ]);
    }
    
    /**
     * Calculer la moyenne générale de l'élève
     */
    private function calculerMoyenneGenerale($inscriptionId)
    {
        $notes = Note::with(['matiere'])
            ->where('inscription_id', $inscriptionId)
            ->get();
        
        if ($notes->isEmpty()) {
            return 0;
        }
        
        $totalPoints = 0;
        $totalCoefficients = 0;
        
        // Regrouper les notes par matière et par trimestre
        $matieres = [];
        
        foreach ($notes as $note) {
            $matiereId = $note->matiere_id;
            $trimestre = $note->trimestre;
            
            if (!isset($matieres[$matiereId])) {
                $matieres[$matiereId] = [
                    'coefficient' => $note->matiere->coefficient ?? 1,
                    'trimestres' => [],
                ];
            }
            
            $matieres[$matiereId]['trimestres'][$trimestre] = floatval($note->note);
        }
        
        // Calculer la moyenne pour chaque matière (moyenne des trimestres)
        foreach ($matieres as $matiereId => $matiereData) {
            $notesMatiere = array_filter($matiereData['trimestres'], function ($note) {
                return $note !== null;
            });
            
            if (count($notesMatiere) > 0) {
                $moyenneMatiere = array_sum($notesMatiere) / count($notesMatiere);
                $totalPoints += $moyenneMatiere * $matiereData['coefficient'];
                $totalCoefficients += $matiereData['coefficient'];
            }
        }
        
        if ($totalCoefficients > 0) {
            return round($totalPoints / $totalCoefficients, 2);
        }
        
        return 0;
    }
    
    /**
     * Calculer le taux de réussite
     */
    private function calculerTauxReussite($inscriptionId)
    {
        $notes = Note::with(['matiere'])
            ->where('inscription_id', $inscriptionId)
            ->get();
        
        if ($notes->isEmpty()) {
            return 0;
        }
        
        // Regrouper par matière
        $matieres = [];
        
        foreach ($notes as $note) {
            $matiereId = $note->matiere_id;
            $trimestre = $note->trimestre;
            
            if (!isset($matieres[$matiereId])) {
                $matieres[$matiereId] = [];
            }
            
            $matieres[$matiereId][$trimestre] = floatval($note->note);
        }
        
        $matieresReussies = 0;
        $totalMatieres = count($matieres);
        
        // Une matière est réussie si la moyenne des trimestres >= 10
        foreach ($matieres as $matiereId => $notesTrimestres) {
            $notesValides = array_filter($notesTrimestres, function ($note) {
                return $note !== null;
            });
            
            if (count($notesValides) > 0) {
                $moyenneMatiere = array_sum($notesValides) / count($notesValides);
                if ($moyenneMatiere >= 10) {
                    $matieresReussies++;
                }
            }
        }
        
        if ($totalMatieres > 0) {
            return round(($matieresReussies / $totalMatieres) * 100, 1);
        }
        
        return 0;
    }
    
    /**
     * Récupérer les statistiques des notes
     */
    private function getStatistiquesNotes($inscriptionId)
    {
        $notes = Note::where('inscription_id', $inscriptionId)->get();
        
        if ($notes->isEmpty()) {
            return [
                'total_notes' => 0,
                'moyenne_max' => 0,
                'moyenne_min' => 0,
                'notes_par_trimestre' => []
            ];
        }
        
        // Statistiques par trimestre
        $trimestres = ['1er' => 0, '2ème' => 0, '3ème' => 0];
        $notesParTrimestre = [];
        
        foreach ($trimestres as $trimestre => $value) {
            $notesTrimestre = $notes->where('trimestre', $trimestre);
            if ($notesTrimestre->isNotEmpty()) {
                $moyenne = $notesTrimestre->avg('note');
                $notesParTrimestre[$trimestre] = [
                    'moyenne' => round($moyenne, 2),
                    'nombre_notes' => $notesTrimestre->count()
                ];
            } else {
                $notesParTrimestre[$trimestre] = [
                    'moyenne' => 0,
                    'nombre_notes' => 0
                ];
            }
        }
        
        return [
            'total_notes' => $notes->count(),
            'moyenne_max' => round($notes->max('note'), 2),
            'moyenne_min' => round($notes->min('note'), 2),
            'notes_par_trimestre' => $notesParTrimestre
        ];
    }
    
    /**
     * Récupérer les dernières notes
     */
    private function getDernieresNotes($inscriptionId)
    {
        $notes = Note::with(['matiere'])
            ->where('inscription_id', $inscriptionId)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return $notes->map(function ($note) {
            return [
                'matiere' => $note->matiere->nomMatiere ?? 'Inconnue',
                'note' => $note->note,
                'trimestre' => $note->trimestre,
                'date' => $note->created_at->format('d/m/Y'),
                'coefficient' => $note->matiere->coefficient ?? 1
            ];
        });
    }
    
    /**
     * Obtenir l'appréciation selon la moyenne
     */
    private function getAppreciation($moyenne)
    {
        if ($moyenne >= 16) return 'Excellent';
        if ($moyenne >= 14) return 'Très Bien';
        if ($moyenne >= 12) return 'Bien';
        if ($moyenne >= 10) return 'Assez Bien';
        if ($moyenne >= 8) return 'Passable';
        return 'Insuffisant';
    }
}