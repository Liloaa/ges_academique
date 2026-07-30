<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NouveauMessageNotification extends Notification
{
    use Queueable;

    public function __construct(private Message $message)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $expediteur = $this->message->expediteur;
        $nomExpediteur = $expediteur ? trim($expediteur->prenom . ' ' . $expediteur->nom) : 'Un utilisateur';

        return [
            'titre' => 'Nouveau message',
            'message' => "{$nomExpediteur} vous a envoyé un message" . ($this->message->sujet ? " : \"{$this->message->sujet}\"" : '.'),
            'icone' => '✉️',
            'message_id' => $this->message->id,
        ];
    }
}
