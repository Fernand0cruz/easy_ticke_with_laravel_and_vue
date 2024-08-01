<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    /**
     * Exibe a lista de departamentos.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        // Se o usuário estiver autenticado e pertencer a uma empresa, obtém os departamentos da empresa
        $departments = auth()->user()->company->departments;

        // Renderiza a página de departamentos com Inertia, passando os departamentos
        return Inertia::render('Dashboard/Departments', compact('departments'));
    }

    /**
     * Cria um novo departamento.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Valida os dados da requisição usando as regras de validação definidas
        $request->validate($this->getValidationRules(), $this->getValidationMessages());

        // Cria um novo departamento com os dados validados
        Department::create($this->getDepartmentData($request, auth()->user()->company_id));

        // Redireciona para a lista de departamentos com uma mensagem de sucesso
        return redirect()->route('departments.index')->with('success', 'Departamento criado com sucesso.');
    }

    /**
     * Define as regras de validação.
     *
     * @return array
     */
    private function getValidationRules()
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:departments,name'], // Nome é obrigatório, deve ser uma string, máximo de 255 caracteres e único
            'email' => ['required', 'email', 'max:255'], // Email é obrigatório, deve ser um email válido e máximo de 255 caracteres
            'phone' => ['nullable', 'string', 'max:20'], // Telefone é opcional, mas se fornecido deve ser uma string e máximo de 20 caracteres
            'status' => ['required', 'in:active,inactive'], // Status é obrigatório e deve ser 'active' ou 'inactive'
        ];
    }

    /**
     * Define as mensagens de validação personalizadas.
     *
     * @return array
     */
    private function getValidationMessages()
    {
        return [
            'name.unique' => 'Já existe um departamento com esse nome.', // Mensagem personalizada para o campo 'name' único
        ];
    }

    /**
     * Obtém os dados do departamento a partir da requisição.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $companyId
     * @return array
     */
    private function getDepartmentData(Request $request, $companyId)
    {
        return [
            'name' => $request->name, // Nome do departamento
            'email' => $request->email, // Email do departamento
            'phone' => $request->phone, // Telefone do departamento
            'status' => $request->status, // Status do departamento
            'company_id' => $companyId, // ID da empresa do usuário autenticado
        ];
    }

    /**
     * Deleta um departamento.
     *
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Department $department)
    {
        // Deleta o departamento fornecido
        $department->delete();

        // Redireciona para a lista de departamentos com uma mensagem de sucesso
        return redirect()->route('departments.index')->with('success', 'Departamento deletado com sucesso.');
    }

    /**
     * Atualiza um departamento.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Department $department
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Department $department)
    {
        // Obtém as regras de validação
        $rules = $this->getValidationRules();

        // Adiciona uma regra personalizada para o campo 'name' ignorando o ID do departamento atual
        $rules['name'] = ['required', 'string', 'max:255', \Illuminate\Validation\Rule::unique('departments', 'name')->ignore($department->id)];

        // Valida os dados da requisição usando as regras de validação
        $request->validate($rules);

        // Atualiza o departamento com os dados validados
        $department->update($this->getDepartmentData($request, auth()->user()->company_id));

        // Redireciona para a mesma página com uma mensagem de sucesso
        return back()->with('success', 'Departamento atualizado com sucesso.');
    }
}
