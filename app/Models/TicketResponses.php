<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketResponses extends Model
{
    use HasFactory; // Inclui o trait HasFactory para permitir a criação de instâncias da fábrica para este modelo

    /**
     * Atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'ticket_id', // ID do ticket ao qual a resposta pertence
        'user_id', // ID do usuário que forneceu a resposta
        'response', // Conteúdo da resposta
    ];

    /**
     * Relacionamento com o modelo Ticket.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ticket()
    {
        // Uma resposta (TicketResponse) pertence a um ticket (Ticket)
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relacionamento com o modelo User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // Uma resposta (TicketResponse) pertence a um usuário (User) que forneceu a resposta
        return $this->belongsTo(User::class);
    }
}
