<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class JurusanController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120', Rule::unique('jurusan', 'nama')],
            'kode' => ['nullable', 'string', 'max:20', Rule::unique('jurusan', 'kode')],
        ]);

        DB::table('jurusan')->insert([
            'nama' => $validated['nama'],
            'kode' => filled($validated['kode'] ?? null) ? strtoupper($validated['kode']) : null,
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function toggle(string $id): RedirectResponse
    {
        $jurusan = DB::table('jurusan')->where('id', $id)->first();

        if (!$jurusan) {
            abort(404, 'Jurusan tidak ditemukan.');
        }

        DB::table('jurusan')
            ->where('id', $id)
            ->update([
                'aktif' => !$jurusan->aktif,
                'updated_at' => now(),
            ]);

        return back()->with(
            'success',
            $jurusan->aktif ? 'Jurusan dinonaktifkan.' : 'Jurusan diaktifkan.'
        );
    }
}
