<?php

namespace App\Http\Controllers;

use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $userId = $request->session()->get('supabase_user.id');

        $profile = DB::table('profiles as p')
            ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
            ->where('p.id', $userId)
            ->select('p.*', DB::raw('coalesce(j.nama, p.jurusan) as jurusan_nama'))
            ->first();

        return Inertia::render('Profile/Edit', [
            'profile' => $profile,
            'jurusan' => DB::table('jurusan')
                ->where(function ($query) use ($profile): void {
                    $query->where('aktif', true);

                    if ($profile?->jurusan_id) {
                        $query->orWhere('id', $profile->jurusan_id);
                    }
                })
                ->orderBy('nama')
                ->get(['id', 'nama', 'kode', 'aktif']),
        ]);
    }

    public function update(Request $request, SupabaseStorage $storage): RedirectResponse
    {
        $userId = $request->session()->get('supabase_user.id');

        $currentProfile = DB::table('profiles')->where('id', $userId)->first();
        $jurusanWajib = $currentProfile?->role !== 'admin';

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'nim' => ['nullable', 'string', 'max:50'],
            'jurusan_id' => [
                $jurusanWajib ? 'required' : 'nullable',
                'uuid',
                Rule::exists('jurusan', 'id')->where(fn ($query) => $query->where('aktif', true)),
            ],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $jurusan = filled($validated['jurusan_id'] ?? null)
            ? DB::table('jurusan')->where('id', $validated['jurusan_id'])->where('aktif', true)->first()
            : null;

        if ($jurusanWajib && !$jurusan) {
            return back()
                ->withErrors(['jurusan_id' => 'Jurusan tidak tersedia.'])
                ->withInput();
        }

        $avatarUrl = DB::table('profiles')->where('id', $userId)->value('avatar_url');

        try {
            if ($request->hasFile('avatar')) {
                $avatarUrl = $storage->upload(
                    (string) config('services.supabase.storage.buckets.profile', 'profile-photos'),
                    $request->file('avatar'),
                    'profiles/' . $userId
                );
            }
        } catch (Throwable $exception) {
            return back()
                ->withErrors(['avatar' => 'Upload foto gagal: ' . $exception->getMessage()])
                ->withInput();
        }

        DB::table('profiles')->updateOrInsert(
            ['id' => $userId],
            [
                'nama' => $validated['nama'],
                'nim' => $validated['nim'] ?? null,
                'jurusan_id' => $jurusan?->id,
                'jurusan' => $jurusan?->nama,
                'semester' => $validated['semester'] ?? null,
                'avatar_url' => $avatarUrl,
                'updated_at' => now(),
            ]
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
