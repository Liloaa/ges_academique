<?php

namespace App\Services;

use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NoteAjouteeNotification;
use App\Notifications\NouveauMessageNotification;

/**
 * Point d'entrée unique pour déclencher les notifications applicatives.
 * Regrouper l'appel ici évite de dupliquer la logique "qui notifier et
 * comment" dans chaque contrôleur qui crée une note ou un message.
 */
class NotificationService
{
    /**
     * Notifie l'élève (son compte utilisateur) qu'une note vient d'être
     * saisie ou mise à jour pour lui.
     */
    public function notifierNoteAjoutee(int $inscriptionId, Matiere $matiere, string $trimestre, float $note, bool $misAJour = false): void
    {
        $inscription = Inscription::with('eleve.user')->find($inscriptionId);
        $destinataire = $inscription?->eleve?->user;

        if (!$destinataire) {
            return;
        }

        $destinataire->notify(new NoteAjouteeNotification(
            $matiere->nomMatiere,
            $trimestre,
            $note,
            $misAJour
        ));
    }

    /**
     * Notifie le destinataire d'un nouveau message dans la messagerie.
     */
    public function notifierNouveauMessage(Message $message): void
    {
        if (!$message->destinataire) {
            return;
        }

        $message->destinataire->notify(new NouveauMessageNotification($message));
    }

    /**
     * Marque comme lue la notification « nouveau message » liée à ce message
     * pour l'utilisateur (appelé quand il ouvre le message).
     */
    public function marquerMessageLu(User $user, int $messageId): void
    {
        $user->unreadNotifications()
            ->where('type', NouveauMessageNotification::class)
            ->get()
            ->filter(fn ($n) => ($n->data['message_id'] ?? null) == $messageId)
            ->each->markAsRead();
    }

    /**
     * Marque comme lues toutes les notifications « nouveau message »
     * (appelé par « Marquer tous les messages comme lus »).
     */
    public function marquerTousMessagesLus(User $user): void
    {
        $user->unreadNotifications()
            ->where('type', NouveauMessageNotification::class)
            ->update(['read_at' => now()]);
    }
}