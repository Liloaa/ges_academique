<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NoteAjouteeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private string $matiereNom,
        private string $trimestre,
        private float $note,
        private bool $misAJour = false,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $verbe = $this->misAJour ? 'mise à jour' : 'publiée';

        return [
            'titre' => 'Nouvelle note',
            'message' => "Une note de {$this->matiereNom} ({$this->trimestre} trimestre) a été {$verbe} : {$this->note}/20.",
            'icone' => '📝',
            'lien' => 'eleve.notes.index',
        ];
    }
}
