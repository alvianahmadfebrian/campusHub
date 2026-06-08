<?php

namespace App\Http\Controllers;

use App\Services\SupabaseAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
        return Inertia::render('Auth/Register', [
            'jurusan' => DB::table('jurusan')
                ->where('aktif', true)
                ->orderBy('nama')
                ->get(['id', 'nama', 'kode']),
        ]);
    }

    public function register(Request $request, SupabaseAuthService $auth): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'nim' => ['nullable', 'string', 'max:50'],
            'jurusan_id' => [
                'required',
                'uuid',
                Rule::exists('jurusan', 'id')->where(fn ($query) => $query->where('aktif', true)),
            ],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'email' => ['required', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $jurusan = DB::table('jurusan')
            ->where('id', $validated['jurusan_id'])
            ->where('aktif', true)
            ->first();

        if (!$jurusan) {
            return back()
                ->withErrors(['jurusan_id' => 'Jurusan tidak tersedia.'])
                ->withInput();
        }

        try {
            $signup = $auth->signUp(
                $validated['email'],
                $validated['password'],
                [
                    'nama' => $validated['nama'],
                    'nim' => $validated['nim'] ?? null,
                    'jurusan_id' => $jurusan->id,
                    'jurusan' => $jurusan->nama,
                    'semester' => $validated['semester'] ?? null,
                ]
            );
        } catch (Throwable $exception) {
            return back()
                ->withErrors(['email' => $exception->getMessage()])
                ->withInput();
        }

        $user = $signup['user'] ?? (isset($signup['id']) ? $signup : null);

        if (!$user || empty($user['id'])) {
            return back()
                ->withErrors(['email' => 'Akun tidak dapat dibuat. Silakan coba lagi.'])
                ->withInput();
        }

        DB::table('profiles')->upsert([
            [
                'id' => $user['id'],
                'nama' => $validated['nama'],
                'nim' => $validated['nim'] ?? null,
                'jurusan_id' => $jurusan->id,
                'jurusan' => $jurusan->nama,
                'semester' => $validated['semester'] ?? null,
                'role' => 'mahasiswa',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], ['id'], ['nama', 'nim', 'jurusan_id', 'jurusan', 'semester', 'updated_at']);

        $accessToken = $signup['access_token'] ?? data_get($signup, 'session.access_token');
        $refreshToken = $signup['refresh_token'] ?? data_get($signup, 'session.refresh_token');

        if (!$accessToken) {
            return redirect()
                ->route('login')
                ->with('success', 'Akun berhasil dibuat. Periksa email konfirmasi, lalu silakan login.');
        }

        $request->session()->regenerate();
        $request->session()->put('supabase_user', [
            'id' => $user['id'],
            'email' => $user['email'] ?? $validated['email'],
            'role' => 'mahasiswa',
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Akun berhasil dibuat.');
    }

    public function login(Request $request, SupabaseAuthService $auth): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $login = $auth->signIn($validated['email'], $validated['password']);
        } catch (Throwable $exception) {
            return back()
                ->withErrors(['email' => $exception->getMessage()])
                ->withInput();
        }

        $user = $login['user'] ?? null;

        if (!$user || empty($user['id'])) {
            return back()
                ->withErrors(['email' => 'Data pengguna tidak ditemukan.'])
                ->withInput();
        }

        $role = DB::table('profiles')
            ->where('id', $user['id'])
            ->value('role') ?? 'mahasiswa';

        $request->session()->regenerate();
        $request->session()->put('supabase_user', [
            'id' => $user['id'],
            'email' => $user['email'] ?? $validated['email'],
            'role' => $role,
            'access_token' => $login['access_token'] ?? null,
            'refresh_token' => $login['refresh_token'] ?? null,
        ]);

        return $role === 'admin'
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard');
    }

    public function logout(Request $request, SupabaseAuthService $auth): RedirectResponse
    {
        try {
            $auth->signOut($request->session()->get('supabase_user.access_token'));
        } catch (Throwable $exception) {
            // Tetap hapus sesi lokal apabila token Supabase sudah tidak valid.
        }

        $request->session()->forget('supabase_user');
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil logout.');
    }

    public function showForgotPassword(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request, SupabaseAuthService $auth): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $auth->resetPassword($request->email);
        } catch (Throwable $exception) {
            return back()
                ->withErrors(['email' => $exception->getMessage()])
                ->withInput();
        }

        return back()->with('success', 'Link reset kata sandi telah dikirim ke email Anda.');
    }
}
