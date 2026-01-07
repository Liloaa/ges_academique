<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EleveNoteController extends Controller
{
    /**
     * Afficher les notes de l'élève connecté
     */
    public function index(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
        
        // Vérifier que l'utilisateur est bien un élève
        if (!$user->isEleve()) {
            abort(403, 'Accès non autorisé.');
        }
        
        // Récupérer l'élève lié à cet utilisateur
        $eleve = $user->eleve;
        
        if (!$eleve) {
            return Inertia::render('Eleve/Notes/Index', [
                'error' => 'Aucun profil élève trouvé pour votre compte.',
                'notes' => [],
                'inscriptionActive' => null,
                'resultats' => [
                    'matieres' => [],
                    'moyennes_trimestrielles' => []
                ],
                'moyenneGenerale' => 0,
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
        
        if (!$inscriptionActive) {
            // Si pas d'inscription active, prendre la dernière inscription
            $inscriptionActive = Inscription::with(['salle.niveau', 'annee'])
                ->where('eleve_id', $eleve->id)
                ->orderBy('date_inscription', 'desc')
                ->first();
        }
        
        // Initialiser les résultats
        $resultats = [
            'matieres' => [],
            'moyennes_trimestrielles' => [],
        ];
        $moyenneGenerale = 0;
        
        if ($inscriptionActive) {
            // Calculer les résultats si une inscription existe
            $resultats = $this->calculerResultatsEleve($inscriptionActive->id);
            $moyenneGenerale = $this->calculerMoyenneGenerale($resultats);
        }
        
        return Inertia::render('Eleve/Notes/Index', [
            'eleve' => $eleve,
            'inscriptionActive' => $inscriptionActive,
            'resultats' => $resultats,
            'moyenneGenerale' => $moyenneGenerale,
            'trimestres' => ['1er', '2ème', '3ème'],
        ]);
    }
    
    /**
     * Calculer les résultats détaillés d'un élève
     */
    private function calculerResultatsEleve($inscriptionId)
    {
        $notes = Note::with(['matiere'])
            ->where('inscription_id', $inscriptionId)
            ->get();
        
        $resultatsParMatiere = [];
        $totauxTrimestres = [
            '1er' => ['points' => 0, 'coefficients' => 0],
            '2ème' => ['points' => 0, 'coefficients' => 0],
            '3ème' => ['points' => 0, 'coefficients' => 0],
        ];
        
        foreach ($notes as $note) {
            $matiereId = $note->matiere_id;
            $trimestre = $note->trimestre;
            
            if (!isset($resultatsParMatiere[$matiereId])) {
                $resultatsParMatiere[$matiereId] = [
                    'matiere' => $note->matiere,
                    'coefficient' => $note->matiere->coefficient ?? 1,
                    'trimestres' => [
                        '1er' => null,
                        '2ème' => null,
                        '3ème' => null,
                    ],
                    'moyenne_annuelle' => 0,
                ];
            }
            
            $noteValue = floatval($note->note);
            $resultatsParMatiere[$matiereId]['trimestres'][$trimestre] = $noteValue;
            
            // Calculer les totaux pour les moyennes trimestrielles
            if ($noteValue > 0) {
                $coefficient = $note->matiere->coefficient ?? 1;
                $totauxTrimestres[$trimestre]['points'] += $noteValue * $coefficient;
                $totauxTrimestres[$trimestre]['coefficients'] += $coefficient;
            }
        }
        
        // Calculer les moyennes annuelles par matière
        foreach ($resultatsParMatiere as $matiereId => &$resultat) {
            $notesValides = array_filter($resultat['trimestres'], function ($note) {
                return $note !== null;
            });
            
            if (count($notesValides) > 0) {
                $resultat['moyenne_annuelle'] = round(array_sum($notesValides) / count($notesValides), 2);
            }
        }
        
        // Calculer les moyennes par trimestre
        $moyennesTrimestrielles = [];
        foreach ($totauxTrimestres as $trimestre => $data) {
            if ($data['coefficients'] > 0) {
                $moyennesTrimestrielles[$trimestre] = round($data['points'] / $data['coefficients'], 2);
            } else {
                $moyennesTrimestrielles[$trimestre] = 0;
            }
        }
        
        return [
            'matieres' => array_values($resultatsParMatiere),
            'moyennes_trimestrielles' => $moyennesTrimestrielles,
        ];
    }
    
    /**
     * Calculer la moyenne générale
     */
    private function calculerMoyenneGenerale($resultats)
    {
        if (!isset($resultats['moyennes_trimestrielles'])) {
            return 0;
        }
        
        $moyennes = array_filter($resultats['moyennes_trimestrielles'], function ($moyenne) {
            return $moyenne > 0;
        });
        
        if (count($moyennes) > 0) {
            return round(array_sum($moyennes) / count($moyennes), 2);
        }
        
        return 0;
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
    
    /**
     * Voir le bulletin détaillé
     */
    public function bulletin()
    {
        $user = Auth::user();
        
        if (!$user->isEleve()) {
            abort(403, 'Accès non autorisé.');
        }
        
        $eleve = $user->eleve;
        
        if (!$eleve) {
            return redirect()->route('eleve.notes.index')
                ->with('error', 'Aucun profil élève trouvé.');
        }
        
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', true)->first();
        
        // Récupérer l'inscription active
        $inscriptionActive = Inscription::with(['salle.niveau', 'annee'])
            ->where('eleve_id', $eleve->id)
            ->when($anneeActive, function ($query) use ($anneeActive) {
                return $query->where('annee_scolaire_id', $anneeActive->id);
            })
            ->where('etat', 'active')
            ->first();
        
        if (!$inscriptionActive) {
            return redirect()->route('eleve.notes.index')
                ->with('error', 'Aucune inscription active trouvée.');
        }
        
        // Calculer les résultats
        $resultats = $this->calculerResultatsEleve($inscriptionActive->id);
        $moyenneGenerale = $this->calculerMoyenneGenerale($resultats);
        $appreciation = $this->getAppreciation($moyenneGenerale);
        
        return Inertia::render('Eleve/Notes/Bulletin', [
            'eleve' => $eleve,
            'inscriptionActive' => $inscriptionActive,
            'resultats' => $resultats,
            'moyenneGenerale' => $moyenneGenerale,
            'appreciation' => $appreciation,
            'trimestres' => ['1er', '2ème', '3ème'],
        ]);
    }
}