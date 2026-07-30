<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\AnneeScolaire;
use App\Services\BulletinService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ResultatController extends Controller
{
    public function __construct(private BulletinService $bulletinService)
    {
    }

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
        return $this->bulletinService->calculerResultatsEleve($inscriptionId);
    }

    /**
     * Calculer la moyenne générale d'un élève (moyenne des trimestres avec notes)
     */
    private function calculerMoyenneGenerale($resultats)
    {
        return $this->bulletinService->calculerMoyenneGenerale($resultats);
    }

    /**
     * Obtenir l'appréciation selon la moyenne
     */
    private function getAppreciation($moyenne)
    {
        return $this->bulletinService->getAppreciation($moyenne);
    }

    /**
     * Générer et télécharger le bulletin PDF d'un élève (usage admin).
     * L'année scolaire est optionnelle en query string (?annee_id=...) ;
     * l'année active est utilisée par défaut.
     */
    public function generateBulletin(Request $request, $eleveId)
    {
        $eleve = Eleve::findOrFail($eleveId);
        $anneeId = $request->get('annee_id');

        if (!$anneeId) {
            $anneeActive = AnneeScolaire::where('active', 1)->first();
            $anneeId = $anneeActive?->id;
        }

        $inscription = Inscription::with(['salle.niveau', 'annee'])
            ->where('eleve_id', $eleveId)
            ->when($anneeId, fn ($q) => $q->where('annee_scolaire_id', $anneeId))
            ->where('etat', 'active')
            ->first();

        if (!$inscription) {
            return redirect()
                ->route('resultats.index', ['eleve_id' => $eleveId])
                ->with('error', "Aucune inscription active trouvée pour cet élève sur l'année demandée.");
        }

        $pdf = $this->bulletinService->genererPdf($eleve, $inscription);
        $nomFichier = $this->bulletinService->nomFichierBulletin($eleve, $inscription);

        return $pdf->download($nomFichier);
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