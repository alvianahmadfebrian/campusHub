# CampusHub Laravel + Inertia + Vue tanpa Breeze

Starter ini memakai:

- Laravel sebagai backend
- Inertia.js sebagai penghubung Laravel dan Vue
- Vue sebagai frontend
- Supabase Auth untuk login/register
- Supabase PostgreSQL untuk database
- Supabase Storage S3 untuk upload foto/file

## 1. Buat project Laravel baru

```bash
composer create-project laravel/laravel campushub
cd campushub
```

## 2. Install dependency tanpa Breeze

```bash
composer require inertiajs/inertia-laravel league/flysystem-aws-s3-v3
npm install @inertiajs/vue3 vue @vitejs/plugin-vue
```

Kalau `laravel-vite-plugin` belum ada:

```bash
npm install laravel-vite-plugin vite
```

## 3. Copy file starter

Copy semua isi ZIP ini ke folder project `campushub`.

## 4. Edit `bootstrap/app.php`

Tambahkan middleware Inertia dan alias middleware auth custom.

Contoh struktur yang perlu ada:

```php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SupabaseAuthMiddleware;
use App\Http\Middleware\RedirectIfSupabaseAuthenticated;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'supabase.auth' => SupabaseAuthMiddleware::class,
            'guest.supabase' => RedirectIfSupabaseAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

## 5. Isi `.env`

Copy `.env.example` ke `.env`, lalu isi Supabase URL, anon key, DB URL, dan Storage S3 keys.

```bash
cp .env.example .env
php artisan key:generate
```

## 6. Buat table session Laravel

Karena `SESSION_DRIVER=database`, jalankan:

```bash
php artisan session:table
php artisan migrate
```

## 7. Pastikan Supabase sudah punya tabel

Kalau belum, jalankan SQL di:

`database/sql/campushub_supabase_schema.sql`

Lewat Supabase SQL Editor.

## 8. Jalankan aplikasi

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Buka:

```text
http://127.0.0.1:8000
```

## Catatan

- Jangan share `SUPABASE_STORAGE_SECRET_ACCESS_KEY`, `service_role key`, dan database password.
- Pastikan Supabase Authentication Email provider aktif.
- Untuk demo, matikan email confirmation supaya register langsung bisa login.
- Pastikan bucket `profile-photos`, `materi-files`, dan `event-images` sudah dibuat dan public.
