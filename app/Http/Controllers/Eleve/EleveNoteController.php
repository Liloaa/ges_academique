<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Inscription;
use App\Models\Note;
use App\Models\AnneeScolaire;
use App\Services\BulletinService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EleveNoteController extends Controller
{
    public function __construct(private BulletinService $bulletinService)
    {
    }

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
        return $this->bulletinService->calculerResultatsEleve($inscriptionId);
    }

    /**
     * Calculer la moyenne générale
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

    /**
     * Télécharger son propre bulletin en PDF (élève connecté uniquement).
     */
    public function downloadBulletin()
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

        $anneeActive = AnneeScolaire::where('active', true)->first();

        $inscriptionActive = Inscription::with(['salle.niveau', 'annee'])
            ->where('eleve_id', $eleve->id)
            ->when($anneeActive, fn ($q) => $q->where('annee_scolaire_id', $anneeActive->id))
            ->where('etat', 'active')
            ->first();

        if (!$inscriptionActive) {
            return redirect()->route('eleve.notes.index')
                ->with('error', 'Aucune inscription active trouvée.');
        }

        $pdf = $this->bulletinService->genererPdf($eleve, $inscriptionActive);
        $nomFichier = $this->bulletinService->nomFichierBulletin($eleve, $inscriptionActive);

        return $pdf->download($nomFichier);
    }
}