<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route pour récupérer la conversation entre deux utilisateurs
Route::middleware('auth:sanctum')->get('/conversation/{userId}', function ($userId) {
    $user = Auth::user();
    
    // Récupérer la conversation entre l'utilisateur authentifié et l'utilisateur spécifié
    $conversation = Message::with(['expediteur', 'destinataire', 'anneeScolaire'])
        ->where(function($query) use ($user, $userId) {
            $query->where('expediteur_id', $user->id)
                  ->where('destinataire_id', $userId);
        })
        ->orWhere(function($query) use ($user, $userId) {
            $query->where('expediteur_id', $userId)
                  ->where('destinataire_id', $user->id);
        })
        ->orderBy('date_envoi', 'desc')
        ->take(10)
        ->get();
    
    return response()->json($conversation);
});

// Route pour récupérer les messages non lus (pour notifications en temps réel)
Route::middleware('auth:sanctum')->get('/messages/unread', function () {
    $user = Auth::user();
    
    $unreadMessages = Message::with('expediteur')
        ->where('destinataire_id', $user->id)
        ->where('lu', false)
        ->orderBy('date_envoi', 'desc')
        ->take(5)
        ->get();
    
    return response()->json([
        'messages' => $unreadMessages,
        'count' => $unreadMessages->count(),
    ]);
});

// Route pour marquer tous les messages comme lus
Route::middleware('auth:sanctum')->post('/messages/mark-all-read', function (Request $request) {
    $user = Auth::user();
    
    $updated = Message::where('destinataire_id', $user->id)
        ->where('lu', false)
        ->update(['lu' => true]);
    
    return response()->json([
        'success' => true,
        'message' => "$updated messages marqués comme lus",
        'count' => $updated,
    ]);
});

// Route pour supprimer plusieurs messages
Route::middleware('auth:sanctum')->post('/messages/bulk-delete', function (Request $request) {
    $request->validate([
        'message_ids' => 'required|array',
        'message_ids.*' => 'exists:messages,id',
    ]);
    
    $user = Auth::user();
    
    // Vérifier que l'utilisateur a le droit de supprimer ces messages
    $messages = Message::whereIn('id', $request->message_ids)
        ->where(function($query) use ($user) {
            $query->where('expediteur_id', $user->id)
                  ->orWhere('destinataire_id', $user->id);
        })
        ->get();
    
    $deletedCount = 0;
    foreach ($messages as $message) {
        $message->delete();
        $deletedCount++;
    }
    
    return response()->json([
        'success' => true,
        'message' => "$deletedCount messages supprimés",
        'count' => $deletedCount,
    ]);
});

// Route pour envoyer un message rapide (pour l'admin)
Route::middleware(['auth:sanctum', 'admin'])->post('/messages/quick-send', function (Request $request) {
    $request->validate([
        'destinataire_id' => 'required|exists:users,id',
        'sujet' => 'required|string|max:200',
        'contenu' => 'required|string|min:10',
    ]);
    
    $user = Auth::user();
    
    $message = Message::create([
        'expediteur_id' => $user->id,
        'destinataire_id' => $request->destinataire_id,
        'sujet' => $request->sujet,
        'contenu' => $request->contenu,
        'date_envoi' => now(),
        'lu' => false,
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Message envoyé avec succès',
        'data' => $message->load(['expediteur', 'destinataire']),
    ]);
});

// Route pour récupérer les statistiques de messagerie
Route::middleware('auth:sanctum')->get('/messages/stats', function () {
    $user = Auth::user();
    
    $stats = [
        'total_received' => Message::where('destinataire_id', $user->id)->count(),
        'total_sent' => Message::where('expediteur_id', $user->id)->count(),
        'unread_count' => Message::where('destinataire_id', $user->id)->where('lu', false)->count(),
        'today_received' => Message::where('destinataire_id', $user->id)
            ->whereDate('date_envoi', today())
            ->count(),
        'today_sent' => Message::where('expediteur_id', $user->id)
            ->whereDate('date_envoi', today())
            ->count(),
    ];
    
    // Récupérer les derniers expéditeurs
    $recentSenders = Message::with('expediteur')
        ->where('destinataire_id', $user->id)
        ->select('expediteur_id')
        ->distinct()
        ->orderBy('date_envoi', 'desc')
        ->limit(5)
        ->get()
        ->pluck('expediteur');
    
    return response()->json([
        'stats' => $stats,
        'recent_senders' => $recentSenders,
    ]);
});