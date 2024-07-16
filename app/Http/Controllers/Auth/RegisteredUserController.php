<?php

namespace App\Http\Controllers\Auth;

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Company;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'company' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Verificar se a empresa já existe
        $existingCompany = Company::where('name', $request->company)->first();

        // Se a empresa existir, verificar se já tem um admin registrado
        if ($existingCompany) {
            $existingAdmin = $existingCompany->users()->first();
            if ($existingAdmin) {
                return redirect()->back()->withErrors([
                    'company' => 'Já existe um administrador registrado nesta empresa.',
                ])->withInput(); // Retornar com os dados de entrada
            }
        } else {
            // Se a empresa não existir, criar
            $existingCompany = Company::create(['name' => $request->company]);
        }

        // Criar o usuário
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'company_id' => $existingCompany->id,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => 'admin', // Atribuir 'admin' ao primeiro usuário
            'status' => 'active', // Defina o status conforme necessário
        ]);

        // Disparar o evento Registered
        event(new Registered($user));

        // Logar o usuário
        Auth::login($user);

        return redirect(route('dashboard', [], false));
    }
}

