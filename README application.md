Système de Gestion Académique des Élèves

_Description
Cette application web est un système de gestion académique destiné aux établissements scolaires. Elle permet d'automatiser la gestion des élèves,enseignants, inscriptions, classes, matières et notes, auparavant réalisée manuellement sur papier.
Le système prend en charge :
. les cycles Primaire, Collège et Lycée
· la gestion des filières au lycée (Scientifique/Littéraire)
· la saisie et la consultation des notes
· la génération des bulletins scolaires en PDF

_Objectifs
. Centraliser les données académiques
· Réduire les erreurs de gestion manuelle
· Offrir un portail sécurisé pour chaque acteur (admin, enseignant, élève)

_Rôles et accès
-Administrateur
· Création des comptes élèves et enseignants
· Gestion des niveaux, salles, filières, matières
. Attribution des enseignants aux matières
· Suivi des effectifs par salle
-Enseignant
· Information personnelle
· Saisie et modification des notes des élèves
· Consultation de ses matières
-Élève
· Information personnelle
· Inscription / réinscription annuelle des
élèves
· Connexion à son compte personnel
· Consultation de ses notes
· Téléchargement du bulletin scolaire en PDF

_Fonctionnalités principales
· Authentification sécurisée (Laravel Breeze)
· Gestion des utilisateurs et rôles
· CRUD : élèves, enseignants, niveaux, salles, matières
· Inscription des élèves par année scolaire
· Attribution automatique du niveau et du cycle
· Saisie des notes par trimestre
. Calcul des effectifs et taux de remplissage par salle
· Génération des bulletins en PDF

_Technologies Utilisées
. Backend : PHP 8.2+, Laravel 11
. Frontend : Vue 3 + Inertia.js
· Base de données : MySQL
· Authentification : Laravel Breeze
· Build : Vite

_Prérequis
· PHP ≥ 8.2
· Composer
. Node.js & npm
. MySQL
· XAMPP ou équivalent
· VS Code (recommandé)

_Installation
-telecharger php8 avec xampp(php version:8.2.12) et ajouter dans le path (C:\xampp\php)
-Télécharger Composer et ajouter dans le path(C:\composer)
-Télécharger laravel a sa version et creer le projet depuis composer: composer creae-project laravel/ laravel ges_academ "11.*"
# Cloner le projet
git clone <repo-url>
cd ges_academ
# Installer les dépendances backend
composer install
# Insaller les dependance frontend
npm install
# Installer breeze par composer
composer require laravel/breeze --dev
# Installer breeze(auhentification laravel)avec vue et vue installe automatiquement ineria
php arisan breeze:install vue
# Copier le fichier d'environnement
cp .env. example .env
# Configurer la base de données dans .env
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

6.Utilisation : //Instructions pour lancer l'application (ex: npm start), comment y accéder (URL), et des exemples d'utilisation.
# lancer le serveur base de donnees : xampp
# Compiler les assets(vite)
npm run dev
# Construire les assets pour la production
npm run build
# Lancer le serveur
php arisan serve et ctrl+clic sur le lien pour acceder a l'URL

_Roadmap
· Amélioration du portail élève
· Amélioration du portail enseignant
· Statistiques avancées
· Messagerie interne avec enseignant

_Auteur
Projet académique réalisé dans le cadre du MEN, par Razaka Mahatoky Luciana, etudiante en Informatique Générale.