<?php

namespace App\Services;

use App\Models\Inscription;
use App\Models\Matiere;
use App\Models\Message;
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
}
