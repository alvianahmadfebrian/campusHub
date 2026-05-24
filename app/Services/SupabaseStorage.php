<?php

namespace App\Services;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class SupabaseStorage
{
    public function upload(string $bucket, UploadedFile $file, string $folder = ''): string
    {
        $url = rtrim((string) config('services.supabase.url'), '/');

        if ($url === '') {
            throw new RuntimeException('Konfigurasi SUPABASE_URL belum tersedia pada file .env.');
        }

        $path = $this->put($bucket, $file, $folder);

        return $url . '/storage/v1/object/public/' . $bucket . '/' . $path;
    }

    /**
     * Upload file ke bucket dan kembalikan path object tanpa URL publik.
     * Digunakan Drive karena bucket harus bersifat private.
     */
    public function put(string $bucket, UploadedFile $file, string $folder = ''): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';
        $suffix = $extension !== '' ? '.' . $extension : '';
        $filename = now()->format('YmdHis') . '_' . $safeName . '_' . Str::random(10) . $suffix;
        $path = trim($folder, '/');
        $fullPath = $path !== '' ? $path . '/' . $filename : $filename;

        $this->disk($bucket)->put($fullPath, file_get_contents($file->getRealPath()), [
            'ContentType' => $file->getMimeType() ?: 'application/octet-stream',
        ]);

        return $fullPath;
    }

    public function readStream(string $bucket, string $path)
    {
        $stream = $this->disk($bucket)->readStream($path);

        if ($stream === false) {
            throw new RuntimeException('File tidak dapat dibaca dari Supabase Storage.');
        }

        return $stream;
    }

    public function delete(string $bucket, string $path): void
    {
        if ($path !== '') {
            $this->disk($bucket)->delete($path);
        }
    }

    private function disk(string $bucket): FilesystemAdapter
    {
        $endpoint = (string) config('services.supabase.storage.endpoint');

        if ($endpoint === '') {
            throw new RuntimeException('Konfigurasi Supabase Storage belum lengkap pada file .env.');
        }

        return Storage::build([
            'driver' => 's3',
            'key' => config('services.supabase.storage.key_id'),
            'secret' => config('services.supabase.storage.secret_key'),
            'region' => config('services.supabase.storage.region', 'ap-south-1'),
            'bucket' => $bucket,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'throw' => true,
        ]);
    }
}
