<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',   // Campo opcional
        'status', // Status (active/inactive)
        'company_id', // Chave estrangeira para Company
    ];

    public function users()
    {
        //departments tem mts users
        return $this->hasMany(User::class);
    }

    public function company()
    {
        //departments tem 1 company
        return $this->belongsTo(Company::class);
    }
}
