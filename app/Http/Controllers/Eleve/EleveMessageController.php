<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class EleveMessageController extends Controller
{
    // Boîte de réception de l'élève
    public function inbox()
    {
        $user = Auth::user();
        
        $messages = Message::with(['expediteur', 'anneeScolaire'])
            ->where('destinataire_id', $user->id)
            ->orderBy('date_envoi', 'desc')
            ->get();

        return Inertia::render('Eleve/Messages/Inbox', [
            'messages' => $messages,
            'user' => $user,
            'unreadCount' => Message::where('destinataire_id', $user->id)->where('lu', false)->count()
        ]);
    }

    // Messages envoyés par l'élève
    public function sent()
    {
        $user = Auth::user();
        
        $messages = Message::with(['destinataire', 'anneeScolaire'])
            ->where('expediteur_id', $user->id)
            ->orderBy('date_envoi', 'desc')
            ->get();

        return Inertia::render('Eleve/Messages/Sent', [
            'messages' => $messages,
            'user' => $user,
            'unreadCount' => Message::where('destinataire_id', $user->id)->where('lu', false)->count()
        ]);
    }

    // Formulaire d'envoi pour l'élève
    public function create()
    {
        $user = Auth::user();
        
        // L'élève peut envoyer des messages aux enseignants et à l'admin
        $destinataires = User::whereIn('role', ['admin', 'enseignant'])
            ->orderBy('nom')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'role' => $user->role,
                    'role_label' => $user->role === 'admin' ? 'Administrateur' : 'Enseignant'
                ];
            });

        // Récupérer l'année scolaire active de l'élève
        $anneeActive = $user->inscriptions()
            ->whereHas('anneeScolaire', function($query) {
                $query->where('active', true);
            })
            ->with('anneeScolaire')
            ->first();

        return Inertia::render('Eleve/Messages/Create', [
            'destinataires' => $destinataires,
            'annees' => AnneeScolaire::orderBy('libelle')->get(),
            'anneeActive' => $anneeActive?->anneeScolaire,
            'user' => $user,
            'unreadCount' => Message::where('destinataire_id', $user->id)->where('lu', false)->count()
        ]);
    }

    // Envoi du message par l'élève
    public function store(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'destinataire_id' => 'required|exists:users,id',
            'sujet' => 'nullable|string|max:200',
            'contenu' => 'required|string|min:10',
            'annee_scolaire_id' => 'nullable|exists:annee_scolaires,id'
        ]);

        // Vérifier que le destinataire est autorisé (admin ou enseignant)
        $destinataire = User::find($data['destinataire_id']);
        if (!in_array($destinataire->role, ['admin', 'enseignant'])) {
            return back()->withErrors([
                'destinataire_id' => 'Vous ne pouvez envoyer des messages qu\'aux administrateurs et enseignants.'
            ]);
        }

        $data['expediteur_id'] = $user->id;
        $data['date_envoi'] = now();

        Message::create($data);

        return redirect()->route('eleve.messages.sent')
            ->with('success', 'Message envoyé avec succès.');
    }

    // Lire un message (élève)
    public function show(Message $message)
    {
        $user = Auth::user();
        
        // Vérifier que l'élève est le destinataire ou l'expéditeur
        if ($message->destinataire_id != $user->id && $message->expediteur_id != $user->id) {
            abort(403, 'Accès non autorisé à ce message.');
        }

        // Marquer comme lu si l'élève est le destinataire
        if ($message->destinataire_id == $user->id && !$message->lu) {
            $message->update(['lu' => true]);
        }

        // Récupérer les messages liés (conversation)
        $relatedMessages = Message::where(function($query) use ($message) {
                $query->where('expediteur_id', $message->expediteur_id)
                      ->where('destinataire_id', $message->destinataire_id)
                      ->orWhere('expediteur_id', $message->destinataire_id)
                      ->where('destinataire_id', $message->expediteur_id);
            })
            ->where('id', '!=', $message->id)
            ->orderBy('date_envoi', 'asc')
            ->get();

        return Inertia::render('Eleve/Messages/Show', [
            'message' => $message->load(['expediteur', 'destinataire', 'anneeScolaire']),
            'user' => $user,
            'relatedMessages' => $relatedMessages,
            'unreadCount' => Message::where('destinataire_id', $user->id)->where('lu', false)->count()
        ]);
    }

    // Supprimer un message (élève)
    public function destroy(Message $message)
    {
        $user = Auth::user();
        
        // Vérifier que l'élève est le destinataire ou l'expéditeur
        if ($message->expediteur_id != $user->id && $message->destinataire_id != $user->id) {
            abort(403, 'Vous n\'avez pas la permission de supprimer ce message.');
        }

        $message->delete();

        return back()->with('success', 'Message supprimé avec succès.');
    }

    // Récupérer les enseignants pour l'autocomplétion
    public function getEnseignants()
    {
        $enseignants = User::where('role', 'enseignant')
            ->orderBy('nom')
            ->get(['id', 'nom', 'email'])
            ->map(function($enseignant) {
                return [
                    'value' => $enseignant->id,
                    'label' => $enseignant->nom . ' (' . $enseignant->email . ')'
                ];
            });

        return response()->json($enseignants);
    }

    // Marquer tous les messages comme lus
    public function markAllAsRead()
    {
        $user = Auth::user();
        
        Message::where('destinataire_id', $user->id)
            ->where('lu', false)
            ->update(['lu' => true]);

        return back()->with('success', 'Tous les messages ont été marqués comme lus.');
    }

    // Statistiques des messages (pour le dashboard)
    public function stats()
    {
        $user = Auth::user();
        
        $stats = [
            'totalRecus' => Message::where('destinataire_id', $user->id)->count(),
            'nonLus' => Message::where('destinataire_id', $user->id)->where('lu', false)->count(),
            'totalEnvoyes' => Message::where('expediteur_id', $user->id)->count(),
            'messagesRecents' => Message::where('destinataire_id', $user->id)
                ->orderBy('date_envoi', 'desc')
                ->limit(5)
                ->with('expediteur')
                ->get()
        ];

        return response()->json($stats);
    }
}