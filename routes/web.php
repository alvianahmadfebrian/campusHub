<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $userId = session()->get('supabase_user.id');

    if (!$userId) {
        return redirect()->route('login');
    }

    $role = DB::table('profiles')
        ->where('id', $userId)
        ->value('role');

    return $role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard');
});

Route::middleware('guest.supabase')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('supabase.auth')
    ->name('logout');

Route::middleware('supabase.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['supabase.auth', 'supabase.admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::post('/jurusan', [JurusanController::class, 'store'])
            ->name('jurusan.store');

        Route::patch('/jurusan/{id}/toggle', [JurusanController::class, 'toggle'])
            ->name('jurusan.toggle');

        Route::post('/pengumuman', [PengumumanController::class, 'store'])
            ->name('pengumuman.store');

        Route::post('/materi', [MateriController::class, 'store'])
            ->name('materi.store');

        Route::post('/events', [EventController::class, 'store'])
            ->name('events.store');
    });
