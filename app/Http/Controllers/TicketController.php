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

        // Obtém o usuário autenticado
        $user = Auth::user();

        // Obtém o departamento do usuário autenticado
        $openedByDepartmentId = $user->department_id;

        // Criação do novo ticket
        Ticket::create([
            'opened_by_department_id' => $openedByDepartmentId, // Departamento que está abrindo o chamado
            'department_id' => $request->department_id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        // Redirecionar ou retornar uma resposta
        return redirect()->route('tickets.show')->with('success', 'Ticket criado com sucesso!');
    }

    // Método para exibir a lista de tickets pertencentes ao departamento do user logado
    public function showByDepartment()
    {
        $user = auth()->user();
    
        if ($user instanceof \App\Models\User) {
            $user->load('company'); // Carrega a relação company
        }
    
        $tickets = Ticket::with(['department', 'user', 'openedByDepartment'])
            ->where('department_id', $user->department_id)
            ->get();
    
        return Inertia::render('Dashboard/TicketsForMe', [
            'user' => $user,
            'tickets' => $tickets
        ]);
    }

    // Método para exibir todos os tickets
    public function showAll()
    {
        $user = auth()->user();

        if ($user instanceof \App\Models\User) {
            $user->load('company'); // Carrega a relação company
        }

        $tickets = Ticket::with(['department', 'user', 'openedByDepartment'])->get();

        return Inertia::render('Dashboard/ShowAllTickets', [
            'user' => $user,
            'tickets' => $tickets
        ]);
    }

    // Método para exibir chamados abertos por mim
    public function showOpendByMe()
    {
        $user = auth()->user();

        if ($user instanceof \App\Models\User) {
            $user->load('company'); // Carrega a relação company
        }

        $tickets = Ticket::with(['department', 'user', 'openedByDepartment'])
            ->where('opened_by_department_id', $user->department_id)
            ->get();

        return Inertia::render('Dashboard/TicketsOpenedByMe', [
            'user' => $user,
            'tickets' => $tickets
        ]);
    }

    // Método para exibir um único chamado
    public function showTicket($id)
    {
        $user = auth()->user();

        // Carrega a relação company se o usuário for do tipo App\Models\User
        if ($user instanceof \App\Models\User) {
            $user->load('company');
        }

        // Busca o ticket pelo ID
        $ticket = Ticket::with(['department', 'user', 'openedByDepartment'])->findOrFail($id);

        // Retorna a visualização com os dados do ticket
        return Inertia::render('Dashboard/Ticket', [
            'user' => $user,
            'ticket' => $ticket
        ]);
    }
}
