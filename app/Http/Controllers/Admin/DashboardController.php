<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\AnneeScolaire;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Récupérer l'année scolaire active
        $anneeActive = AnneeScolaire::where('active', 1)->first();
        
        // 1. Nombre total d'élèves enregistrés
        $totalEleves = Eleve::count();
        
        // 2. Nombre d'élèves inscrits dans l'année scolaire active
        $totalInscrits = 0;
        if ($anneeActive) {
            $totalInscrits = Inscription::where('annee_scolaire_id', $anneeActive->id)
                ->where('etat', 'active')
                ->count();
        }
        
        // 3. Taux de réussite de l'établissement dans l'année active
        $tauxReussite = 0;
        if ($anneeActive) {
            $tauxReussite = $this->calculerTauxReussite($anneeActive->id);
        }
        
        // Statistiques par cycle (primaire, collège, lycée)
        $statistiquesCycles = [];
        if ($anneeActive) {
            $cycles = ['primaire', 'college', 'lycee'];
            
            foreach ($cycles as $cycle) {
                // Compter les inscriptions par cycle
                $inscriptionsCycle = Inscription::whereHas('salle.niveau', function ($query) use ($cycle) {
                        $query->where('cycle', $cycle);
                    })
                    ->where('annee_scolaire_id', $anneeActive->id)
                    ->where('etat', 'active')
                    ->count();
                
                $statistiquesCycles[$cycle] = $inscriptionsCycle;
            }
        }
        
        // Récupérer les dernières inscriptions
        $dernieresInscriptions = [];
        if ($anneeActive) {
            $dernieresInscriptions = Inscription::with(['eleve', 'salle.niveau'])
                ->where('annee_scolaire_id', $anneeActive->id)
                ->where('etat', 'active')
                ->orderBy('date_inscription', 'desc')
                ->take(5)
                ->get();
        }
        
        // Statistiques mensuelles des inscriptions
        $statistiquesMensuelles = [];
        if ($anneeActive) {
            $statistiquesMensuelles = Inscription::select(
                DB::raw('MONTH(date_inscription) as mois'),
                DB::raw('COUNT(*) as nombre')
            )
                ->where('annee_scolaire_id', $anneeActive->id)
                ->whereYear('date_inscription', date('Y'))
                ->groupBy(DB::raw('MONTH(date_inscription)'))
                ->orderBy('mois')
                ->get()
                ->map(function ($item) {
                    return [
                        'mois' => $this->getMonthName($item->mois),
                        'nombre' => $item->nombre
                    ];
                });
        }
        
        return Inertia::render('Admin/Index', [
            'statistiques' => [
                'totalEleves' => $totalEleves,
                'totalInscrits' => $totalInscrits,
                'tauxReussite' => round($tauxReussite, 2),
                'anneeActive' => $anneeActive ? $anneeActive->libelle : 'Aucune année active',
                'statistiquesCycles' => $statistiquesCycles,
                'dernieresInscriptions' => $dernieresInscriptions,
                'statistiquesMensuelles' => $statistiquesMensuelles,
            ]
        ]);
    }
    
    private function getMonthName($monthNumber)
    {
        $months = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre'
        ];
        
        return $months[$monthNumber] ?? 'Inconnu';
    }

    private function calculerTauxReussite($anneeActiveId)
    {
        // Récupérer toutes les inscriptions actives
        $inscriptions = Inscription::where('annee_scolaire_id', $anneeActiveId)
            ->where('etat', 'active')
            ->with(['notes' => function ($query) {
                $query->selectRaw('inscription_id, AVG(note) as moyenne_note')
                    ->groupBy('inscription_id');
            }])
            ->get();
        
        $totalElevesAvecNotes = 0;
        $elevesAdmis = 0;
        
        foreach ($inscriptions as $inscription) {
            if ($inscription->notes->count() > 0) {
                $totalElevesAvecNotes++;
                
                // Calculer la moyenne de l'élève
                $moyenne = $inscription->notes->first()->moyenne_note;
                
                if ($moyenne >= 10) {
                    $elevesAdmis++;
                }
            }
        }
        
        if ($totalElevesAvecNotes > 0) {
            return ($elevesAdmis / $totalElevesAvecNotes) * 100;
        }
        
        return 0;
    }
}