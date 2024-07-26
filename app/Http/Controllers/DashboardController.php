<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user instanceof \App\Models\User) {
            $user->load('company');
        }

        return Inertia::render('Dashboard/Dashboard', ['user' => $user]);
    }
}
