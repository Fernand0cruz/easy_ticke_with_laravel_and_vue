<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('company');
        return Inertia::render('Dashboard/Dashboard', ['user' => $user]);
    }
}

