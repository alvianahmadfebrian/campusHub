<?php

namespace App\Http\Controllers;

use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class MateriController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Materi/Index', [
            'items' => DB::table('materi')->latest('created_at')->get(),
        ]);
    }

    public function store(Request $request, SupabaseStorage $storage): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'mata_kuliah' => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:20480'],
        ]);

        $fileUrl = $storage->upload(env('SUPABASE_BUCKET_MATERI', 'materi-files'), $request->file('file'), 'materi');

        DB::table('materi')->insert([
            'judul' => $validated['judul'],
            'mata_kuliah' => $validated['mata_kuliah'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'file_url' => $fileUrl,
            'uploaded_by' => $request->session()->get('supabase_user.id'),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Materi berhasil diupload.');
    }
}
