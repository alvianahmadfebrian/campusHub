<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PengumumanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Pengumuman/Index', [
            'items' => DB::table('pengumuman')->latest('created_at')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'isi' => ['required', 'string'],
            'kategori' => ['nullable', 'string', 'max:80'],
        ]);

        DB::table('pengumuman')->insert([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'kategori' => $validated['kategori'] ?? null,
            'created_by' => $request->session()->get('supabase_user.id'),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }
}
