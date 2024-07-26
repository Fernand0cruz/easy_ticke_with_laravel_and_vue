<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        // Renderiza a view de registro usando Inertia.js.
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação dos dados do formulário de registro.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Verifica se a empresa fornecida já existe no banco de dados.
        $existingCompany = Company::where('name', $request->company)->first();

        // Se a empresa já existe, verifica se já há um administrador registrado para ela.
        if ($existingCompany) {
            $existingAdmin = $existingCompany->users()->where('role', 'admin')->first();
            if ($existingAdmin) {
                // Se já houver um administrador registrado, retorna um erro e os dados inseridos.
                return redirect()->back()->withErrors([
                    'company' => 'Já existe um administrador registrado nesta empresa.',
                ])->withInput();
            }
        } else {
            // Se a empresa não existe, cria uma nova empresa com os dados fornecidos.
            $existingCompany = Company::create([
                'name' => $request->company,
                'phone' => $request->phone, 
                'status' => 'active', // Define o status da empresa como ativo.
            ]);
        }

        // Cria um novo departamento para o administrador na empresa existente.
        $department = Department::create([
            'name' => 'Gestor de Sistemas',
            'company_id' => $existingCompany->id, // Associa o departamento à empresa criada ou existente.
            'email' => $request->email, // Atribui o email do administrador ao departamento.
        ]);

        // Cria o usuário com os dados fornecidos.
        $user = User::create([
            'name' => $request->name, 
            'email' => $request->email, 
            'company_id' => $existingCompany->id, // Associa o usuário à empresa.
            'department_id' => $department->id, // Associa o usuário ao departamento criado.
            'phone' => $request->phone, 
            'password' => Hash::make($request->password), // Criptografa a senha do usuário.
            'role' => 'admin', // Define o papel do usuário como administrador.
            'status' => 'active', // Define o status do usuário como ativo.
        ]);

        // Dispara o evento Registered para indicar que um novo usuário foi registrado.
        event(new Registered($user));

        // Faz o login do usuário recém-criado.
        Auth::login($user);

        // Redireciona o usuário para a rota 'dashboard'.
        return redirect(route('dashboard', [], false));
    }
}
