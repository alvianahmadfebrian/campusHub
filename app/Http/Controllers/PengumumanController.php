<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PengumumanController extends Controller
{
    public function index(Request $request): Response
    {
        $profile = DB::table('profiles')
            ->where('id', $request->session()->get('supabase_user.id'))
            ->first();

        $query = DB::table('pengumuman as p')
            ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
            ->select('p.*', 'j.nama as target_jurusan');

        if ($profile?->role !== 'admin') {
            $this->onlyVisibleForMahasiswa($query, $profile?->jurusan_id);
        }

        return Inertia::render('Pengumuman/Index', [
            'items' => $query->orderByDesc('p.created_at')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'isi' => ['required', 'string'],
            'kategori' => ['nullable', 'string', 'max:80'],
            'jurusan_id' => ['nullable', 'uuid', Rule::exists('jurusan', 'id')->where(fn ($query) => $query->where('aktif', true))],
        ]);

        DB::table('pengumuman')->insert([
            'judul' => $validated['judul'],
            'isi' => $validated['isi'],
            'kategori' => ($validated['kategori'] ?? null) ?: 'Umum',
            'jurusan_id' => ($validated['jurusan_id'] ?? null) ?: null,
            'created_by' => $request->session()->get('supabase_user.id'),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    private function onlyVisibleForMahasiswa(Builder $query, ?string $jurusanId): void
    {
        $query->where(function (Builder $builder) use ($jurusanId): void {
            $builder->whereNull('p.jurusan_id');

            if ($jurusanId) {
                $builder->orWhere('p.jurusan_id', $jurusanId);
            }
        });
    }
}
