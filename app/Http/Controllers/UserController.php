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
    /**
     * Exibe a lista de usuários.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Obtém os usuários da empresa do usuário autenticado, se existir
        $users = $this->getUsersForCompany(auth()->user()->company->id);

        // Obtém os departamentos ativos da empresa do usuário autenticado, se existir
        $departments = $this->getActiveDepartmentsForCompany(auth()->user()->company->id);

        // Renderiza a página de usuários com Inertia, passando os usuários e os departamentos
        return Inertia::render('Dashboard/Users', compact('users', 'departments'));
    }

    /**
     * Método privado para obter os usuários de uma empresa específica.
     *
     * @param int $companyId
     * @return \Illuminate\Support\Collection
     */
    private function getUsersForCompany($companyId)
    {
        return User::where('company_id', $companyId)
            ->with('department') // Carrega o relacionamento com 'department'
            ->get()
            ->map(function ($user) {
                // Adiciona o nome do departamento ao usuário, se existir
                $user->department_name = optional($user->department)->name ?? 'N/A';
                return $user;
            });
    }

    /**
     * Método privado para obter os departamentos ativos de uma empresa específica.
     *
     * @param int $companyId
     * @return \Illuminate\Support\Collection
     */
    private function getActiveDepartmentsForCompany($companyId)
    {
        return Department::where('company_id', $companyId)
            ->where('status', 'active') // Filtra pelos departamentos com status 'active'
            ->get();
    }

    /**
     * Cria um novo usuário.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valida os dados da requisição usando as regras de validação definidas
        $request->validate($this->getValidationRules());

        // Cria um novo usuário com os dados validados
        $user = User::create($this->getUserData($request, auth()->user()->company_id));

        // Dispara o evento de registro de usuário
        event(new Registered($user));

        // Redireciona para a lista de usuários com uma mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    /**
     * Define as regras de validação.
     *
     * @return array
     */
    private function getValidationRules()
    {
        return [
            'name' => ['required', 'string', 'max:255'], // Nome é obrigatório, deve ser uma string e máximo de 255 caracteres
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'], // Email é obrigatório, deve ser uma string, um email válido, máximo de 255 caracteres e único
            'phone' => ['required', 'string', 'max:255'], // Telefone é obrigatório, deve ser uma string e máximo de 255 caracteres
            'status' => ['required', 'string', 'in:active,inactive'], // Status é obrigatório e deve ser 'active' ou 'inactive'
            'role' => ['required', 'string', 'max:255'], // Papel (role) é obrigatório, deve ser uma string e máximo de 255 caracteres
            'department_id' => ['required', 'integer', 'exists:departments,id'], // ID do departamento é obrigatório, deve ser um inteiro e existir na tabela de departamentos
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // Senha é obrigatória, deve ser confirmada e seguir as regras padrão de senha
        ];
    }

    /**
     * Obtém os dados do usuário a partir da requisição.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $companyId
     * @return array
     */
    private function getUserData(Request $request, $companyId)
    {
        $userData = [
            'name' => $request->name, // Nome do usuário
            'email' => $request->email, // Email do usuário
            'phone' => $request->phone, // Telefone do usuário
            'status' => $request->status, // Status do usuário
            'role' => $request->role, // Papel do usuário
            'company_id' => $companyId, // ID da empresa do usuário autenticado
            'department_id' => $request->department_id, // ID do departamento do usuário
        ];

        // Se a senha estiver preenchida, adiciona ao array de dados do usuário
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        return $userData;
    }

    /**
     * Deleta um usuário.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Deleta o usuário fornecido
        $user->delete();

        // Redireciona para a lista de usuários com uma mensagem de sucesso
        return redirect()->route('users.index')->with('success', 'Usuário deletado com sucesso.');
    }

    /**
     * Atualiza um usuário.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Obtém as regras de validação
        $rules = $this->getValidationRules();

        // Adiciona uma regra personalizada para o campo 'email' ignorando o ID do usuário atual
        $rules['email'] = ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users', 'email')->ignore($user->id),];

        // Se a senha estiver preenchida, adiciona a regra de validação para a senha
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        } else {
            // Se a senha não estiver preenchida, remove a regra de validação para a senha
            unset($rules['password']);
        }

        // Valida os dados da requisição usando as regras de validação
        $request->validate($rules);

        // Atualiza o usuário com os dados validados
        $user->update($this->getUserData($request, auth()->user()->company_id));

        // Redireciona para a mesma página com uma mensagem de sucesso
        return back()->with('success', 'Usuário atualizado com sucesso.');
    }
}
