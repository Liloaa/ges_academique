<?php

namespace App\Services;

use App\Models\Eleve;
use App\Models\Inscription;
use App\Models\Note;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Centralise le calcul des moyennes/résultats d'un élève et la génération
 * du bulletin PDF correspondant.
 *
 * Auparavant cette logique était dupliquée à l'identique dans
 * ResultatController (côté admin) et EleveNoteController (côté élève).
 * La centraliser ici garantit que le bulletin PDF, la page de résultats
 * admin et le portail élève affichent toujours exactement les mêmes
 * chiffres.
 */
class BulletinService
{
    /**
     * Calcule les résultats détaillés (par matière et par trimestre)
     * pour une inscription donnée.
     */
    public function calculerResultatsEleve(int $inscriptionId): array
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
            $coefficient = $note->matiere->coefficient ?? 1;

            if (!isset($resultatsParMatiere[$matiereId])) {
                $resultatsParMatiere[$matiereId] = [
                    'matiere' => $note->matiere,
                    'coefficient' => $coefficient,
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

            if ($noteValue > 0) {
                $totauxTrimestres[$trimestre]['points'] += $noteValue * $coefficient;
                $totauxTrimestres[$trimestre]['coefficients'] += $coefficient;
            }
        }

        foreach ($resultatsParMatiere as &$resultat) {
            $notesValides = array_filter($resultat['trimestres'], fn ($n) => $n !== null);

            if (count($notesValides) > 0) {
                $resultat['moyenne_annuelle'] = round(array_sum($notesValides) / count($notesValides), 2);
            }
        }
        unset($resultat);

        $moyennesTrimestrielles = [];
        foreach ($totauxTrimestres as $trimestre => $data) {
            $moyennesTrimestrielles[$trimestre] = $data['coefficients'] > 0
                ? round($data['points'] / $data['coefficients'], 2)
                : 0;
        }

        return [
            'matieres' => array_values($resultatsParMatiere),
            'moyennes_trimestrielles' => $moyennesTrimestrielles,
        ];
    }

    /**
     * Moyenne générale annuelle = moyenne des trimestres ayant des notes.
     */
    public function calculerMoyenneGenerale(array $resultats): float
    {
        if (!isset($resultats['moyennes_trimestrielles'])) {
            return 0;
        }

        $moyennes = array_filter($resultats['moyennes_trimestrielles'], fn ($m) => $m > 0);

        if (count($moyennes) === 0) {
            return 0;
        }

        return round(array_sum($moyennes) / count($moyennes), 2);
    }

    public function getAppreciation(float $moyenne): string
    {
        return match (true) {
            $moyenne >= 16 => 'Excellent',
            $moyenne >= 14 => 'Très Bien',
            $moyenne >= 12 => 'Bien',
            $moyenne >= 10 => 'Assez Bien',
            $moyenne >= 8 => 'Passable',
            default => 'Insuffisant',
        };
    }

    /**
     * Rassemble toutes les données nécessaires à l'affichage/impression
     * du bulletin d'un élève pour une inscription donnée.
     */
    public function donneesBulletin(Eleve $eleve, Inscription $inscription): array
    {
        $resultats = $this->calculerResultatsEleve($inscription->id);
        $moyenneGenerale = $this->calculerMoyenneGenerale($resultats);

        return [
            'eleve' => $eleve,
            'inscription' => $inscription->load(['salle.niveau', 'annee']),
            'resultats' => $resultats,
            'moyenneGenerale' => $moyenneGenerale,
            'appreciation' => $this->getAppreciation($moyenneGenerale),
            'etablissement' => config('etablissement'),
            'dateGeneration' => now()->translatedFormat('d/m/Y'),
        ];
    }

    /**
     * Génère le PDF du bulletin et retourne l'objet PDF prêt à être
     * streamé ou téléchargé par le contrôleur appelant.
     */
    public function genererPdf(Eleve $eleve, Inscription $inscription)
    {
        $donnees = $this->donneesBulletin($eleve, $inscription);

        return Pdf::loadView('pdf.bulletin', $donnees)->setPaper('a4', 'portrait');
    }

    /**
     * Nom de fichier standardisé pour le téléchargement du bulletin.
     */
    public function nomFichierBulletin(Eleve $eleve, Inscription $inscription): string
    {
        $annee = str_replace(['/', ' '], '-', $inscription->annee->libelle ?? 'annee');

        return "bulletin-{$eleve->matricule}-{$annee}.pdf";
    }
}
