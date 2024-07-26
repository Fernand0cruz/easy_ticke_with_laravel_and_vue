<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    // Método para exibir a lista de departamentos
    public function index()
    {
        // Obtém o usuário autenticado
        $user = auth()->user();
        $departments = [];

        // Verifica se o usuário está autenticado e possui uma empresa associada
        if ($user && $user->company) {
            // Busca todos os departamentos associados à empresa do usuário autenticado
            $departments = Department::where('company_id', $user->company->id)->get();
        }

        // Renderiza a view 'Dashboard/Departments' passando o usuário e a lista de departamentos
        return Inertia::render('Dashboard/Departments', [
            'user' => $user,
            'departments' => $departments
        ]);
    }

    // Método para criar um novo departamento
    public function store(Request $request)
    {
        // Valida os dados recebidos na requisição
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.unique' => 'Já existe um departamento com esse nome.'
        ]);

        // Obtém o usuário autenticado
        $user = auth()->user();

        // Verifica se o usuário está autenticado e possui uma empresa associada
        if ($user && $user->company) {
            // Cria um novo departamento associado à empresa do usuário
            Department::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_id' => $user->company->id,
            ]);
        }

        // Redireciona para a rota de listagem de departamentos
        return redirect()->route('departments.index');
    }

    // Método para excluir um departamento
    public function destroy($id)
    {
        // Encontra o departamento pelo ID fornecido
        $department = Department::findOrFail($id);

        // Verifica se o departamento pertence à empresa do usuário autenticado
        if (auth()->user()->company_id !== $department->company_id) {
            return redirect()->route('departments.index')->with('error', 'Você não tem permissão para excluir este departamento.');
        }

        // Verifica se o departamento tem usuários associados
        if ($department->users()->exists()) {
            return redirect()->route('departments.index')->with('error', 'Não é possível excluir o departamento porque há usuários associados.');
        }

        // Exclui o departamento
        $department->delete();

        // Redireciona para a rota de listagem de departamentos com uma mensagem de sucesso
        return redirect()->route('departments.index')->with('success', 'Departamento excluído com sucesso.');
    }

    // Método para atualizar um departamento existente
    public function update(Request $request, Department $department)
    {
        // Valida os dados recebidos na requisição
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        // Verifica se o departamento pertence à empresa do usuário autenticado
        if (auth()->user()->company_id !== $department->company_id) {
            return back()->with('error', 'Você não tem permissão para atualizar este departamento.');
        }

        // Atualiza os dados do departamento com os dados fornecidos na requisição
        $department->update($request->all());

        // Redireciona de volta para a página anterior com uma mensagem de sucesso
        return back()->with('success', 'Departamento atualizado com sucesso.');
    }
}
