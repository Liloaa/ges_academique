<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Informations de l'établissement
    |--------------------------------------------------------------------------
    |
    | Ces informations apparaissent sur les bulletins PDF et autres documents
    | officiels générés par l'application. Chaque école cliente doit adapter
    | ces valeurs (directement ici ou via le fichier .env) sans toucher au
    | code de génération des documents.
    |
    */

    'nom' => env('ETABLISSEMENT_NOM', env('APP_NAME', 'Mon Établissement')),

    'adresse' => env('ETABLISSEMENT_ADRESSE', ''),

    'telephone' => env('ETABLISSEMENT_TELEPHONE', ''),

    'email' => env('ETABLISSEMENT_EMAIL', ''),

    // Chemin public vers le logo (dans /public), ex: 'images/logo-ecole.png'
    'logo' => env('ETABLISSEMENT_LOGO', null),

    // Texte affiché en pied de page des bulletins
    'signature' => env('ETABLISSEMENT_SIGNATURE', 'Le Directeur / La Directrice'),

];
