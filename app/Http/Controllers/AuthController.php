<?php

namespace App\Http\Controllers;

use App\Services\SupabaseAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function register(Request $request, SupabaseAuthService $auth): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'nim' => ['nullable', 'string', 'max:50'],
            'jurusan' => ['nullable', 'string', 'max:120'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        try {
            $auth->signUp($validated['email'], $validated['password']);
            $login = $auth->signIn($validated['email'], $validated['password']);
        } catch (Throwable $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        $user = $login['user'];

        DB::table('profiles')->upsert([
            'id' => $user['id'],
            'nama' => $validated['nama'],
            'nim' => $validated['nim'] ?? null,
            'jurusan' => $validated['jurusan'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'role' => 'mahasiswa',
            'created_at' => now(),
        ], ['id'], ['nama', 'nim', 'jurusan', 'semester']);

        $request->session()->regenerate();
        $request->session()->put('supabase_user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'access_token' => $login['access_token'] ?? null,
            'refresh_token' => $login['refresh_token'] ?? null,
        ]);

        return redirect()->route('dashboard')->with('success', 'Akun berhasil dibuat.');
    }

    public function login(Request $request, SupabaseAuthService $auth): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $login = $auth->signIn($validated['email'], $validated['password']);
        } catch (Throwable $e) {
            return back()->withErrors(['email' => $e->getMessage()])->withInput();
        }

        $user = $login['user'];
        $request->session()->regenerate();
        $request->session()->put('supabase_user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'access_token' => $login['access_token'] ?? null,
            'refresh_token' => $login['refresh_token'] ?? null,
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('supabase_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
