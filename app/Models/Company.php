<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory; // Inclui o trait HasFactory para permitir a criação de instâncias da fábrica para este modelo

    // Define os atributos que podem ser atribuídos em massa (mass assignable)
    protected $fillable = [
        'name', // Nome da empresa (obrigatório)
        'logo', // URL do logo da empresa (opcional)
        'cnpj', // CNPJ da empresa (opcional)
    ];

    // Define o relacionamento entre Company e User
    public function users()
    {
        // Uma empresa (Company) tem muitos usuários (User)
        return $this->hasMany(User::class);
    }

    // Define o relacionamento entre Company e Department
    public function departments()
    {
        // Uma empresa (Company) tem muitos departamentos (Department)
        return $this->hasMany(Department::class);
    }
}
