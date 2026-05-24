-- CampusHub - Supabase database setup lengkap
-- Jalankan pada Supabase SQL Editor untuk instalasi baru.
-- Untuk database yang sudah pernah memakai versi lama, jalankan upgrade_jurusan_target.sql.

create extension if not exists "pgcrypto";

-- =========================================================
-- MASTER JURUSAN
-- Admin mengelola daftar ini. Mahasiswa memilih jurusan aktif saat register.
-- =========================================================
create table if not exists public.jurusan (
  id uuid primary key default gen_random_uuid(),
  nama text not null unique,
  kode text unique,
  aktif boolean not null default true,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

-- =========================================================
-- TABEL PROFIL DAN AUTH
-- Kolom jurusan lama disimpan untuk kompatibilitas data lama.
-- Relasi utama baru adalah jurusan_id.
-- =========================================================
create table if not exists public.profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  nama text not null,
  nim text,
  jurusan text,
  jurusan_id uuid references public.jurusan(id) on delete set null,
  semester int4,
  role text not null default 'mahasiswa',
  avatar_url text,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

alter table public.profiles
  add column if not exists jurusan_id uuid references public.jurusan(id) on delete set null,
  add column if not exists avatar_url text,
  add column if not exists created_at timestamptz default now(),
  add column if not exists updated_at timestamptz default now();

update public.profiles
set role = 'mahasiswa'
where role is null;

alter table public.profiles
  alter column role set default 'mahasiswa',
  alter column role set not null,
  alter column created_at set default now(),
  alter column updated_at set default now();

alter table public.profiles drop constraint if exists profiles_role_check;
alter table public.profiles
  add constraint profiles_role_check check (role in ('mahasiswa', 'admin'));

alter table public.profiles drop constraint if exists profiles_semester_check;
alter table public.profiles
  add constraint profiles_semester_check check (semester is null or semester between 1 and 14);

-- Impor nilai jurusan teks lama ke master jurusan, kemudian pasangkan relasinya.
insert into public.jurusan (nama)
select distinct btrim(jurusan)
from public.profiles
where nullif(btrim(jurusan), '') is not null
on conflict (nama) do nothing;

update public.profiles as p
set jurusan_id = j.id,
    jurusan = j.nama
from public.jurusan as j
where p.jurusan_id is null
  and lower(btrim(p.jurusan)) = lower(btrim(j.nama));

-- Trigger: user baru otomatis memperoleh profil mahasiswa sesuai jurusan pilihan.
create or replace function public.handle_new_user()
returns trigger
language plpgsql
security definer set search_path = ''
as $$
declare
  selected_jurusan_id uuid;
  selected_jurusan_nama text;
  metadata_jurusan_id text;
begin
  metadata_jurusan_id := new.raw_user_meta_data ->> 'jurusan_id';

  if metadata_jurusan_id ~* '^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$' then
    select id, nama
      into selected_jurusan_id, selected_jurusan_nama
    from public.jurusan
    where id = metadata_jurusan_id::uuid
      and aktif = true;
  end if;

  insert into public.profiles (
    id, nama, nim, jurusan, jurusan_id, semester, role, created_at, updated_at
  )
  values (
    new.id,
    coalesce(nullif(new.raw_user_meta_data ->> 'nama', ''), split_part(new.email, '@', 1)),
    nullif(new.raw_user_meta_data ->> 'nim', ''),
    selected_jurusan_nama,
    selected_jurusan_id,
    case
      when (new.raw_user_meta_data ->> 'semester') ~ '^[0-9]+$'
        then (new.raw_user_meta_data ->> 'semester')::int4
      else null
    end,
    'mahasiswa',
    now(),
    now()
  )
  on conflict (id) do update set
    nama = excluded.nama,
    nim = excluded.nim,
    jurusan = excluded.jurusan,
    jurusan_id = excluded.jurusan_id,
    semester = excluded.semester,
    updated_at = now();

  return new;
end;
$$;

drop trigger if exists on_auth_user_created on auth.users;
create trigger on_auth_user_created
after insert on auth.users
for each row execute function public.handle_new_user();

create or replace function public.set_updated_at()
returns trigger
language plpgsql
as $$
begin
  new.updated_at = now();
  return new;
end;
$$;

drop trigger if exists profiles_set_updated_at on public.profiles;
create trigger profiles_set_updated_at
before update on public.profiles
for each row execute function public.set_updated_at();

drop trigger if exists jurusan_set_updated_at on public.jurusan;
create trigger jurusan_set_updated_at
before update on public.jurusan
for each row execute function public.set_updated_at();

-- =========================================================
-- TABEL KONTEN
-- jurusan_id NULL = terlihat oleh seluruh jurusan.
-- jurusan_id terisi = hanya mahasiswa pada jurusan tersebut.
-- =========================================================
create table if not exists public.pengumuman (
  id uuid primary key default gen_random_uuid(),
  judul text not null,
  isi text not null,
  kategori text,
  jurusan_id uuid references public.jurusan(id) on delete set null,
  created_by uuid references auth.users(id) on delete set null,
  created_at timestamptz default now()
);

alter table public.pengumuman
  add column if not exists jurusan_id uuid references public.jurusan(id) on delete set null;

create table if not exists public.materi (
  id uuid primary key default gen_random_uuid(),
  judul text not null,
  mata_kuliah text,
  deskripsi text,
  file_url text,
  jurusan_id uuid references public.jurusan(id) on delete set null,
  uploaded_by uuid references auth.users(id) on delete set null,
  created_at timestamptz default now()
);

alter table public.materi
  add column if not exists jurusan_id uuid references public.jurusan(id) on delete set null;

create table if not exists public.events (
  id uuid primary key default gen_random_uuid(),
  nama_event text not null,
  deskripsi text,
  tanggal date,
  lokasi text,
  gambar_url text,
  link_pendaftaran text,
  jurusan_id uuid references public.jurusan(id) on delete set null,
  created_at timestamptz default now()
);

alter table public.events
  add column if not exists jurusan_id uuid references public.jurusan(id) on delete set null;

create table if not exists public.jadwal (
  id uuid primary key default gen_random_uuid(),
  user_id uuid references auth.users(id) on delete cascade,
  mata_kuliah text not null,
  dosen text,
  hari text,
  jam_mulai time,
  jam_selesai time,
  ruangan text,
  created_at timestamptz default now()
);

create table if not exists public.forum_posts (
  id uuid primary key default gen_random_uuid(),
  user_id uuid references auth.users(id) on delete cascade,
  judul text not null,
  isi text not null,
  mata_kuliah text,
  created_at timestamptz default now()
);

create table if not exists public.forum_comments (
  id uuid primary key default gen_random_uuid(),
  post_id uuid references public.forum_posts(id) on delete cascade,
  user_id uuid references auth.users(id) on delete cascade,
  komentar text not null,
  created_at timestamptz default now()
);

create table if not exists public.absensi (
  id uuid primary key default gen_random_uuid(),
  user_id uuid references auth.users(id) on delete cascade,
  mata_kuliah text not null,
  tanggal date default current_date,
  status text default 'hadir',
  created_at timestamptz default now()
);

-- =========================================================
-- ROW LEVEL SECURITY UNTUK PROFILE
-- =========================================================
alter table public.profiles enable row level security;

drop policy if exists "profiles_select_sendiri" on public.profiles;
create policy "profiles_select_sendiri"
on public.profiles
for select
to authenticated
using ((select auth.uid()) = id);

drop policy if exists "profiles_update_sendiri" on public.profiles;
create policy "profiles_update_sendiri"
on public.profiles
for update
to authenticated
using ((select auth.uid()) = id)
with check ((select auth.uid()) = id);



-- =========================================================
-- DRIVE PRIBADI + PUBLIC SHARE LINK
-- Bucket drive-files tetap PRIVATE; aplikasi Laravel melayani tautan publik.
-- =========================================================
create table if not exists public.drive_folders (
  id uuid primary key default gen_random_uuid(),
  owner_id uuid not null references auth.users(id) on delete cascade,
  parent_id uuid references public.drive_folders(id) on delete cascade,
  nama text not null check (nullif(btrim(nama), '') is not null),
  is_public boolean not null default false,
  share_token uuid not null default gen_random_uuid() unique,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now(),
  constraint drive_folder_not_own_parent check (parent_id is null or parent_id <> id)
);

create table if not exists public.drive_files (
  id uuid primary key default gen_random_uuid(),
  owner_id uuid not null references auth.users(id) on delete cascade,
  folder_id uuid references public.drive_folders(id) on delete cascade,
  nama_asli text not null,
  nama_tampilan text not null check (nullif(btrim(nama_tampilan), '') is not null),
  storage_path text not null unique,
  mime_type text,
  ukuran_bytes bigint not null default 0 check (ukuran_bytes >= 0),
  is_public boolean not null default false,
  share_token uuid not null default gen_random_uuid() unique,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

create index if not exists drive_folders_owner_parent_index on public.drive_folders(owner_id, parent_id);
create index if not exists drive_files_owner_folder_index on public.drive_files(owner_id, folder_id);

drop trigger if exists drive_folders_set_updated_at on public.drive_folders;
create trigger drive_folders_set_updated_at
before update on public.drive_folders
for each row execute function public.set_updated_at();

drop trigger if exists drive_files_set_updated_at on public.drive_files;
create trigger drive_files_set_updated_at
before update on public.drive_files
for each row execute function public.set_updated_at();

alter table public.drive_folders enable row level security;
alter table public.drive_files enable row level security;

-- =========================================================
-- STORAGE BUCKET PUBLIC UNTUK URL FILE/GAMBAR
-- =========================================================
insert into storage.buckets (id, name, public)
values
  ('profile-photos', 'profile-photos', true),
  ('materi-files', 'materi-files', true),
  ('event-images', 'event-images', true),
  ('drive-files', 'drive-files', false)
on conflict (id) do update set public = excluded.public;

drop policy if exists "campushub_public_profile_photos" on storage.objects;
create policy "campushub_public_profile_photos"
on storage.objects for select
to public
using (bucket_id = 'profile-photos');

drop policy if exists "campushub_public_materi_files" on storage.objects;
create policy "campushub_public_materi_files"
on storage.objects for select
to public
using (bucket_id = 'materi-files');

drop policy if exists "campushub_public_event_images" on storage.objects;
create policy "campushub_public_event_images"
on storage.objects for select
to public
using (bucket_id = 'event-images');
