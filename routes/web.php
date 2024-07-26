<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TicketController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Index', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rota para exibir a lista de departamentos    
    Route::get('/dashboard/departments', [DepartmentController::class, 'index'])->middleware(['auth', 'verified'])->name('departments.index');
    // Rota para criar departamento
    Route::post('/dashboard/departments', [DepartmentController::class, 'store'])->middleware(['auth', 'verified'])->name('departments.store');
    // Rota para excluir departamento
    Route::delete('/dashboard/departments/{department}', [DepartmentController::class, 'destroy'])->middleware(['auth', 'verified'])->name('departments.destroy');
    // Rota para atualizar departamento
    Route::patch('/dashboard/departments/{department}', [DepartmentController::class, 'update'])->middleware(['auth', 'verified'])->name('departments.update');

    // Rota para exibir a lista de usuários
    Route::get('/dashboard/users', [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('users.index');
    // Rota para criar usuário
    Route::post('/dashboard/users', [UserController::class, 'store'])->middleware(['auth', 'verified'])->name('users.store');
    // Rota para excluir usuário
    Route::delete('/dashboard/users/{user}', [UserController::class, 'destroy'])->middleware(['auth', 'verified'])->name('users.destroy');
    // Rota para atualizar usuário
    Route::patch('/dashboard/users/{user}', [UserController::class, 'update'])->middleware(['auth', 'verified'])->name('users.update');

    // Rota para exibir form de criação de chamado
    Route::get('/dashboard/createticket', [TicketController::class, 'index'])->middleware(['auth', 'verified'])->name('createTicket.index');
    // Rota para criar chamado
    Route::post('/dashboard/createticket', [TicketController::class, 'store'])->middleware(['auth', 'verified'])->name('createTicket.store');

});

require __DIR__.'/auth.php';
