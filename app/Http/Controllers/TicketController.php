<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $departments = [];

        if ($user instanceof \App\Models\User) {
            $user->load('company');
            $departments = Department::where('company_id', $user->company->id)
                ->where('status', 'active') // Filtra departamentos ativos
                ->get();
        }

        return Inertia::render('Dashboard/CreateTicket', [
            'user' => $user,
            'departments' => $departments
        ]);
    }

    // Método para criar um novo ticket
    public function store(Request $request)
    {
        // Valida os dados recebidos na requisição
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'in:high,medium,low'],
        ]);

        // Criação do novo ticket
        Ticket::create([
            'department_id' => $request->department_id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        // Redirecionar ou retornar uma resposta
        return redirect()->route('createTicket.index')->with('success', 'Ticket criado com sucesso!');
    }
}
