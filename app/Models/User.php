<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail; // Não utilizado atualmente, pode ser removido se não necessário
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // Usa os traits HasFactory e Notifiable na classe User

    /**
     * Os atributos que podem ser atribuídos em massa (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Nome do usuário
        'email', // Email do usuário
        'phone', // Telefone do usuário (adicionado campo telefone)
        'status', // Status do usuário (adicionado campo status)
        'role', // Papel do usuário (adicionado campo role)
        'company_id', // Chave estrangeira para a tabela Company
        'department_id', // Chave estrangeira para a tabela Department
        'password', // Senha do usuário
    ];

    /**
     * Os atributos que devem ser ocultados durante a serialização.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', // Senha do usuário (oculta)
        'remember_token', // Token de lembrete para autenticação
    ];

    /**
     * Obtém os atributos que devem ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Converte o atributo email_verified_at para o formato datetime
            'password' => 'hashed', // Converte a senha para um formato hash
        ];
    }

    /**
     * Define o relacionamento com a Company.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        // Um usuário (User) pertence a uma empresa (Company)
        return $this->belongsTo(Company::class);
    }

    /**
     * Define o relacionamento com o Department.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function department()
    {
        // Um usuário (User) pertence a um departamento (Department)
        return $this->belongsTo(Department::class);
    }

    /**
     * Define o relacionamento com TicketResponses.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ticketResponses()
    {
        // Um usuário (User) pode ter muitas respostas de tickets (TicketResponses)
        return $this->hasMany(TicketResponses::class);
    }
}
