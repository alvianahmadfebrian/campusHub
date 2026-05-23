<?php

namespace App\Http\Controllers;

use App\Services\SupabaseStorage;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class MateriController extends Controller
{
    public function index(Request $request): Response
    {
        $profile = DB::table('profiles')
            ->where('id', $request->session()->get('supabase_user.id'))
            ->first();

        $query = DB::table('materi as m')
            ->leftJoin('jurusan as j', 'j.id', '=', 'm.jurusan_id')
            ->select('m.*', 'j.nama as target_jurusan');

        if ($profile?->role !== 'admin') {
            $this->onlyVisibleForMahasiswa($query, $profile?->jurusan_id);
        }

        return Inertia::render('Materi/Index', [
            'items' => $query->orderByDesc('m.created_at')->get(),
        ]);
    }

    public function store(Request $request, SupabaseStorage $storage): RedirectResponse
    {
        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:180'],
            'mata_kuliah' => ['required', 'string', 'max:120'],
            'deskripsi' => ['nullable', 'string'],
            'jurusan_id' => ['nullable', 'uuid', Rule::exists('jurusan', 'id')->where(fn ($query) => $query->where('aktif', true))],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,ppt,pptx', 'max:20480'],
        ]);

        try {
            $fileUrl = $storage->upload(
                (string) config('services.supabase.storage.buckets.materi', 'materi-files'),
                $request->file('file'),
                'materi'
            );
        } catch (Throwable $exception) {
            return back()
                ->withErrors(['file' => 'Upload materi gagal: ' . $exception->getMessage()])
                ->withInput();
        }

        DB::table('materi')->insert([
            'judul' => $validated['judul'],
            'mata_kuliah' => $validated['mata_kuliah'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'jurusan_id' => ($validated['jurusan_id'] ?? null) ?: null,
            'file_url' => $fileUrl,
            'uploaded_by' => $request->session()->get('supabase_user.id'),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Materi berhasil diunggah.');
    }

    private function onlyVisibleForMahasiswa(Builder $query, ?string $jurusanId): void
    {
        $query->where(function (Builder $builder) use ($jurusanId): void {
            $builder->whereNull('m.jurusan_id');

            if ($jurusanId) {
                $builder->orWhere('m.jurusan_id', $jurusanId);
            }
        });
    }
}
