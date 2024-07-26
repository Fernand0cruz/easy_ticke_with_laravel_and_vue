<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

class UserController extends Controller
{
    public function index()
    {
        // Carrega o usuário autenticado e sua empresa associada, se houver
        $user = auth()->user();
        $users = [];
        $departments = [];

        if ($user && $user->company) {
            // Busca todos os usuários da empresa do usuário autenticado e carrega o departamento associado
            $users = User::where('company_id', $user->company->id)
                ->with('department') // Carrega a relação com o departamento
                ->get()
                ->map(function ($user) {
                    // Adiciona o nome do departamento ao usuário
                    $user->department_name = $user->department ? $user->department->name : 'N/A';
                    return $user;
                });

            // Busca todos os departamentos ativos da empresa do usuário autenticado
            $departments = Department::where('company_id', $user->company->id)
                ->where('status', 'active') // Filtra departamentos ativos
                ->get();
        }

        // Renderiza a view passando os usuários, departamentos e o usuário autenticado
        return Inertia::render('Dashboard/Users', [
            'user' => $user,
            'users' => $users,
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        // Valida os dados do formulário
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'in:active,inactive'],
            'role' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ] , [
            'email.unique' => 'Email já cadastrado nessa empresa.'
        ]);

        // Obtém o company_id do usuário autenticado
        $companyId = auth()->user()->company_id;
        
        // Cria um novo usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'role' => $request->role,
            'company_id' => $companyId, // Define o company_id do usuário autenticado
            'department_id' => $request->department_id,
            'password' => Hash::make($request->password),
        ]);

        // Disparar o evento Registered
        event(new Registered($user));

        // Redireciona para a página de usuários ou retorna a página com sucesso
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function destroy(User $user)
    {
        $user = User::findOrFail($user->id);

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function update(Request $request, User $user)
{
    // Validação condicional para a senha
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
        'phone' => ['required', 'string', 'max:255'],
        'department_id' => ['required', 'exists:departments,id'],
        'status' => ['required', 'in:active,inactive'],
        'role' => ['required', 'in:user,admin'],
    ];

    // Adiciona a validação de senha se o campo estiver presente
    if ($request->filled('password')) {
        $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
    }

    $request->validate($rules);

    $companyId = auth()->user()->company_id;

    // Atualiza os dados do usuário
    $userData = [
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'department_id' => $request->department_id,
        'company_id' => $companyId,
        'status' => $request->status,
        'role' => $request->role,
    ];

    // Atualiza a senha somente se fornecida
    if ($request->filled('password')) {
        $userData['password'] = Hash::make($request->password);
    }

    $user->update($userData);

    return back()->with('success', 'Usuário atualizado com sucesso');
}

}
