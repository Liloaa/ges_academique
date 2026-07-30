<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Gère les notifications de l'utilisateur connecté, quel que soit son rôle
 * (admin, enseignant, élève). Utilise le système de notifications natif de
 * Laravel (table `notifications`, colonne polymorphe notifiable).
 */
class NotificationController extends Controller
{
    /**
     * Dernières notifications de l'utilisateur connecté + nombre non lues.
     * Utilisé par la cloche de notification dans les layouts.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'notifications' => $user->notifications()->latest()->limit(15)->get(),
            'non_lues' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Marquer une notification comme lue.
     */
    public function markAsRead(string $id)
    {
        $notification = Auth::user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'ok']);
    }
}
