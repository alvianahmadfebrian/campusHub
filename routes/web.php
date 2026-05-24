<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DriveController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman Awal
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Route::middleware('guest.supabase')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('supabase.auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Public Share Drive
|--------------------------------------------------------------------------
*/
Route::get('/share/folder/{token}/file/{fileId}', [DriveController::class, 'publicFolderFile'])
    ->name('share.folder.file');

Route::get('/share/folder/{token}/{folderId?}', [DriveController::class, 'publicFolder'])
    ->name('share.folder');

Route::get('/share/file/{token}', [DriveController::class, 'publicFile'])
    ->name('share.file');

/*
|--------------------------------------------------------------------------
| Halaman User Login
|--------------------------------------------------------------------------
*/
Route::middleware('supabase.auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::post('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | Akademik
    |--------------------------------------------------------------------------
    */
    Route::get('/pengumuman', [PengumumanController::class, 'index'])
        ->name('pengumuman.index');

    Route::get('/materi', [MateriController::class, 'index'])
        ->name('materi.index');

    Route::get('/events', [EventController::class, 'index'])
        ->name('events.index');

    /*
    |--------------------------------------------------------------------------
    | Drive
    |--------------------------------------------------------------------------
    */
    Route::get('/drive/{folderId?}', [DriveController::class, 'index'])
        ->name('drive.index');

    Route::post('/drive/folders', [DriveController::class, 'storeFolder'])
        ->name('drive.folders.store');

    Route::patch('/drive/folders/{id}', [DriveController::class, 'updateFolder'])
        ->name('drive.folders.update');

    Route::delete('/drive/folders/{id}', [DriveController::class, 'destroyFolder'])
        ->name('drive.folders.destroy');

    Route::post('/drive/files', [DriveController::class, 'storeFile'])
        ->name('drive.files.store');

    Route::patch('/drive/files/{id}', [DriveController::class, 'updateFile'])
        ->name('drive.files.update');

    Route::delete('/drive/files/{id}', [DriveController::class, 'destroyFile'])
        ->name('drive.files.destroy');

    Route::get('/drive/files/{id}/download', [DriveController::class, 'downloadFile'])
        ->name('drive.files.download');

    /*
    |--------------------------------------------------------------------------
    | Chatbot CampusHub
    |--------------------------------------------------------------------------
    | Semua route chatbot wajib login.
    | Dokumen chatbot otomatis difilter berdasarkan akun login.
    */
    Route::get('/chat', [ChatController::class, 'index'])
        ->name('chat.index');

    Route::post('/chat/messages', [ChatController::class, 'store'])
        ->middleware('throttle:12,1')
        ->name('chat.messages.store');

    Route::delete('/chat', [ChatController::class, 'clear'])
        ->name('chat.clear');

    Route::post('/chat/documents', [ChatController::class, 'uploadDocument'])
        ->name('chat.documents.store');

    Route::delete('/chat/documents/{id}', [ChatController::class, 'destroyDocument'])
        ->name('chat.documents.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['supabase.auth', 'supabase.admin'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])
            ->name('dashboard');

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
