<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


// Contrôleurs généraux
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetController;

// Contrôleurs admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\AnneeScolaireController;
use App\Http\Controllers\EleveController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\FiliereController;
use App\Http\Controllers\NiveauController;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\MatiereController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ResultatController;
use App\Http\Middleware\AdminMiddleware;

// Contrôleurs élève
use App\Http\Controllers\Eleve\EleveDashboardController;
use App\Http\Controllers\Eleve\EleveMessageController;
use App\Http\Controllers\Eleve\EleveNoteController;
use App\Http\Controllers\Eleve\EleveProfileController;

// Contrôleurs enseignant
use App\Http\Controllers\Enseignant\EnseignantDashboardController;
use App\Http\Controllers\Enseignant\EnseignantNoteController;
use App\Http\Controllers\Enseignant\EnseignantProfileController;

// Page d'accueil
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// DASHBOARD GÉNÉRAL (accessible uniquement aux utilisateurs vérifiés sans rôle spécifique)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// REDIRECTION APRÈS CONNEXION
Route::get('/home', function () {
    $user = Auth::user(); // ← UTILISEZ Auth::user() au lieu de auth()->user()
    
    if (!$user) {
        return redirect('/login');
    }

    // Vérifier le rôle de l'utilisateur
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } elseif ($user->role === 'enseignant') {
        return redirect()->route('enseignant.dashboard');
    } elseif ($user->role === 'eleve') {
        return redirect()->route('eleve.dashboard');
    }

    // Si l'utilisateur n'a pas de rôle spécifique mais est vérifié
    if ($user->hasVerifiedEmail()) {
        return redirect()->route('dashboard');
    }

    // Sinon, rediriger vers la vérification d'email
    return redirect()->route('verification.notice');
})->middleware('auth')->name('home');


// PROFIL UTILISATEUR (pour les utilisateurs sans rôle spécifique)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

//admin
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');

    //Proile
    Route::get('/admin/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/admin/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::delete('/admin/profile', [AdminProfileController::class, 'destroy'])->name('admin.profile.destroy');
    
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
        ->name('admin.eleves.historique');

    Route::resource('admin/enseignants', EnseignantController::class)->names('enseignants');
    Route::resource('admin/matieres', MatiereController::class)->names('matieres');
    Route::resource('admin/inscriptions', InscriptionController::class)->names('inscriptions');

    Route::resource('admin/notes', NoteController::class)
        ->parameters(['notes' => 'inscription'])
        ->names('notes');
    Route::get('/notes-api/{inscriptionId}', [NoteController::class, 'getNotesByInscription'])->name('notes.by-inscription');

    // Routes pour les résultats
    Route::get('/admin/resultats', [ResultatController::class, 'index'])->name('resultats.index');
    Route::get('/admin/resultats/statistiques', [ResultatController::class, 'statistiques'])->name('resultats.statistiques');
    Route::get('/admin/resultats/bulletin/{eleveId}', [ResultatController::class, 'generateBulletin'])->name('resultats.bulletin');

    // Messagerie admin
    Route::get('/admin/messages', [MessageController::class, 'inbox'])->name('admin.messages.inbox');
    Route::get('/admin/messages/envoyes', [MessageController::class, 'sent'])->name('admin.messages.sent');
    Route::get('/admin/messages/creer', [MessageController::class, 'create'])->name('admin.messages.create');
    Route::post('/admin/messages', [MessageController::class, 'store'])->name('admin.messages.store');
    Route::get('/admin/messages/{message}', [MessageController::class, 'show'])->name('admin.messages.show');    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    //Route::get('/admin/messages/historique/{annee_id}', [MessageController::class, 'historique'])->name('messages.historique');
});

//enseignant
Route::middleware(['auth', \App\Http\Middleware\EnseignantMiddleware::class])->group(function () {
    Route::get('/enseignant', [EnseignantDashboardController::class, 'index'])->name('enseignant.dashboard');

    //Profile
    Route::get('/enseignant/profile', [EnseignantProfileController::class, 'edit'])->name('enseignant.profile.edit');
    Route::patch('/enseignant/profile', [EnseignantProfileController::class, 'update'])->name('enseignant.profile.update');
    Route::delete('/enseignant/profile', [EnseignantProfileController::class, 'destroy'])->name('enseignant.profile.destroy');

    //Note
    Route::get('/enseignant/notes/index', [EnseignantNoteController::class, 'index'])->name('enseignant.notes.index');
    Route::get('/enseignant/notes/statistiques', [EnseignantNoteController::class, 'statistiques'])->name('enseignant.notes.statistiques');
    Route::post('/enseignant/notes/saisie', [EnseignantNoteController::class, 'storeNotes'])->name('enseignant.notes.store');

    Route::get('/enseignant/notes/saisie/{salle}/{matiere}', [EnseignantNoteController::class, 'showEleves'])->name('enseignant.notes.eleves');
    Route::get('/enseignant/notes/historique/{matiere}', [EnseignantNoteController::class, 'historiqueMatiere'])->name('enseignant.notes.historique');
    
    Route::get('/enseignant/notes/{note}/edit', [EnseignantNoteController::class, 'editNote'])->where('note', '[0-9]+')->name('enseignant.notes.edit');
    Route::put('/enseignant/notes/{note}', [EnseignantNoteController::class, 'updateNote'])->where('note', '[0-9]+')->name('enseignant.notes.update');
    Route::delete('/enseignant/notes/{note}', [EnseignantNoteController::class, 'destroyNote'])->where('note', '[0-9]+')->name('enseignant.notes.destroy');
});

//Eleve
Route::middleware(['auth', \App\Http\Middleware\EleveMiddleware::class])->group(function () {
    Route::get('/eleve', [EleveDashboardController::class, 'index'])->name('eleve.dashboard');

    // Notes
    Route::get('/eleve/notes', [EleveNoteController::class, 'index'])->name('eleve.notes.index');
    Route::get('/eleve/notes/bulletin', [EleveNoteController::class, 'bulletin'])->name('eleve.notes.bulletin');
    
    //Profile
    Route::get('/eleve/profile', [EleveProfileController::class, 'edit'])->name('eleve.profile.edit');
    Route::patch('/eleve/profile', [EleveProfileController::class, 'update'])->name('eleve.profile.update');
    Route::delete('/eleve/profile', [EleveProfileController::class, 'destroy'])->name('eleve.profile.destroy');

    
    // Messagerie élève
    Route::get('/eleve/messages', [EleveMessageController::class, 'inbox'])->name('eleve.messages.inbox');
    Route::get('/eleve/messages/envoyes', [EleveMessageController::class, 'sent'])->name('eleve.messages.sent');
    Route::get('/eleve/messages/creer', [EleveMessageController::class, 'create'])->name('eleve.messages.create');
    Route::post('/eleve/messages/', [EleveMessageController::class, 'store'])->name('eleve.messages.store');
    Route::get('/eleve/messages/{message}', [EleveMessageController::class, 'show'])->name('eleve.messages.show');
    Route::delete('/eleve/messages/{message}', [EleveMessageController::class, 'destroy'])->name('eleve.messages.destroy');
    Route::post('/eleve/messages/marquer-tous-lus', [EleveMessageController::class, 'markAllAsRead'])->name('eleve.messages.mark-all-read');
    Route::get('/eleve/messages/enseignants', [EleveMessageController::class, 'getEnseignants'])->name('eleve.messages.enseignants');
    Route::get('/eleve/messages/statistiques', [EleveMessageController::class, 'stats'])->name('eleve.messages.stats');
});
 
require __DIR__.'/auth.php';