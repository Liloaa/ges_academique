<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ResultatController extends Controller
{
    public function index(Request $request)
    {
        $eleveId = $request->get('eleve_id');
        $anneeId = $request->get('annee_id');
        
        // Si un élève spécifique est demandé
        if ($eleveId) {
            return $this->showEleveResultats($eleveId, $anneeId);
        }
        
        // Sinon, vue générale des résultats
        return $this->showAllResultats($request);
    }

    /**
     * Afficher les résultats d'un élève spécifique
     */
    private function showEleveResultats($eleveId, $anneeId = null)
    {
        $eleve = Eleve::with(['user'])->findOrFail($eleveId);
        
        // Récupérer l'année scolaire active si aucune n'est spécifiée
        if (!$anneeId) {
            $anneeActive = AnneeScolaire::where('active', 1)->first();
            $anneeId = $anneeActive ? $anneeActive->id : null;
        }

        // Récupérer l'inscription active de l'élève
        $inscription = Inscription::with(['salle.niveau', 'annee'])
            ->where('eleve_id', $eleveId)
            ->when($anneeId, function ($query) use ($anneeId) {
                return $query->where('annee_scolaire_id', $anneeId);
            })
            ->where('etat', 'active')
            ->first();

        if (!$inscription) {
            return Inertia::render('Admin/Resultats/Eleve', [
                'eleve' => $eleve,
                'inscription' => null,
                'resultats' => [
                    'matieres' => [],
                    'moyennes_trimestrielles' => []
                ],
                'moyenneGenerale' => 0,
                'annees' => AnneeScolaire::orderBy('libelle', 'desc')->get(),
            ]);
        }

        // Calculer les résultats par matière et par trimestre
        $resultats = $this->calculerResultatsEleve($inscription->id);
        $moyenneGenerale = $this->calculerMoyenneGenerale($resultats);

        return Inertia::render('Admin/Resultats/Eleve', [
            'eleve' => $eleve,
            'inscription' => $inscription,
            'resultats' => $resultats,
            'moyenneGenerale' => $moyenneGenerale,
            'annees' => AnneeScolaire::orderBy('libelle', 'desc')->get(),
            'anneeCourante' => $anneeId,
        ]);
    }

    /**
     * Afficher tous les résultats (vue générale)
     */
    private function showAllResultats(Request $request)
    {
        $anneeId = $request->get('annee_id');
        $niveauId = $request->get('niveau_id');
        $search = $request->get('search');

        // Récupérer l'année scolaire active si aucune n'est spécifiée
        if (!$anneeId) {
            $anneeActive = AnneeScolaire::where('active', 1)->first();
            $anneeId = $anneeActive ? $anneeActive->id : null;
        }

        // Récupérer les inscriptions avec calcul des moyennes
        $query = Inscription::with(['eleve', 'salle.niveau', 'annee'])
            ->where('etat', 'active')
            ->when($anneeId, function ($q) use ($anneeId) {
                return $q->where('annee_scolaire_id', $anneeId);
            })
            ->when($niveauId, function ($q) use ($niveauId) {
                return $q->whereHas('salle', function ($q2) use ($niveauId) {
                    return $q2->where('niveau_id', $niveauId);
                });
            })
            ->when($search, function ($q) use ($search) {
                return $q->whereHas('eleve', function ($q2) use ($search) {
                    return $q2->where('nom', 'like', "%{$search}%")
                             ->orWhere('prenom', 'like', "%{$search}%")
                             ->orWhere('matricule', 'like', "%{$search}%");
                });
            });

        $inscriptions = $query->get()->map(function ($inscription) {
            $resultats = $this->calculerResultatsEleve($inscription->id);
            $moyenne = $this->calculerMoyenneGenerale($resultats);
            
            return [
                'id' => $inscription->id,
                'eleve' => $inscription->eleve,
                'salle' => $inscription->salle,
                'annee' => $inscription->annee,
                'moyenne_generale' => $moyenne,
                'appreciation' => $this->getAppreciation($moyenne),
            ];
        });

        // Trier par moyenne décroissante
        $inscriptions = $inscriptions->sortByDesc('moyenne_generale')->values();

        return Inertia::render('Admin/Resultats/Index', [
            'inscriptions' => $inscriptions,
            'annees' => AnneeScolaire::orderBy('libelle', 'desc')->get(),
            'niveaux' => \App\Models\Niveau::with('salles')->get(),
            'filters' => [
                'annee_id' => $anneeId,
                'niveau_id' => $niveauId,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Calculer les résultats détaillés d'un élève avec moyennes par trimestre
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
                    'coefficient' => $note->matiere->coefficient,
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
                $totauxTrimestres[$trimestre]['points'] += $noteValue * $note->matiere->coefficient;
                $totauxTrimestres[$trimestre]['coefficients'] += $note->matiere->coefficient;
            }
        }

        // Calculer les moyennes annuelles par matière
        foreach ($resultatsParMatiere as $matiereId => &$resultat) {
            $notesValides = array_filter($resultat['trimestres'], function ($note) {
                return $note !== null;
            });

            if (count($notesValides) > 0) {
                $resultat['moyenne_annuelle'] = array_sum($notesValides) / count($notesValides);
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
     * Calculer la moyenne générale d'un élève (moyenne des 3 trimestres)
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
            return round(array_sum($moyennes) / 3 , 2);
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
     * Générer un bulletin PDF
     */
    public function generateBulletin($eleveId, $anneeId = null)
    {
        // Cette méthode peut être implémentée plus tard pour générer un PDF
        // Pour l'instant, on redirige vers la vue des résultats
        return redirect()->route('resultats.index', ['eleve_id' => $eleveId, 'annee_id' => $anneeId]);
    }

    /**
     * Statistiques des résultats par niveau
     */
    public function statistiques(Request $request)
    {
        $anneeId = $request->get('annee_id');
        
        if (!$anneeId) {
            $anneeActive = AnneeScolaire::where('active', 1)->first();
            $anneeId = $anneeActive ? $anneeActive->id : null;
        }

        $statistiques = [];

        // Récupérer tous les niveaux
        $niveaux = \App\Models\Niveau::with(['salles.inscriptions' => function ($query) use ($anneeId) {
            $query->where('etat', 'active')
                  ->when($anneeId, function ($q) use ($anneeId) {
                      return $q->where('annee_scolaire_id', $anneeId);
                  });
        }])->get();

        foreach ($niveaux as $niveau) {
            $moyennes = [];
            
            foreach ($niveau->salles as $salle) {
                foreach ($salle->inscriptions as $inscription) {
                    $resultats = $this->calculerResultatsEleve($inscription->id);
                    $moyenne = $this->calculerMoyenneGenerale($resultats);
                    
                    if ($moyenne > 0) {
                        $moyennes[] = $moyenne;
                    }
                }
            }

            if (count($moyennes) > 0) {
                $statistiques[] = [
                    'niveau' => $niveau->nomNiveau,
                    'cycle' => $niveau->cycle,
                    'nombre_eleves' => count($moyennes),
                    'moyenne_generale' => round(array_sum($moyennes) / count($moyennes) , 2),
                    'moyenne_max' => round(max($moyennes), 2),
                    'moyenne_min' => round(min($moyennes), 2),
                ];
            }
        }

        return Inertia::render('Admin/Resultats/Statistiques', [
            'statistiques' => $statistiques,
            'annees' => AnneeScolaire::orderBy('libelle', 'desc')->get(),
            'anneeCourante' => $anneeId,
        ]);
    }
}