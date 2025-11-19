<?php

namespace App\Http\Controllers\Eleve;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class EleveDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Eleve/Index');
    }
}