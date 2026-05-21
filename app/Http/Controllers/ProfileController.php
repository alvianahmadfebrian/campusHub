<?php

namespace App\Http\Controllers;

use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $userId = $request->session()->get('supabase_user.id');

        return Inertia::render('Profile/Edit', [
            'profile' => DB::table('profiles')->where('id', $userId)->first(),
        ]);
    }

    public function update(Request $request, SupabaseStorage $storage): RedirectResponse
    {
        $userId = $request->session()->get('supabase_user.id');

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:120'],
            'nim' => ['nullable', 'string', 'max:50'],
            'jurusan' => ['nullable', 'string', 'max:120'],
            'semester' => ['nullable', 'integer', 'min:1', 'max:14'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarUrl = DB::table('profiles')->where('id', $userId)->value('avatar_url');
        if ($request->hasFile('avatar')) {
            $avatarUrl = $storage->upload(env('SUPABASE_BUCKET_PROFILE', 'profile-photos'), $request->file('avatar'), 'profiles/' . $userId);
        }

        DB::table('profiles')->updateOrInsert(
            ['id' => $userId],
            [
                'nama' => $validated['nama'],
                'nim' => $validated['nim'] ?? null,
                'jurusan' => $validated['jurusan'] ?? null,
                'semester' => $validated['semester'] ?? null,
                'avatar_url' => $avatarUrl,
            ]
        );

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
