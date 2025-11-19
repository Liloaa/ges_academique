<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'prenom' => ['required', 'string', 'max:100'],
            'nom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,enseignant,eleve'],
            'code_secret' => ['nullable', 'string'],
        ]);

        // Vérification des codes secrets
        if ($request->role === 'admin' && $request->code_secret !== env('ADMIN_SECRET')) {
            return back()->withErrors(['code_secret' => 'Code admin invalide.'])->withInput();
        }

        if ($request->role === 'enseignant' && $request->code_secret !== env('ENSEIGNANT_SECRET')) {
            return back()->withErrors(['code_secret' => 'Code enseignant invalide.'])->withInput();
        }

        $user = User::create([
            'prenom' => $request->prenom,
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirection selon le rôle
        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role === 'enseignant') {
            return redirect()->route('enseignants.dashboard');
        } else {
            return redirect()->route('eleves.dashboard');
        }

    }
}
