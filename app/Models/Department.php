<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory; // Inclui o trait HasFactory para permitir a criação de instâncias da fábrica para este modelo

    // Define os atributos que podem ser atribuídos em massa (mass assignable)
    protected $fillable = [
        'name', // Nome do departamento
        'email', // Email do departamento
        'phone', // Telefone do departamento (opcional)
        'status', // Status do departamento (active/inactive)
        'company_id', // Chave estrangeira para a tabela Company
    ];

    // Define o relacionamento entre Department e User
    public function users()
    {
        // Um departamento (Department) tem muitos usuários (User)
        return $this->hasMany(User::class);
    }

    // Define o relacionamento entre Department e Company
    public function company()
    {
        // Um departamento (Department) pertence a uma empresa (Company)
        return $this->belongsTo(Company::class);
    }
}
