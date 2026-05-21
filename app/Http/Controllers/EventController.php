<?php

namespace App\Http\Controllers;

use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Events/Index', [
            'items' => DB::table('events')->latest('created_at')->get(),
        ]);
    }

    public function store(Request $request, SupabaseStorage $storage): RedirectResponse
    {
        $validated = $request->validate([
            'nama_event' => ['required', 'string', 'max:180'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal' => ['nullable', 'date'],
            'lokasi' => ['nullable', 'string', 'max:180'],
            'link_pendaftaran' => ['nullable', 'url', 'max:255'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $gambarUrl = null;
        if ($request->hasFile('gambar')) {
            $gambarUrl = $storage->upload(env('SUPABASE_BUCKET_EVENT', 'event-images'), $request->file('gambar'), 'events');
        }

        DB::table('events')->insert([
            'nama_event' => $validated['nama_event'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal' => $validated['tanggal'] ?? null,
            'lokasi' => $validated['lokasi'] ?? null,
            'gambar_url' => $gambarUrl,
            'link_pendaftaran' => $validated['link_pendaftaran'] ?? null,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Event berhasil ditambahkan.');
    }
}
