<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory; // Inclui o trait HasFactory para permitir a criação de instâncias da fábrica para este modelo

    // Define os atributos que podem ser atribuídos em massa (mass assignable)
    protected $fillable = [
        'opened_by_department_id', // ID do departamento que abriu o ticket
        'department_id', // ID do departamento responsável pelo ticket
        'user_id', // ID do usuário que abriu o ticket
        'assigned_to_user_id', // ID do usuário a quem o ticket está atribuído
        'title', // Título do ticket
        'description', // Descrição do ticket
        'priority', // Prioridade do ticket
    ];

    // Define o relacionamento entre Ticket e Department
    public function department()
    {
        // Um ticket (Ticket) pertence a um departamento (Department)
        return $this->belongsTo(Department::class);
    }

    // Define o relacionamento entre Ticket e User
    public function user()
    {
        // Um ticket (Ticket) pertence a um usuário (User) que o abriu
        return $this->belongsTo(User::class);
    }

    // Define o relacionamento entre Ticket e o departamento que abriu o ticket
    public function openedByDepartment()
    {
        // Um ticket (Ticket) pertence a um departamento (Department) que o abriu
        return $this->belongsTo(Department::class, 'opened_by_department_id');
    }

    // Define o relacionamento entre Ticket e o usuário ao qual o ticket está atribuído
    public function assignedUser()
    {
        // Um ticket (Ticket) pertence a um usuário (User) a quem foi atribuído
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    // Define o relacionamento entre Ticket e TicketResponses
    public function responses()
    {
        // Um ticket (Ticket) pode ter muitas respostas (TicketResponses)
        return $this->hasMany(TicketResponses::class);
    }
}
