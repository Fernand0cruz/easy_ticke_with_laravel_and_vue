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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::prefix('dashboard')->group(function () {
        // Routes for Departments
        Route::resource('departments', DepartmentController::class)->except(['create', 'edit', 'show']);

        // Routes for Users
        Route::resource('users', UserController::class)->except(['create', 'edit', 'show']);

        // Routes for Tickets
        Route::get('/createticket', [TicketController::class, 'index'])->name('createTicket.index');
        Route::post('/createticket', [TicketController::class, 'store'])->name('createTicket.store');
        Route::get('/alltickets', [TicketController::class, 'showAll'])->name('allTickets.show');
        Route::get('/tickets', [TicketController::class, 'showByDepartment'])->name('tickets.show');
        Route::get('/mytickets', [TicketController::class, 'showOpendByMe'])->name('myTickets.show');
        Route::get('/ticket/{id}', [TicketController::class, 'showTicket'])->name('ticket.show');
    });
});

require __DIR__.'/auth.php';
