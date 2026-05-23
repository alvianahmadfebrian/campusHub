# CampusHub — Laravel, Inertia Vue, Supabase

Project ini sudah disusun untuk memiliki dua jenis pengguna:

- **Mahasiswa**: register/login, melihat pengumuman, materi dan event, serta mengedit profil.
- **Admin**: login ke dashboard admin dan menambahkan pengumuman, mengunggah materi, serta membuat event.

## URL Aplikasi

| Halaman | URL |
| --- | --- |
| Login | `http://127.0.0.1:8000/login` |
| Register | `http://127.0.0.1:8000/register` |
| Dashboard mahasiswa | `http://127.0.0.1:8000/dashboard` |
| Dashboard admin | `http://127.0.0.1:8000/admin/dashboard` |
| Pengumuman | `http://127.0.0.1:8000/pengumuman` |
| Materi | `http://127.0.0.1:8000/materi` |
| Event | `http://127.0.0.1:8000/events` |
| Profil | `http://127.0.0.1:8000/profile` |

## 1. Install Project

Buka terminal dari folder project:

```bash
composer install
npm install
copy .env.example .env
php artisan key:generate
```

Pada macOS/Linux, gunakan `cp .env.example .env`.

## 2. Isi `.env`

Isi nilai berikut menggunakan data project Supabase kamu:

```env
DB_URL=postgres://postgres.PROJECT_REF:[YOUR-PASSWORD]@aws-0-REGION.pooler.supabase.com:5432/postgres

SUPABASE_URL=https://PROJECT_REF.supabase.co
SUPABASE_ANON_KEY=ISI_ANON_PUBLIC_KEY

SUPABASE_STORAGE_KEY_ID=ISI_ACCESS_KEY_ID
SUPABASE_STORAGE_SECRET_ACCESS_KEY=ISI_SECRET_ACCESS_KEY
SUPABASE_STORAGE_REGION=ap-south-1
SUPABASE_STORAGE_ENDPOINT=https://PROJECT_REF.storage.supabase.co/storage/v1/s3

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
```

## 3. Setup Database dan Storage Supabase

Buka **Supabase > SQL Editor**, lalu jalankan isi file:

```text
database/sql/campushub_supabase_schema.sql
```

Aplikasi menggunakan session file lokal, sehingga login tidak membutuhkan tabel session Laravel tambahan.

Script tersebut:

- membuat/melengkapi tabel aplikasi;
- membuat profil otomatis saat user register;
- mengatur role default `mahasiswa`;
- mengaktifkan kebijakan profil;
- membuat bucket public untuk foto profil, materi, dan gambar event.

Karena aplikasi mengunggah file melalui S3 Supabase, buat juga S3 Access Key pada halaman Storage Supabase lalu masukkan key tersebut ke `.env`.

## 4. Membuat Admin

Register akun terlebih dahulu melalui aplikasi. Setelah user tampil pada tabel `profiles`, buka file:

```text
database/sql/set_admin.sql
```

Ganti `EMAIL_ADMIN_ANDA`, lalu jalankan pada Supabase SQL Editor. Logout lalu login kembali; akun tersebut akan diarahkan ke dashboard admin.

## 5. Jalankan Aplikasi

Terminal pertama:

```bash
php artisan optimize:clear
php artisan serve
```

Terminal kedua:

```bash
npm run dev
```

Buka:

```text
http://127.0.0.1:8000/login
```

## Route Penting

Cek bahwa route login telah aktif:

```bash
php artisan route:list --path=login
```

Hasilnya harus memuat `GET|HEAD /login` dan `POST /login`.

## Struktur Utama yang Telah Ditambahkan

```text
app/Http/Middleware/SupabaseAdminMiddleware.php
resources/js/Pages/Admin/Dashboard.vue
database/sql/set_admin.sql
```

Akses penambahan pengumuman, materi, dan event kini hanya berada di `/admin/dashboard` serta dilindungi middleware `supabase.admin`.
