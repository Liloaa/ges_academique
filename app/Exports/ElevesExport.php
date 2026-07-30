<?php

namespace App\Exports;

use App\Models\Eleve;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ElevesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(private ?string $search = null)
    {
    }

    /**
     * Élèves à exporter, avec leur inscription active (classe/niveau courants).
     */
    public function collection()
    {
        return Eleve::with(['inscriptions' => function ($query) {
                $query->where('etat', 'active')->with(['salle.niveau', 'annee']);
            }])
            ->when($this->search, function ($query) {
                $query->where('nom', 'like', "%{$this->search}%")
                    ->orWhere('prenom', 'like', "%{$this->search}%")
                    ->orWhere('matricule', 'like', "%{$this->search}%");
            })
            ->orderBy('nom')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Matricule',
            'Nom',
            'Prénom',
            'Sexe',
            'Date de naissance',
            'Email',
            'Téléphone',
            'Filière',
            'Niveau',
            'Salle',
            'Année scolaire',
        ];
    }

    /**
     * @param Eleve $eleve
     */
    public function map($eleve): array
    {
        $inscription = $eleve->inscriptions->first();
        $niveau = $inscription?->salle?->niveau;

        return [
            $eleve->matricule,
            $eleve->nom,
            $eleve->prenom,
            $eleve->sexe ?? '—',
            $eleve->date_naissance ? Carbon::parse($eleve->date_naissance)->format('d/m/Y') : '—',
            $eleve->email ?? '—',
            $eleve->telephone ?? '—',
            $niveau?->filiere?->nomFiliere ?? '—',
            $niveau?->nomNiveau ?? '—',
            $inscription?->salle?->nomSalle ?? 'Non inscrit',
            $inscription?->annee?->libelle ?? '—',
        ];
    }

    public function title(): string
    {
        return 'Élèves';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E3A8A'],
                ],
            ],
        ];
    }
}
