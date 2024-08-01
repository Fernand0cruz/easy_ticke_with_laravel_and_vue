<?php

namespace App\Http\Controllers;

use App\Models\TicketResponses;
use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // Método para exibir a página de criação de tickets
    public function index()
    {
        $user = auth()->user();
        if ($user) {
            $user->load('company'); // Carrega o relacionamento 'company' do usuário
        }

        // Obtém os departamentos ativos da empresa do usuário autenticado
        $departments = $user ? $this->getActiveDepartmentsForCompany($user->company->id) : [];

        // Renderiza a página de criação de tickets com Inertia, passando os departamentos
        return Inertia::render('Dashboard/CreateTicket', compact('departments'));
    }

    // Método privado para obter os departamentos ativos de uma empresa específica
    private function getActiveDepartmentsForCompany($companyId)
    {
        return Department::where('company_id', $companyId)
            ->where('status', 'active') // Filtra pelos departamentos com status 'active'
            ->get();
    }

    // Método para criar um novo ticket
    public function store(Request $request)
    {
        // Valida os dados da requisição usando as regras de validação definidas
        $request->validate($this->getValidationRules());

        // Obtém o ID do departamento do usuário autenticado
        $openedByDepartmentId = auth()->user()->department_id;

        // Cria um novo ticket com os dados validados
        Ticket::create($this->getTicketData($request, $openedByDepartmentId));

        // Redireciona para o dashboard com uma mensagem de sucesso
        return redirect()->route('dashboard')->with('success', 'Ticket criado com sucesso!');
    }

    // Método privado para definir as regras de validação
    private function getValidationRules()
    {
        return [
            'title' => ['required', 'string', 'max:255'], // Título é obrigatório, deve ser uma string e máximo de 255 caracteres
            'department_id' => ['required', 'exists:departments,id'], // ID do departamento é obrigatório e deve existir na tabela de departamentos
            'description' => ['required', 'string'], // Descrição é obrigatória e deve ser uma string
            'priority' => ['required', 'in:high,medium,low'], // Prioridade é obrigatória e deve ser 'high', 'medium' ou 'low'
        ];
    }

    // Método privado para obter os dados do ticket a partir da requisição
    private function getTicketData(Request $request, $openedByDepartmentId)
    {
        return [
            'opened_by_department_id' => $openedByDepartmentId, // ID do departamento que abriu o ticket
            'department_id' => $request->department_id, // ID do departamento destinatário do ticket
            'user_id' => Auth::id(), // ID do usuário autenticado que abriu o ticket
            'title' => $request->title, // Título do ticket
            'description' => $request->description, // Descrição do ticket
            'priority' => $request->priority, // Prioridade do ticket
        ];
    }

    // Método para exibir a lista de tickets pertencentes ao departamento do usuário logado
    public function showByDepartment()
    {
        $user = auth()->user();
        if ($user) {
            $user->load('company'); // Carrega o relacionamento 'company' do usuário
        }

        // Obtém os tickets do departamento do usuário autenticado
        $tickets = $user ? $this->getTicketsByDepartment($user->department_id) : [];

        // Renderiza a página de tickets do departamento com Inertia, passando os tickets
        return Inertia::render('Dashboard/TicketsForMe', compact('tickets'));
    }

    // Método privado para obter os tickets de um departamento específico
    private function getTicketsByDepartment($departmentId)
    {
        return Ticket::with(['department', 'user', 'openedByDepartment']) // Carrega os relacionamentos necessários
            ->where('department_id', $departmentId) // Filtra pelos tickets do departamento especificado
            ->get();
    }

    // Método para exibir todos os tickets
    public function showAll()
    {
        $user = auth()->user();
        if ($user) {
            $user->load('company'); // Carrega o relacionamento 'company' do usuário
        }

        // Obtém todos os tickets
        $tickets = $user ? $this->getAllTickets() : [];

        // Renderiza a página de todos os tickets com Inertia, passando os tickets
        return Inertia::render('Dashboard/ShowAllTickets', compact('tickets'));
    }

    // Método privado para obter todos os tickets
    private function getAllTickets()
    {
        return Ticket::with(['department', 'user', 'openedByDepartment', 'assignedUser']) // Carrega os relacionamentos necessários
            ->get();
    }

    // Método para exibir tickets abertos pelo usuário autenticado
    public function showOpenedByMe()
    {
        $user = auth()->user();
        if ($user) {
            $user->load('company'); // Carrega o relacionamento 'company' do usuário
        }

        // Obtém os tickets abertos pelo usuário autenticado
        $tickets = $user ? $this->getOpenedByMe($user) : [];

        // Renderiza a página de tickets abertos pelo usuário com Inertia, passando os tickets
        return Inertia::render('Dashboard/TicketsOpenedByMe', compact('tickets'));
    }

    // Método privado para obter os tickets abertos pelo usuário autenticado
    private function getOpenedByMe($user)
    {
        return Ticket::with(['department', 'user', 'openedByDepartment']) // Carrega os relacionamentos necessários
            ->where('opened_by_department_id', $user->department_id) // Filtra pelos tickets abertos pelo departamento do usuário autenticado
            ->get();
    }

    // Método para exibir um único ticket e suas respostas
    public function showTicket($id)
    {
        $user = auth()->user();
        if ($user) {
            $user->load('company'); // Carrega o relacionamento 'company' do usuário
        }

        // Obtém o ticket pelo ID
        $ticket = $user ? $this->getTicket($id) : [];

        // Obtém as respostas do ticket pelo ID do ticket
        $responses = $user ? $this->getTicketResponse($id) : [];

        // Renderiza a página do ticket com Inertia, passando o usuário, o ticket e as respostas
        return Inertia::render('Dashboard/Ticket', compact('user', 'ticket', 'responses'));
    }

    // Método privado para obter um ticket pelo ID
    private function getTicket($id)
    {
        return Ticket::with(['department', 'user', 'openedByDepartment']) // Carrega os relacionamentos necessários
            ->findOrFail($id); // Encontra o ticket pelo ID ou falha se não existir
    }

    // Método privado para obter as respostas de um ticket pelo ID do ticket
    private function getTicketResponse($id)
    {
        return TicketResponses::with(['user', 'user.department']) // Carrega os relacionamentos necessários
            ->where('ticket_id', $id) // Filtra pelas respostas do ticket especificado
            ->get();
    }

    // Método para atribuir um ticket a um usuário
    public function assignToUser($id)
    {
        $user = auth()->user();
        if ($user) {
            $user->load('company'); // Carrega o relacionamento 'company' do usuário
        }

        // Obtém o ticket pelo ID
        $ticket = $user ? $this->getAssignToUser($id) : [];

        // Verifica se o ticket já está atribuído a outro usuário
        if ($ticket->assigned_to_user_id !== null && $ticket->assigned_to_user_id !== $user->id) {
            // Redireciona para o dashboard com uma mensagem de erro
            return redirect()->route('dashboard')->with('error', 'Este ticket já está vinculado a outra pessoa');
        }

        // Atribui o ticket ao usuário autenticado
        $ticket->assigned_to_user_id = $user->id;
        $ticket->save();

        // Redireciona para o dashboard com uma mensagem de sucesso
        return redirect()->route('dashboard')->with('success', 'Você foi vinculado ao ticket');
    }

    // Método privado para obter um ticket pelo ID
    private function getAssignToUser($id)
    {
        return Ticket::findOrFail($id); // Encontra o ticket pelo ID ou falha se não existir
    }

    // Método para criar uma nova resposta ao ticket
    public function storeResponse(Request $request, $id)
    {
        // Valida os dados da requisição usando as regras de validação definidas
        $request->validate($this->getResponsesValidationRules());

        // Cria uma nova resposta ao ticket com os dados validados
        TicketResponses::create($this->getResponsesData($request, $id));

        // Redireciona para o dashboard com uma mensagem de sucesso
        return redirect()->route('dashboard')->with('success', 'Resposta criada com sucesso!');
    }

    // Método privado para definir as regras de validação para respostas
    private function getResponsesValidationRules()
    {
        return [
            'response' => ['required', 'string'], // Resposta é obrigatória e deve ser uma string
        ];
    }

    // Método privado para obter os dados da resposta a partir da requisição
    private function getResponsesData($request, $id)
    {
        return [
            'user_id' => Auth::id(), // ID do usuário autenticado que criou a resposta
            'response' => $request->response, // Conteúdo da resposta
            'ticket_id' => $id, // ID do ticket ao qual a resposta pertence
        ];
    }
}
