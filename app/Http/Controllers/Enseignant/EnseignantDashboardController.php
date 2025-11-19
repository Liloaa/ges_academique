<?php

namespace App\Http\Controllers\Enseignant;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class EnseignantDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Enseignant/Index'); 
    }
}