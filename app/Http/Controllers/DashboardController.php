<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Exibe a página inicial do painel de controle.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Obtém o usuário autenticado
        $user = auth()->user();

        // Verifica se há um usuário autenticado
        if ($user) {
            // Carrega a relação 'company' para o usuário autenticado
            $user->load('company');
        }

        // Renderiza a view do dashboard usando Inertia.js
        // Passa o objeto do usuário autenticado para a view
        return Inertia::render('Dashboard/Dashboard', compact('user'));
    }
}
