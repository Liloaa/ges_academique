<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnseignantController extends Controller
{
    public function index()
    {
        $enseignants = Enseignant::with('user')->orderByDesc('id')->get();

        return Inertia::render('Admin/Enseignants/Index', [
            'enseignants' => $enseignants,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Enseignants/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'matricule'   => 'required|unique:enseignants,matriculeEnseig',
            'nom'         => 'required|string|max:100',
            'prenom'      => 'required|string|max:100',
            'grade'       => 'nullable|string|max:50',
            'specialite'  => 'nullable|string|max:50',
            'email'       => 'required|email|unique:enseignants,email|unique:users,email',
            'telephone'   => 'nullable|string|max:20',
            'sexe'        => 'nullable|string|max:10',
            'adresse'     => 'nullable|string',
            'password'    => 'required|string|min:8|confirmed',
        ]);

        DB::beginTransaction();
        try {
            // ✅ Création du compte utilisateur
            $user = User::create([
                'prenom'   => $validated['prenom'],
                'nom'      => $validated['nom'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'enseignant',
            ]);

            // ✅ Création de l'enseignant lié
            Enseignant::create([
                'matriculeEnseig' => $validated['matricule'],
                'nom'             => $validated['nom'],
                'prenom'          => $validated['prenom'],
                'grade'           => $validated['grade'],
                'specialite'      => $validated['specialite'],
                'email'           => $validated['email'],
                'telephone'       => $validated['telephone'],
                'sexe'            => $validated['sexe'],
                'adresse'         => $validated['adresse'],
                'user_id'         => $user->id,
            ]);

            DB::commit();

            return redirect()->route('enseignants.index')->with('success', 'Enseignant créé avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur création enseignant : ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['error' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }

    public function edit(Enseignant $enseignant)
    {
        return Inertia::render('Admin/Enseignants/Edit', [
            'enseignant' => $enseignant->load('user'),
        ]);
    }

    public function update(Request $request, Enseignant $enseignant)
    {
        $validated = $request->validate([
            'matricule'  => 'required|unique:enseignants,matriculeEnseig,' . $enseignant->id,
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'grade'      => 'nullable|string|max:50',
            'specialite' => 'nullable|string|max:50',
            'email'      => 'required|email|unique:enseignants,email,' . $enseignant->id . '|unique:users,email,' . ($enseignant->user->id ?? 'NULL'),
            'telephone'  => 'nullable|string|max:20',
            'sexe'       => 'nullable|string|max:10',
            'adresse'    => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // 🔹 Mise à jour de l'enseignant
            $enseignant->update([
                'matriculeEnseig' => $validated['matricule'],
                'nom'             => $validated['nom'],
                'prenom'          => $validated['prenom'],
                'grade'           => $validated['grade'],
                'specialite'      => $validated['specialite'],
                'email'           => $validated['email'],
                'telephone'       => $validated['telephone'],
                'sexe'            => $validated['sexe'],
                'adresse'         => $validated['adresse'],
            ]);

            // 🔹 Mise à jour du compte utilisateur lié
            if ($enseignant->user) {
                $enseignant->user->update([
                    'prenom' => $validated['prenom'],
                    'nom'    => $validated['nom'],
                    'email'  => $validated['email'],
                ]);
            }

            DB::commit();

            return redirect()->route('enseignants.index')->with('success', 'Enseignant mis à jour avec succès.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erreur update enseignant : ' . $e->getMessage());
            return back()->withErrors(['error' => 'Erreur serveur : ' . $e->getMessage()]);
        }
    }

    public function destroy(Enseignant $enseignant)
    {
        try {
            $enseignant->delete();
            return redirect()->route('enseignants.index')->with('success', 'Enseignant supprimé avec succès.');
        } catch (\Throwable $e) {
            Log::error('Erreur suppression enseignant : ' . $e->getMessage());
            return back()->withErrors(['error' => 'Impossible de supprimer : ' . $e->getMessage()]);
        }
    }
}