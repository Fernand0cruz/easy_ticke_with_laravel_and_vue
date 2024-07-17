<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validar os campos de entrada
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'company' => ['required', 'string'],
        ]);

        // Verificar se a empresa existe
        $company = Company::where('name', $request->company)->first();

        if (!$company) {
            throw ValidationException::withMessages([
                'company' => 'Empresa não encontrada.',
            ]);
        }

        // Verificar se o usuário existe e pertence à empresa
        $user = User::where('email', $request->email)
                    ->where('company_id', $company->id)
                    ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Usuário não encontrado ou não pertence à empresa informada.',
            ]);
        }

        // Verificar se a senha está correta
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Senha inválida.',
            ]);
        }

        // Realizar o login do usuário
        Auth::login($user);

        // Regenerar a sessão
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
