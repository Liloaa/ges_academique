<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Enseignant;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnseignantController extends Controller
{
    /**
     * Display a listing of the resource.Liste des enseignants
     */
    public function index()
    {
        $enseignants = Enseignant::with('user')->paginate(10);

        return Inertia::render('Admin/Dashboard', [
            'enseignants' => $enseignants,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /*$users = User::where('role', 'enseignant')->get();

        return Inertia::render('Admin/Enseignants/Create', [
            'users' => $users,
        ]);*/
        return inertia('Admin/Enseignants/Create');
    }

    /**
     * Store a newly created resource in storage(Sauvegarde).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'matriculeEnsei' => 'nullable|string|max:50|unique:enseignants',
            'specialite' => 'nullable|string|max:150',
            'grade' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',           
        ]);

        Enseignant::create($validated);

        return redirect()->route('admin.enseignants.index')->with('success', 'Enseignant créé avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
