<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\AnneeScolaire;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Boîte de réception
    public function inbox()
    {
        $messages = Message::with(['expediteur', 'anneeScolaire'])
            ->where('destinataire_id', Auth::id())
            ->orderBy('date_envoi', 'desc')
            ->get();

        return Inertia::render('Admin/Messages/Inbox', [
            'messages' => $messages,
            'user' => Auth::user()
        ]);
    }

    // Messages envoyés
    public function sent()
    {
        $messages = Message::with(['destinataire', 'anneeScolaire'])
            ->where('expediteur_id', Auth::id())
            ->orderBy('date_envoi', 'desc')
            ->get();

        return Inertia::render('Admin/Messages/Sent', [
            'messages' => $messages,
            'user' => Auth::user()
        ]);
    }

    // Formulaire d'envoi
    public function create()
    {
        return Inertia::render('Admin/Messages/Create', [
            'utilisateurs' => User::orderBy('nom')->get(),
            'annees' => AnneeScolaire::orderBy('libelle')->get(),
            'user' => Auth::user()
        ]);
    }

    // Envoi du message
    public function store(Request $request)
    {
        $data = $request->validate([
            'destinataire_id' => 'required|exists:users,id',
            'sujet' => 'nullable|string|max:200',
            'contenu' => 'required|string',
            'annee_scolaire_id' => 'nullable|exists:annee_scolaires,id'
        ]);

        $data['expediteur_id'] = Auth::id();
        $data['date_envoi'] = now();

        Message::create($data);

        return redirect()->route('admin.messages.sent')
            ->with('success', 'Message envoyé avec succès.');
    }

    // Lire un message
    public function show(Message $message)
    {
        // Vérifier que l'utilisateur est le destinataire ou l'expéditeur
        if ($message->destinataire_id != Auth::id() && $message->expediteur_id != Auth::id()) {
            abort(403);
        }

        if ($message->destinataire_id == Auth::id() && !$message->lu) {
            $message->update(['lu' => true]);
        }

        // Récupérer les messages liés (même sujet ou conversation)
        $relatedMessages = Message::where(function($query) use ($message) {
                $query->where('expediteur_id', $message->expediteur_id)
                    ->where('destinataire_id', $message->destinataire_id)
                    ->orWhere('expediteur_id', $message->destinataire_id)
                    ->where('destinataire_id', $message->expediteur_id);
            })
            ->where('id', '!=', $message->id)
            ->orderBy('date_envoi', 'asc')
            ->get();

        return Inertia::render('Admin/Messages/Show', [
            'message' => $message->load(['expediteur', 'destinataire', 'anneeScolaire']),
            'user' => Auth::user(),
            'annees' => AnneeScolaire::orderBy('libelle')->get(),
            'relatedMessages' => $relatedMessages
        ]);
    }

    // Supprimer un message
    public function destroy(Message $message)
    {
        // Vérifier que seul l'expéditeur ou destinataire peut supprimer
        if ($message->expediteur_id != Auth::id() && $message->destinataire_id != Auth::id()) {
            abort(403);
        }

        $message->delete();

        return back()->with('success', 'Message supprimé.');
    }

    /* Historique par année scolaire
    public function historique($annee_id)
    {
        $messages = Message::with(['expediteur', 'destinataire'])
            ->where('annee_scolaire_id', $annee_id)
            ->orderBy('date_envoi', 'desc')
            ->get();

        return Inertia::render('Admin/Messages/Historique', [
            'messages' => $messages,
            'annee' => AnneeScolaire::find($annee_id),
            'user' => Auth::user()
        ]);
    }*/
}