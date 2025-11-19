<?php

namespace App\Http\Controllers;

use App\Models\Eleve;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class EleveController extends Controller
{
    // LISTE DES ELEVES
    public function index()
    {
        $eleves = Eleve::with(['user'])
            ->orderBy('id', 'desc')
            ->get();

        return Inertia::render('Admin/Eleves/Index', [
            'eleves' => $eleves,
        ]);
    }

    // FORMULAIRE D'AJOUT
    public function create()
    {
        return Inertia::render('Admin/Eleves/Create');
    }

    // AJOUT D'UN ELEVE
    public function store(Request $request)
    {
        $request->validate([
            'matricule' => 'required|unique:eleves,matricule',
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'email' => 'nullable|email|unique:users,email',
            'sexe' => 'nullable|string',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:4|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $userId = null;

            // CRÉATION USER SI EMAIL
            if ($request->email) {
                $user = User::create([
                    'prenom' => $request->prenom,
                    'nom' => $request->nom,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'role' => 'eleve',
                ]);
                $userId = $user->id;
            }

            // CRÉATION ELEVE
            Eleve::create(array_merge($request->only([
                'matricule','nom','prenom','date_naissance','email','sexe','adresse','telephone'
            ]), ['user_id' => $userId]));

            DB::commit();

            return redirect()->route('eleves.index')
                ->with('success', 'Élève et compte utilisateur créés avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur création élève : ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Erreur serveur : '.$e->getMessage()]);
        }
    }

    // FORMULAIRE D'EDITION
    public function edit(Eleve $eleve)
    {
        return Inertia::render('Admin/Eleves/Edit', [
            'eleve' => $eleve->load(['user']),
        ]);
    }

    // MISE À JOUR ELEVE
    public function update(Request $request, Eleve $eleve)
    {
        $request->validate([
            'matricule' => 'required|unique:eleves,matricule,' . $eleve->id,
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'date_naissance' => 'nullable|date',
            'email' => 'nullable|email|unique:users,email,' . $eleve->user_id,
            'sexe' => 'nullable|string',
            'adresse' => 'nullable|string',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:4|confirmed',
        ]);

        DB::beginTransaction();

        try {
            // GESTION USER
            if ($eleve->user || $request->email) {
                if ($eleve->user) {
                    $user = $eleve->user;
                    $user->prenom = $request->prenom;
                    $user->nom = $request->nom;
                    if ($request->email) $user->email = $request->email;
                    if ($request->password) $user->password = Hash::make($request->password);
                    $user->save();
                } 
                else {
                    $user = User::create([
                        'prenom' => $request->prenom,
                        'nom' => $request->nom,
                        'email' => $request->email,
                        'password' => bcrypt($request->password),
                        'role' => 'eleve',
                    ]);
                    $eleve->user_id = $user->id;
                }
            }

            // MISE À JOUR ELEVE
            $eleve->update($request->only([
                'matricule','nom','prenom','date_naissance','email','sexe','adresse','telephone'
            ]));

            DB::commit();

            return redirect()->route('eleves.index')
                ->with('success', 'Élève mis à jour avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur update élève : '.$e->getMessage());
            return back()->withErrors(['error' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }

    // SUPPRESSION ELEVE
    public function destroy(Eleve $eleve)
    {
        try {
            $eleve->delete();
            return redirect()->route('eleves.index')->with('success', 'Élève supprimé.');
        } catch (\Throwable $e) {
            Log::error('Erreur suppression élève : '.$e->getMessage());
            return back()->withErrors(['error' => 'Impossible de supprimer : '.$e->getMessage()]);
        }
    }

    // HISTORIQUE (SI INSCRIPTIONS EXISTENT)
    public function historique(Eleve $eleve)
    {
        $inscriptions = $eleve->inscriptions()
            ->orderBy('date_inscription', 'desc')
            ->get();

        return Inertia::render('Admin/Eleves/Historique', [
            'eleve' => $eleve,
            'inscriptions' => $inscriptions,
        ]);
    }
}
