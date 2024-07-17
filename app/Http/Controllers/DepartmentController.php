<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function index()
    {
        // Carrega o usuário autenticado e a empresa associada, se houver
        $user = auth()->user();
        $departments = [];

        if ($user) {
            $user->load('company');
            
            if ($user->company) {
                // Busca os departamentos da empresa do usuário autenticado
                $departments = Department::where('company_id', $user->company->id)->get();
            }
        }

        // Renderiza a view passando os departamentos e o usuário autenticado
        return Inertia::render('Dashboard/Departments', [
            'user' => $user,
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'location' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        if ($user && $user->company) {
            Department::create([
                'name' => $request->name,
                'email' => $request->email,
                'location' => $request->location,
                'phone' => $request->phone,
                'company_id' => $user->company->id,
            ]);
        }

        return redirect()->route('departments.index');
    }

}
