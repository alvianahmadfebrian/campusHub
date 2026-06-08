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

        abort_unless($userId, 401);

        $profile = DB::table('profiles as p')
            ->leftJoin('jurusan as j', 'j.id', '=', 'p.jurusan_id')
            ->where('p.id', $userId)
            ->select(
                'p.*',
                DB::raw('coalesce(j.nama, p.jurusan) as jurusan_nama')
            )
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
                ->get([
                    'id',
                    'nama',
                    'kode',
                    'aktif',
                ]),
        ]);
    }

    public function update(Request $request, SupabaseStorage $storage): RedirectResponse
    {
        $userId = $request->session()->get('supabase_user.id');

        abort_unless($userId, 401);

        $currentProfile = DB::table('profiles')
            ->where('id', $userId)
            ->first();

        $jurusanWajib = $currentProfile?->role !== 'admin';

        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:120',
            ],

            'nim' => [
                'nullable',
                'string',
                'max:50',
            ],

            'jurusan_id' => [
                $jurusanWajib ? 'required' : 'nullable',
                'nullable',
                'uuid',
                Rule::exists('jurusan', 'id')->where(
                    fn ($query) => $query->where('aktif', true)
                ),
            ],

            'semester' => [
                'nullable',
                'integer',
                'min:1',
                'max:14',
            ],

            'email' => [
                'nullable',
                'email',
                'max:150',
            ],

            'no_telfon' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9]*$/',
            ],

            'alamat' => [
                'nullable',
                'string',
                'max:500',
            ],

            'avatar' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ], [
            'no_telfon.regex' => 'Nomor telepon hanya boleh berisi angka.',
        ]);

        $jurusan = filled($validated['jurusan_id'] ?? null)
            ? DB::table('jurusan')
                ->where('id', $validated['jurusan_id'])
                ->where('aktif', true)
                ->first()
            : null;

        if ($jurusanWajib && !$jurusan) {
            return back()
                ->withErrors([
                    'jurusan_id' => 'Jurusan tidak tersedia.',
                ])
                ->withInput();
        }

        $avatarUrl = $currentProfile?->avatar_url;

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
                ->withErrors([
                    'avatar' => 'Upload foto gagal: ' . $exception->getMessage(),
                ])
                ->withInput();
        }

        DB::table('profiles')->updateOrInsert(
            ['id' => $userId],
            [
                'nama' => $validated['nama'],
                'nim' => $validated['nim'] ?? null,
                'email' => $validated['email'] ?? null,
                'no_telfon' => $validated['no_telfon'] ?? null,
                'alamat' => $validated['alamat'] ?? null,
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
