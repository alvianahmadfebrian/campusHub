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

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $profile = DB::table('profiles')
            ->where('id', $request->session()->get('supabase_user.id'))
            ->first();

        $query = DB::table('events as e')
            ->leftJoin('jurusan as j', 'j.id', '=', 'e.jurusan_id')
            ->select('e.*', 'j.nama as target_jurusan');

        if ($profile?->role !== 'admin') {
            $this->onlyVisibleForMahasiswa($query, $profile?->jurusan_id);
        }

        return Inertia::render('Events/Index', [
            'items' => $query->orderBy('e.tanggal')->get(),
        ]);
    }

    public function store(Request $request, SupabaseStorage $storage): RedirectResponse
    {
        $validated = $request->validate([
            'nama_event' => ['required', 'string', 'max:180'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
            'lokasi' => ['nullable', 'string', 'max:180'],
            'link_pendaftaran' => ['nullable', 'url', 'max:255'],
            'jurusan_id' => ['nullable', 'uuid', Rule::exists('jurusan', 'id')->where(fn ($query) => $query->where('aktif', true))],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $gambarUrl = null;

        try {
            if ($request->hasFile('gambar')) {
                $gambarUrl = $storage->upload(
                    (string) config('services.supabase.storage.buckets.event', 'event-images'),
                    $request->file('gambar'),
                    'events'
                );
            }
        } catch (Throwable $exception) {
            return back()
                ->withErrors(['gambar' => 'Upload gambar gagal: ' . $exception->getMessage()])
                ->withInput();
        }

        DB::table('events')->insert([
            'nama_event' => $validated['nama_event'],
            'deskripsi' => $validated['deskripsi'] ?? null,
            'tanggal' => $validated['tanggal'],
            'lokasi' => $validated['lokasi'] ?? null,
            'jurusan_id' => ($validated['jurusan_id'] ?? null) ?: null,
            'gambar_url' => $gambarUrl,
            'link_pendaftaran' => $validated['link_pendaftaran'] ?? null,
            'created_at' => now(),
        ]);

        return back()->with('success', 'Event berhasil ditambahkan.');
    }

    private function onlyVisibleForMahasiswa(Builder $query, ?string $jurusanId): void
    {
        $query->where(function (Builder $builder) use ($jurusanId): void {
            $builder->whereNull('e.jurusan_id');

            if ($jurusanId) {
                $builder->orWhere('e.jurusan_id', $jurusanId);
            }
        });
    }
}
