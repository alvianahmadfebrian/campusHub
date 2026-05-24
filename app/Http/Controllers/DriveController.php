<?php

namespace App\Http\Controllers;

use App\Services\SupabaseStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DriveController extends Controller
{
    public function index(Request $request, ?string $folderId = null): Response
    {
        $userId = $this->userId($request);

        $viewerRole = DB::table('profiles')
            ->where('id', $userId)
            ->value('role') ?? 'mahasiswa';

        $folder = null;

        if ($folderId) {
            $folder = $this->ownedFolder($folderId, $userId);
        }

        $folders = DB::table('drive_folders')
            ->where('owner_id', $userId)
            ->where('parent_id', $folder?->id)
            ->orderBy('nama')
            ->get()
            ->map(fn ($folder) => $this->folderResource($folder))
            ->values();

        $files = DB::table('drive_files')
            ->where('owner_id', $userId)
            ->where('folder_id', $folder?->id)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($file) => $this->fileResource($file))
            ->values();

        return Inertia::render('Drive/Index', [
            'viewerRole' => $viewerRole,

            'currentFolder' => $folder
                ? $this->folderResource($folder)
                : null,

            'breadcrumbs' => $folder
                ? $this->breadcrumbs($folder, $userId)
                : [],

            'folders' => $folders,
            'files' => $files,

            'limits' => [
                'maxUploadMb' => 50,
            ],
        ]);
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $userId = $this->userId($request);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'parent_id' => ['nullable', 'uuid'],
        ]);

        $parentId = ($validated['parent_id'] ?? null) ?: null;

        if ($parentId) {
            $this->ownedFolder($parentId, $userId);
        }

        DB::table('drive_folders')->insert([
            'id' => (string) Str::uuid(),
            'owner_id' => $userId,
            'parent_id' => $parentId,
            'nama' => trim($validated['nama']),
            'is_public' => false,
            'share_token' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Folder berhasil dibuat.');
    }

    public function updateFolder(Request $request, string $id): RedirectResponse
    {
        $userId = $this->userId($request);
        $folder = $this->ownedFolder($id, $userId);

        $validated = $request->validate([
            'nama' => ['sometimes', 'required', 'string', 'max:150'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $update = [
            'updated_at' => now(),
        ];

        if (array_key_exists('nama', $validated)) {
            $update['nama'] = trim($validated['nama']);
        }

        if (array_key_exists('is_public', $validated)) {
            $update['is_public'] = (bool) $validated['is_public'];
        }

        DB::table('drive_folders')
            ->where('id', $folder->id)
            ->where('owner_id', $userId)
            ->update($update);

        return back()->with('success', 'Folder berhasil diperbarui.');
    }

    public function destroyFolder(
        Request $request,
        string $id,
        SupabaseStorage $storage
    ): RedirectResponse {
        $userId = $this->userId($request);
        $folder = $this->ownedFolder($id, $userId);

        $folderIds = $this->descendantFolderIds($folder->id, $userId);
        $bucket = $this->driveBucket();

        $files = DB::table('drive_files')
            ->where('owner_id', $userId)
            ->whereIn('folder_id', $folderIds)
            ->get(['id', 'storage_path']);

        try {
            foreach ($files as $file) {
                $storage->delete($bucket, $file->storage_path);
            }
        } catch (Throwable $exception) {
            return back()->withErrors([
                'drive' => 'Folder tidak dapat dihapus karena file storage gagal dihapus: ' . $exception->getMessage(),
            ]);
        }

        DB::transaction(function () use ($userId, $folderIds): void {
            DB::table('drive_files')
                ->where('owner_id', $userId)
                ->whereIn('folder_id', $folderIds)
                ->delete();

            DB::table('drive_folders')
                ->where('owner_id', $userId)
                ->whereIn('id', $folderIds)
                ->delete();
        });

        return redirect()
            ->route('drive.index', $folder->parent_id ? ['folderId' => $folder->parent_id] : [])
            ->with('success', 'Folder dan seluruh isinya berhasil dihapus.');
    }

    public function storeFile(
        Request $request,
        SupabaseStorage $storage
    ): RedirectResponse {
        $userId = $this->userId($request);

        $validated = $request->validate([
            'folder_id' => ['nullable', 'uuid'],
            'file' => ['required', 'file', 'max:51200'],
        ]);

        $folderId = ($validated['folder_id'] ?? null) ?: null;

        if ($folderId) {
            $this->ownedFolder($folderId, $userId);
        }

        $uploadedFile = $request->file('file');

        $storageFolder = 'drive/' . $userId . '/'
            . ($folderId ?: 'root');

        try {
            $path = $storage->put(
                $this->driveBucket(),
                $uploadedFile,
                $storageFolder
            );
        } catch (Throwable $exception) {
            return back()->withErrors([
                'file' => 'Upload file gagal: ' . $exception->getMessage(),
            ]);
        }

        DB::table('drive_files')->insert([
            'id' => (string) Str::uuid(),
            'folder_id' => $folderId,
            'owner_id' => $userId,
            'nama_asli' => $uploadedFile->getClientOriginalName(),
            'nama_tampilan' => $uploadedFile->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $uploadedFile->getMimeType() ?: 'application/octet-stream',
            'ukuran_bytes' => $uploadedFile->getSize() ?: 0,
            'is_public' => false,
            'share_token' => (string) Str::uuid(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'File berhasil diunggah ke Drive.');
    }

    public function updateFile(Request $request, string $id): RedirectResponse
    {
        $userId = $this->userId($request);
        $file = $this->ownedFile($id, $userId);

        $validated = $request->validate([
            'nama_tampilan' => ['sometimes', 'required', 'string', 'max:220'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $update = [
            'updated_at' => now(),
        ];

        if (array_key_exists('nama_tampilan', $validated)) {
            $update['nama_tampilan'] = trim($validated['nama_tampilan']);
        }

        if (array_key_exists('is_public', $validated)) {
            $update['is_public'] = (bool) $validated['is_public'];
        }

        DB::table('drive_files')
            ->where('id', $file->id)
            ->where('owner_id', $userId)
            ->update($update);

        return back()->with('success', 'File berhasil diperbarui.');
    }

    public function destroyFile(
        Request $request,
        string $id,
        SupabaseStorage $storage
    ): RedirectResponse {
        $userId = $this->userId($request);
        $file = $this->ownedFile($id, $userId);

        try {
            $storage->delete($this->driveBucket(), $file->storage_path);
        } catch (Throwable $exception) {
            return back()->withErrors([
                'drive' => 'File storage gagal dihapus: ' . $exception->getMessage(),
            ]);
        }

        DB::table('drive_files')
            ->where('id', $file->id)
            ->where('owner_id', $userId)
            ->delete();

        return back()->with('success', 'File berhasil dihapus.');
    }

    public function downloadFile(
        Request $request,
        string $id,
        SupabaseStorage $storage
    ): StreamedResponse {
        $file = $this->ownedFile($id, $this->userId($request));

        return $this->serveFile($file, $storage, false);
    }

    /**
     * Folder public dapat dibuka tanpa session/login.
     * Jika folder root public, semua isi turunannya juga dapat dibuka via link folder.
     */
    public function publicFolder(string $token, ?string $folderId = null): Response
    {
        $root = DB::table('drive_folders')
            ->where('share_token', $token)
            ->where('is_public', true)
            ->first();

        abort_unless($root, 404);

        $current = $folderId
            ? DB::table('drive_folders')
                ->where('id', $folderId)
                ->where('owner_id', $root->owner_id)
                ->first()
            : $root;

        abort_unless(
            $current && $this->isFolderInside($root->id, $current->id, $root->owner_id),
            404
        );

        $folders = DB::table('drive_folders')
            ->where('owner_id', $root->owner_id)
            ->where('parent_id', $current->id)
            ->orderBy('nama')
            ->get()
            ->map(fn ($folder) => [
                'id' => $folder->id,
                'nama' => $folder->nama,
                'url' => route('share.folder', [
                    'token' => $token,
                    'folderId' => $folder->id,
                ]),
            ])
            ->values();

        $files = DB::table('drive_files')
            ->where('owner_id', $root->owner_id)
            ->where('folder_id', $current->id)
            ->orderBy('nama_tampilan')
            ->get()
            ->map(fn ($file) => [
                'id' => $file->id,
                'nama_tampilan' => $file->nama_tampilan,
                'mime_type' => $file->mime_type,
                'ukuran_bytes' => $file->ukuran_bytes,
                'url' => route('share.folder.file', [
                    'token' => $token,
                    'fileId' => $file->id,
                ]),
            ])
            ->values();

        return Inertia::render('Drive/PublicFolder', [
            'rootFolder' => [
                'nama' => $root->nama,
            ],

            'currentFolder' => [
                'id' => $current->id,
                'nama' => $current->nama,
            ],

            'breadcrumbs' => $this->publicBreadcrumbs($root, $current, $token),
            'folders' => $folders,
            'files' => $files,
        ]);
    }

    public function publicFolderFile(
        string $token,
        string $fileId,
        SupabaseStorage $storage
    ): StreamedResponse {
        $root = DB::table('drive_folders')
            ->where('share_token', $token)
            ->where('is_public', true)
            ->first();

        abort_unless($root, 404);

        $file = DB::table('drive_files')
            ->where('id', $fileId)
            ->where('owner_id', $root->owner_id)
            ->first();

        abort_unless(
            $file
                && $file->folder_id
                && $this->isFolderInside($root->id, $file->folder_id, $root->owner_id),
            404
        );

        return $this->serveFile($file, $storage, true);
    }

    public function publicFile(
        string $token,
        SupabaseStorage $storage
    ): StreamedResponse {
        $file = DB::table('drive_files')
            ->where('share_token', $token)
            ->where('is_public', true)
            ->first();

        abort_unless($file, 404);

        return $this->serveFile($file, $storage, true);
    }

    private function serveFile(
    object $file,
    SupabaseStorage $storage,
    bool $inline
): StreamedResponse {
    $stream = $storage->readStream(
        $this->driveBucket(),
        $file->storage_path
    );

    $filename = (string) (
        $file->nama_tampilan
        ?: $file->nama_asli
        ?: 'download'
    );

    /*
     * Header Content-Disposition tidak menerima slash/backslash
     * pada nama file.
     */
    $filename = str_replace(['/', '\\'], '-', $filename);

    /*
     * Fallback filename wajib ASCII dan tidak boleh memiliki karakter %.
     */
    $fallbackFilename = Str::ascii($filename);
    $fallbackFilename = str_replace('%', '_', $fallbackFilename);
    $fallbackFilename = trim($fallbackFilename) !== ''
        ? $fallbackFilename
        : 'download';

    $disposition = HeaderUtils::makeDisposition(
        $inline
            ? HeaderUtils::DISPOSITION_INLINE
            : HeaderUtils::DISPOSITION_ATTACHMENT,
        $filename,
        $fallbackFilename
    );

    return response()->stream(function () use ($stream): void {
        fpassthru($stream);

        if (is_resource($stream)) {
            fclose($stream);
        }
    }, 200, [
        'Content-Type' => $file->mime_type ?: 'application/octet-stream',
        'Content-Disposition' => $disposition,
        'Content-Length' => (string) ($file->ukuran_bytes ?? ''),
        'Cache-Control' => 'private, max-age=0, no-cache',
    ]);
}

    private function userId(Request $request): string
    {
        $userId = $request->session()->get('supabase_user.id');

        abort_unless($userId, 401);

        return (string) $userId;
    }

    private function ownedFolder(string $id, string $userId): object
    {
        $folder = DB::table('drive_folders')
            ->where('id', $id)
            ->where('owner_id', $userId)
            ->first();

        abort_unless($folder, 404);

        return $folder;
    }

    private function ownedFile(string $id, string $userId): object
    {
        $file = DB::table('drive_files')
            ->where('id', $id)
            ->where('owner_id', $userId)
            ->first();

        abort_unless($file, 404);

        return $file;
    }

    private function folderResource(object $folder): array
    {
        return [
            'id' => $folder->id,
            'nama' => $folder->nama,
            'parent_id' => $folder->parent_id,
            'is_public' => (bool) $folder->is_public,
            'open_url' => route('drive.index', [
                'folderId' => $folder->id,
            ]),
            'share_url' => $folder->is_public
                ? route('share.folder', [
                    'token' => $folder->share_token,
                ])
                : null,
        ];
    }

    private function fileResource(object $file): array
    {
        return [
            'id' => $file->id,
            'folder_id' => $file->folder_id,
            'nama_tampilan' => $file->nama_tampilan,
            'nama_asli' => $file->nama_asli,
            'mime_type' => $file->mime_type,
            'ukuran_bytes' => $file->ukuran_bytes,
            'is_public' => (bool) $file->is_public,
            'download_url' => route('drive.files.download', [
                'id' => $file->id,
            ]),
            'share_url' => $file->is_public
                ? route('share.file', [
                    'token' => $file->share_token,
                ])
                : null,
        ];
    }

    private function breadcrumbs(object $folder, string $userId): array
    {
        $result = [];
        $current = $folder;
        $guard = 0;

        while ($current && $guard < 100) {
            array_unshift($result, [
                'nama' => $current->nama,
                'url' => route('drive.index', [
                    'folderId' => $current->id,
                ]),
            ]);

            $current = $current->parent_id
                ? DB::table('drive_folders')
                    ->where('owner_id', $userId)
                    ->where('id', $current->parent_id)
                    ->first()
                : null;

            $guard++;
        }

        return $result;
    }

    private function descendantFolderIds(string $rootId, string $userId): array
    {
        $ids = [$rootId];
        $queue = [$rootId];
        $guard = 0;

        while ($queue !== [] && $guard < 100) {
            $children = DB::table('drive_folders')
                ->where('owner_id', $userId)
                ->whereIn('parent_id', $queue)
                ->pluck('id')
                ->all();

            $queue = array_values(array_diff($children, $ids));
            $ids = array_merge($ids, $queue);

            $guard++;
        }

        return array_values(array_unique($ids));
    }

    private function isFolderInside(
        string $rootId,
        string $folderId,
        string $ownerId
    ): bool {
        $currentId = $folderId;
        $guard = 0;

        while ($currentId && $guard < 100) {
            if ($currentId === $rootId) {
                return true;
            }

            $currentId = DB::table('drive_folders')
                ->where('owner_id', $ownerId)
                ->where('id', $currentId)
                ->value('parent_id');

            $guard++;
        }

        return false;
    }

    private function publicBreadcrumbs(
        object $root,
        object $current,
        string $token
    ): array {
        $result = [];
        $cursor = $current;
        $guard = 0;

        while ($cursor && $guard < 100) {
            array_unshift($result, [
                'nama' => $cursor->nama,
                'url' => $cursor->id === $root->id
                    ? route('share.folder', [
                        'token' => $token,
                    ])
                    : route('share.folder', [
                        'token' => $token,
                        'folderId' => $cursor->id,
                    ]),
            ]);

            if ($cursor->id === $root->id) {
                break;
            }

            $cursor = $cursor->parent_id
                ? DB::table('drive_folders')
                    ->where('owner_id', $root->owner_id)
                    ->where('id', $cursor->parent_id)
                    ->first()
                : null;

            $guard++;
        }

        return $result;
    }

    private function driveBucket(): string
    {
        return (string) config(
            'services.supabase.storage.buckets.drive',
            'drive-files'
        );
    }
}
