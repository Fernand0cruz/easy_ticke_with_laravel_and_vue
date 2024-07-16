<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo', // Campo opcional
        'cnpj', // Campo opcional
    ];

    public function users()
    {
        //company tem mts users
        return $this->hasMany(User::class);
    }

    public function departments()
    {
        //companye tem mts departments
        return $this->hasMany(Department::class);
    }
}
