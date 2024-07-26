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
     *
     * @return Response
     */
    public function create(): Response
    {
       
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'), 
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Valida os dados de entrada do usuário. 
        $request->validate([
            'company' => ['required', 'string'], 
            'email' => ['required', 'string', 'email'], 
            'password' => ['required', 'string'],
        ]);

        // Busca a empresa pelo nome fornecido no request.
        $company = Company::where('name', $request->company)->first();

        // Se a empresa não for encontrada, lança uma exceção de validação com uma mensagem de erro.
        if (!$company) {
            throw ValidationException::withMessages([
                'company' => 'Empresa não encontrada.',
            ]);
        }

        // Busca o usuário pelo email fornecido e verifica se ele pertence à empresa encontrada.
        $user = User::where('email', $request->email)
                    ->where('company_id', $company->id)
                    ->first();

        // Se o usuário não for encontrado, lança uma exceção de validação com uma mensagem de erro.
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Usuário não encontrado na empresa informada.',
            ]);
        }

        // Verifica se a senha fornecida corresponde à senha do usuário usando o Hash.
        // Lança uma exceção de validação com uma mensagem de erro se a senha for inválida.
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Senha inválida.',
            ]);
        }

        // Se todas as validações passarem, realiza o login do usuário.
        Auth::login($user);

        // Regenera a sessão para garantir que não haja dados antigos da sessão.
        $request->session()->regenerate();

        // Redireciona o usuário para a página de dashboard ou para a página que o usuário estava tentando acessar antes do login.
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Faz o logout do usuário autenticado.
        Auth::guard('web')->logout();

        // Invalida a sessão atual para garantir que todos os dados da sessão sejam removidos.
        $request->session()->invalidate();

        // Regenera o token da sessão para prevenir ataques CSRF.
        $request->session()->regenerateToken();

        // Redireciona o usuário para a página inicial.
        return redirect('/');
    }
}
