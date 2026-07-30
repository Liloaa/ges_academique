<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Bulletin - {{ $eleve->nom }} {{ $eleve->prenom }}</title>
    <style>
        /* Dompdf ne supporte pas flexbox/grid de façon fiable : layout en tables */
        @page {
            margin: 90px 35px 70px 35px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1f2937;
        }

        header {
            position: fixed;
            top: -70px;
            left: 0;
            right: 0;
            height: 70px;
        }

        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            font-size: 9px;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 6px;
        }

        .header-table { width: 100%; border-collapse: collapse; }
        .header-table td { vertical-align: middle; }
        .logo { max-height: 55px; max-width: 55px; }
        .etab-nom { font-size: 15px; font-weight: bold; color: #1e3a8a; }
        .etab-info { font-size: 9px; color: #6b7280; }
        .header-right { text-align: right; }
        .titre-bulletin {
            font-size: 16px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .annee-bulletin { font-size: 10px; color: #6b7280; }

        hr.separator { border: none; border-top: 2px solid #1e3a8a; margin: 4px 0 12px 0; }

        .infos-eleve {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            background-color: #f8fafc;
        }
        .infos-eleve td {
            padding: 6px 10px;
            font-size: 10.5px;
            border: 1px solid #e5e7eb;
        }
        .infos-eleve .label { color: #6b7280; width: 22%; }
        .infos-eleve .valeur { font-weight: bold; width: 28%; }

        table.notes {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.notes th {
            background-color: #1e3a8a;
            color: #ffffff;
            padding: 7px 5px;
            font-size: 10px;
            text-align: center;
        }
        table.notes td {
            padding: 6px 5px;
            font-size: 10px;
            border: 1px solid #e5e7eb;
            text-align: center;
        }
        table.notes td.matiere-nom { text-align: left; font-weight: bold; }
        table.notes tr:nth-child(even) td { background-color: #f8fafc; }
        .note-insuffisante { color: #b91c1c; font-weight: bold; }
        .note-bonne { color: #15803d; font-weight: bold; }

        .recap-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .recap-table td {
            padding: 10px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .recap-moyenne {
            width: 45%;
            text-align: center;
            background-color: #eff6ff;
        }
        .recap-moyenne .valeur-moyenne { font-size: 26px; font-weight: bold; color: #1e3a8a; }
        .recap-moyenne .label-moyenne { font-size: 9px; color: #6b7280; text-transform: uppercase; }
        .recap-appreciation {
            width: 55%;
            text-align: center;
            background-color: #f0fdf4;
        }
        .recap-appreciation .valeur-appreciation { font-size: 20px; font-weight: bold; color: #15803d; }
        .recap-appreciation .label-appreciation { font-size: 9px; color: #6b7280; text-transform: uppercase; }

        .signature-zone {
            width: 100%;
            margin-top: 40px;
        }
        .signature-zone td {
            width: 50%;
            text-align: center;
            font-size: 10px;
            color: #374151;
        }
        .signature-ligne {
            margin-top: 40px;
            border-top: 1px solid #9ca3af;
            padding-top: 4px;
            width: 70%;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>

    <header>
        <table class="header-table">
            <tr>
                <td style="width: 60px;">
                    @if(!empty($etablissement['logo']) && file_exists(public_path($etablissement['logo'])))
                        <img class="logo" src="{{ public_path($etablissement['logo']) }}">
                    @endif
                </td>
                <td>
                    <div class="etab-nom">{{ $etablissement['nom'] }}</div>
                    <div class="etab-info">
                        {{ $etablissement['adresse'] }}
                        @if(!empty($etablissement['telephone'])) &bull; Tél: {{ $etablissement['telephone'] }} @endif
                    </div>
                </td>
                <td class="header-right">
                    <div class="titre-bulletin">Bulletin Scolaire</div>
                    <div class="annee-bulletin">Année scolaire {{ $inscription->annee->libelle ?? '' }}</div>
                </td>
            </tr>
        </table>
        <hr class="separator">
    </header>

    <footer>
        Document généré le {{ $dateGeneration }} — {{ $etablissement['nom'] }}
    </footer>

    <table class="infos-eleve">
        <tr>
            <td class="label">Élève</td>
            <td class="valeur">{{ strtoupper($eleve->nom) }} {{ $eleve->prenom }}</td>
            <td class="label">Matricule</td>
            <td class="valeur">{{ $eleve->matricule }}</td>
        </tr>
        <tr>
            <td class="label">Classe</td>
            <td class="valeur">{{ $inscription->salle->nomSalle ?? '—' }}</td>
            <td class="label">Niveau</td>
            <td class="valeur">{{ $inscription->salle->niveau->nomNiveau ?? '—' }}</td>
        </tr>
        @if($eleve->date_naissance)
        <tr>
            <td class="label">Né(e) le</td>
            <td class="valeur">{{ \Illuminate\Support\Carbon::parse($eleve->date_naissance)->format('d/m/Y') }}</td>
            <td class="label">Sexe</td>
            <td class="valeur">{{ $eleve->sexe ?? '—' }}</td>
        </tr>
        @endif
    </table>

    <table class="notes">
        <thead>
            <tr>
                <th style="width: 30%; text-align:left;">Matière</th>
                <th style="width: 10%;">Coef.</th>
                <th style="width: 15%;">1er Trim.</th>
                <th style="width: 15%;">2ème Trim.</th>
                <th style="width: 15%;">3ème Trim.</th>
                <th style="width: 15%;">Moy. Annuelle</th>
            </tr>
        </thead>
        <tbody>
            @forelse($resultats['matieres'] as $matiere)
            <tr>
                <td class="matiere-nom">{{ $matiere['matiere']->nomMatiere ?? '—' }}</td>
                <td>{{ $matiere['coefficient'] }}</td>
                <td>{{ $matiere['trimestres']['1er'] !== null ? number_format($matiere['trimestres']['1er'], 2) : '—' }}</td>
                <td>{{ $matiere['trimestres']['2ème'] !== null ? number_format($matiere['trimestres']['2ème'], 2) : '—' }}</td>
                <td>{{ $matiere['trimestres']['3ème'] !== null ? number_format($matiere['trimestres']['3ème'], 2) : '—' }}</td>
                <td class="{{ $matiere['moyenne_annuelle'] >= 10 ? 'note-bonne' : 'note-insuffisante' }}">
                    {{ number_format($matiere['moyenne_annuelle'], 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6">Aucune note enregistrée pour cette inscription.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="recap-table">
        <tr>
            <td class="recap-moyenne">
                <div class="label-moyenne">Moyenne Générale Annuelle</div>
                <div class="valeur-moyenne">{{ number_format($moyenneGenerale, 2) }} / 20</div>
            </td>
            <td class="recap-appreciation">
                <div class="label-appreciation">Appréciation</div>
                <div class="valeur-appreciation">{{ $appreciation }}</div>
            </td>
        </tr>
    </table>

    <table class="signature-zone">
        <tr>
            <td>
                <div class="signature-ligne">Cachet de l'établissement</div>
            </td>
            <td>
                <div class="signature-ligne">{{ $etablissement['signature'] }}</div>
            </td>
        </tr>
    </table>

</body>
</html>
