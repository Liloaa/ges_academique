<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


// Contrôleurs généraux
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;

// Contrôleurs admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\NoteController;
use App\Http\Middleware\AdminMiddleware;

// Contrôleurs élève
use App\Http\Controllers\Eleve\EleveDashboardController;
use App\Http\Controllers\Eleve\EleveNoteController;
use App\Http\Controllers\Eleve\ProfilController as EleveProfilController;

// Contrôleurs enseignant
use App\Http\Controllers\Enseignant\EnseignantDashboardController;

// Page d'accueil
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// DASHBOARD GÉNÉRAL
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


//PROFIL UTILISATEUR
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//admin
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('admin/anneesscolaires', AnneeScolaireController::class)
        ->names('anneesscolaires');
    Route::put('admin/anneesscolaires/{anneesscolaire}/active', [AnneeScolaireController::class, 'setActive'])
        ->name('anneesscolaires.active');

    Route::resource('admin/filieres', FiliereController::class)->names('filieres');
    Route::resource('admin/niveaux', NiveauController::class)->names('niveaux');
    Route::resource('admin/salles', SalleController::class)->names('salles');

    Route::resource('admin/eleves', EleveController::class)
        ->parameters([
            'eleves' => 'eleve', 
        ])
        ->names('eleves');

    Route::get('/admin/eleves/{eleve}/historique', [EleveController::class, 'historique'])
        ->name('eleves.historique');

    Route::resource('admin/enseignants', EnseignantController::class)->names('enseignants');
    Route::resource('admin/matieres', MatiereController::class)->names('matieres');
    Route::resource('admin/inscriptions', InscriptionController::class)->names('inscriptions');
    Route::resource('admin/notes' , NoteController::class)->names('notes');

    Route::get('admin/profile', [ProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('admin/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('admin/profile', [ProfileController::class, 'destroy'])->name('admin.profile.destroy');

});

//enseignant
Route::middleware(['auth', \App\Http\Middleware\EnseignantMiddleware::class])->group(function () {
    Route::get('/enseignant', [EnseignantDashboardController::class, 'index'])->name('enseignant.dashboard');
});

//eleve
Route::middleware(['auth', \App\Http\Middleware\EleveMiddleware::class])->group(function () {
    Route::get('/eleve', [EleveDashboardController::class, 'index'])->name('eleve.dashboard');

    //Notes (lecture seule)
    //Route::get('eleve/notes', EleveNoteController::class)->name('notes');
    Route::get('/notes/{note}', [EleveNoteController::class, 'show'])->name('notes.show');
    
    /* Profil
        Route::get('/profil', [EleveProfilController::class, 'index'])->name('profil');*/
});
 



require __DIR__.'/auth.php';