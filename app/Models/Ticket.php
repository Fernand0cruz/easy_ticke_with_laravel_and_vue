<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'opened_by_department_id',
        'department_id',
        'user_id',
        'title',
        'description',
        'priority',
    ];

    // Definindo relacionamentos
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function openedByDepartment()
    {
        return $this->belongsTo(Department::class, 'opened_by_department_id');
    }
}
