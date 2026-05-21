<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SupabaseStorage
{
    public function upload(string $bucket, UploadedFile $file, string $folder = ''): string
    {
        $disk = Storage::build([
            'driver' => 's3',
            'key' => env('SUPABASE_STORAGE_KEY_ID'),
            'secret' => env('SUPABASE_STORAGE_SECRET_ACCESS_KEY'),
            'region' => env('SUPABASE_STORAGE_REGION', 'ap-south-1'),
            'bucket' => $bucket,
            'endpoint' => env('SUPABASE_STORAGE_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'throw' => true,
        ]);

        $extension = $file->getClientOriginalExtension();
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = now()->format('YmdHis') . '_' . $safeName . '_' . Str::random(8) . '.' . $extension;
        $path = trim($folder, '/');
        $fullPath = $path ? $path . '/' . $filename : $filename;

        $disk->put($fullPath, file_get_contents($file->getRealPath()), [
            'ContentType' => $file->getMimeType(),
        ]);

        return rtrim(env('SUPABASE_URL'), '/') . '/storage/v1/object/public/' . $bucket . '/' . $fullPath;
    }
}
