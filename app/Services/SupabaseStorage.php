<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class SupabaseStorage
{
    public function upload(string $bucket, UploadedFile $file, string $folder = ''): string
    {
        $endpoint = (string) config('services.supabase.storage.endpoint');
        $url = rtrim((string) config('services.supabase.url'), '/');

        if ($endpoint === '' || $url === '') {
            throw new RuntimeException('Konfigurasi Supabase Storage belum lengkap pada file .env.');
        }

        $disk = Storage::build([
            'driver' => 's3',
            'key' => config('services.supabase.storage.key_id'),
            'secret' => config('services.supabase.storage.secret_key'),
            'region' => config('services.supabase.storage.region', 'ap-south-1'),
            'bucket' => $bucket,
            'endpoint' => $endpoint,
            'use_path_style_endpoint' => true,
            'throw' => true,
        ]);

        $extension = strtolower($file->getClientOriginalExtension());
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';
        $filename = now()->format('YmdHis') . '_' . $safeName . '_' . Str::random(8) . '.' . $extension;
        $path = trim($folder, '/');
        $fullPath = $path ? $path . '/' . $filename : $filename;

        $disk->put($fullPath, file_get_contents($file->getRealPath()), [
            'ContentType' => $file->getMimeType(),
        ]);

        return $url . '/storage/v1/object/public/' . $bucket . '/' . $fullPath;
    }
}
