<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use App\Models\Enseignant;
use App\Models\AnneeScolaire;
use App\Models\Inscription;
use App\Models\Salle;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EnseignantDashboardController extends Controller
{
    public function index()
    {
        // Récupérer l'enseignant connecté
        $user = Auth::user();
        $enseignant = Enseignant::where('user_id', $user->id)->first();
        
        if (!$enseignant) {
            return Inertia::render('Enseignant/Index', [
                'classes' => [],
                'statistiques' => [],
                'matieres' => [],
                'salles' => [],
                'enseignant' => null
            ]);
        }
        
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', 1)->first();
        
        // 1. Liste des classes attribuées (matière, niveau, effectif)
        $classes = $this->getClassesAttribuees($enseignant->id, $anneeActive ? $anneeActive->id : null);
        
        // 2. Statistiques générales
        $statistiques = $this->getStatistiquesEnseignant($enseignant->id, $anneeActive ? $anneeActive->id : null);
        
        // 3. Prochaines évaluations (si vous avez ce modèle)
        $prochainesEvaluations = $this->getProchainesEvaluations($enseignant->id);
        
        // 4. Matières enseignées
        $matieres = $this->getMatieresEnseignees($enseignant->id);
        
        // 5. Salles avec matières enseignées
        $salles = $this->getSallesAvecMatieres($enseignant->id, $anneeActive ? $anneeActive->id : null);
        
        return Inertia::render('Enseignant/Index', [
            'classes' => $classes,
            'statistiques' => $statistiques,
            'prochainesEvaluations' => $prochainesEvaluations,
            'enseignant' => [
                'id' => $enseignant->id,
                'nom' => $enseignant->nom,
                'prenom' => $enseignant->prenom,
                'specialite' => $enseignant->specialite,
            ],
            'matieres' => $matieres,
            'salles' => $salles,
            'anneeActive' => $anneeActive,
        ]);
    }
    
    /**
     * Récupérer les classes attribuées à un enseignant
     */
    private function getClassesAttribuees($enseignantId, $anneeActiveId)
    {
        // Récupérer les matières enseignées par cet enseignant
        $matieres = Matiere::where('enseignant_id', $enseignantId)
            ->with(['niveau.filiere', 'niveau.salles'])
            ->get();
        
        $classes = [];
        
        foreach ($matieres as $matiere) {
            // Pour chaque matière, récupérer les salles (classes) du niveau
            foreach ($matiere->niveau->salles as $salle) {
                // Calculer l'effectif pour cette salle dans l'année active
                $effectif = 0;
                if ($anneeActiveId) {
                    $effectif = Inscription::where('salle_id', $salle->id)
                        ->where('annee_scolaire_id', $anneeActiveId)
                        ->where('etat', 'active')
                        ->count();
                }
                
                $classes[] = [
                    'matiere' => [
                        'id' => $matiere->id,
                        'nom' => $matiere->nomMatiere,
                        'coefficient' => $matiere->coefficient,
                    ],
                    'niveau' => [
                        'id' => $matiere->niveau->id,
                        'nom' => $matiere->niveau->nomNiveau,
                        'cycle' => $matiere->niveau->cycle,
                        'filiere' => $matiere->niveau->filiere ? $matiere->niveau->filiere->nomFiliere : null,
                    ],
                    'salle' => [
                        'id' => $salle->id,
                        'nom' => $salle->nomSalle,
                        'capacite' => $salle->capacite,
                    ],
                    'effectif' => $effectif,
                ];
            }
        }
        
        // Grouper par niveau pour une meilleure organisation
        $groupedClasses = [];
        foreach ($classes as $classe) {
            $niveauKey = $classe['niveau']['id'];
            
            if (!isset($groupedClasses[$niveauKey])) {
                $groupedClasses[$niveauKey] = [
                    'niveau' => $classe['niveau'],
                    'salle' => $classe['salle'],
                    'effectif' => $classe['effectif'],
                    'matieres' => [],
                ];
            }
            
            $groupedClasses[$niveauKey]['matieres'][] = $classe['matiere'];
        }
        
        return array_values($groupedClasses);
    }
    
    /**
     * Statistiques pour l'enseignant
     */
    private function getStatistiquesEnseignant($enseignantId, $anneeActiveId)
    {
        $statistiques = [];
        
        // Nombre total de matières enseignées
        $statistiques['totalMatieres'] = Matiere::where('enseignant_id', $enseignantId)->count();
        
        // Nombre total d'élèves (en sommant les effectifs de toutes les classes)
        $totalEleves = 0;
        $matieres = Matiere::where('enseignant_id', $enseignantId)
            ->with(['niveau.salles'])
            ->get();
        
        foreach ($matieres as $matiere) {
            foreach ($matiere->niveau->salles as $salle) {
                if ($anneeActiveId) {
                    $effectif = Inscription::where('salle_id', $salle->id)
                        ->where('annee_scolaire_id', $anneeActiveId)
                        ->where('etat', 'active')
                        ->count();
                    $totalEleves += $effectif;
                }
            }
        }
        
        $statistiques['totalEleves'] = $totalEleves;
        
        // Nombre de niveaux différents enseignés
        $niveauxIds = Matiere::where('enseignant_id', $enseignantId)
            ->pluck('niveau_id')
            ->unique()
            ->count();
        
        $statistiques['totalNiveaux'] = $niveauxIds;
        
        return $statistiques;
    }
    
    /**
     * Récupérer les prochaines évaluations
     */
    private function getProchainesEvaluations($enseignantId)
    {
        // À implémenter selon votre modèle d'évaluations
        return [];
    }
    
    /**
     * Récupérer les matières enseignées
     */
    private function getMatieresEnseignees($enseignantId)
    {
        $matieres = Matiere::where('enseignant_id', $enseignantId)
            ->with('niveau.filiere')
            ->get()
            ->map(function ($matiere) {
                return [
                    'id' => $matiere->id,
                    'nomMatiere' => $matiere->nomMatiere,
                    'coefficient' => $matiere->coefficient,
                    'niveau' => $matiere->niveau ? [
                        'id' => $matiere->niveau->id,
                        'nom' => $matiere->niveau->nomNiveau,
                        'cycle' => $matiere->niveau->cycle,
                        'filiere' => $matiere->niveau->filiere ? [
                            'nom' => $matiere->niveau->filiere->nomFiliere
                        ] : null,
                    ] : null,
                ];
            });
        
        return $matieres;
    }
    
    /**
     * Récupérer les salles avec matières enseignées
     */
    private function getSallesAvecMatieres($enseignantId, $anneeActiveId)
    {
        // Récupérer toutes les matières de l'enseignant avec leurs niveaux
        $matieres = Matiere::where('enseignant_id', $enseignantId)
            ->with(['niveau.salles'])
            ->get();
        
        $sallesData = [];
        
        foreach ($matieres as $matiere) {
            if (!$matiere->niveau) continue;
            
            foreach ($matiere->niveau->salles as $salle) {
                $salleId = $salle->id;
                
                if (!isset($sallesData[$salleId])) {
                    // Calculer l'effectif
                    $effectif = 0;
                    if ($anneeActiveId) {
                        $effectif = Inscription::where('salle_id', $salleId)
                            ->where('annee_scolaire_id', $anneeActiveId)
                            ->where('etat', 'active')
                            ->count();
                    }
                    
                    $sallesData[$salleId] = [
                        'id' => $salle->id,
                        'nomSalle' => $salle->nomSalle,
                        'effectif' => $effectif,
                        'capacite' => $salle->capacite,
                        'niveau' => [
                            'id' => $matiere->niveau->id,
                            'nom' => $matiere->niveau->nomNiveau,
                            'cycle' => $matiere->niveau->cycle,
                            'filiere' => $matiere->niveau->filiere ? [
                                'nom' => $matiere->niveau->filiere->nomFiliere
                            ] : null,
                        ],
                        'matieres_enseignees' => [],
                    ];
                }
                
                // Ajouter la matière à la salle si elle n'y est pas déjà
                $matiereExistante = collect($sallesData[$salleId]['matieres_enseignees'])
                    ->where('id', $matiere->id)
                    ->first();
                
                if (!$matiereExistante) {
                    $sallesData[$salleId]['matieres_enseignees'][] = [
                        'id' => $matiere->id,
                        'nomMatiere' => $matiere->nomMatiere,
                        'coefficient' => $matiere->coefficient,
                    ];
                }
            }
        }
        
        return array_values($sallesData);
    }
}