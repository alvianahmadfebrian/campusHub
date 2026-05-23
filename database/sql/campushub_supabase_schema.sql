-- CampusHub - Supabase database setup
-- Jalankan file ini pada Supabase SQL Editor sebelum memakai aplikasi.

create extension if not exists "pgcrypto";

-- =========================================================
-- TABEL PROFIL DAN AUTH
-- =========================================================
create table if not exists public.profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  nama text not null,
  nim text,
  jurusan text,
  semester int4,
  role text not null default 'mahasiswa',
  avatar_url text,
  created_at timestamptz not null default now(),
  updated_at timestamptz not null default now()
);

alter table public.profiles
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

alter table public.profiles
  drop constraint if exists profiles_role_check;

alter table public.profiles
  add constraint profiles_role_check
  check (role in ('mahasiswa', 'admin'));

alter table public.profiles
  drop constraint if exists profiles_semester_check;

alter table public.profiles
  add constraint profiles_semester_check
  check (semester is null or semester between 1 and 14);

-- Trigger: setiap pendaftaran Supabase Auth otomatis mempunyai profil mahasiswa.
create or replace function public.handle_new_user()
returns trigger
language plpgsql
security definer set search_path = ''
as $$
begin
  insert into public.profiles (
    id,
    nama,
    nim,
    jurusan,
    semester,
    role,
    created_at,
    updated_at
  )
  values (
    new.id,
    coalesce(nullif(new.raw_user_meta_data ->> 'nama', ''), split_part(new.email, '@', 1)),
    nullif(new.raw_user_meta_data ->> 'nim', ''),
    nullif(new.raw_user_meta_data ->> 'jurusan', ''),
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

-- =========================================================
-- TABEL KONTEN
-- =========================================================
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

create table if not exists public.pengumuman (
  id uuid primary key default gen_random_uuid(),
  judul text not null,
  isi text not null,
  kategori text,
  created_by uuid references auth.users(id) on delete set null,
  created_at timestamptz default now()
);

create table if not exists public.materi (
  id uuid primary key default gen_random_uuid(),
  judul text not null,
  mata_kuliah text,
  deskripsi text,
  file_url text,
  uploaded_by uuid references auth.users(id) on delete set null,
  created_at timestamptz default now()
);

create table if not exists public.events (
  id uuid primary key default gen_random_uuid(),
  nama_event text not null,
  deskripsi text,
  tanggal date,
  lokasi text,
  gambar_url text,
  link_pendaftaran text,
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
-- Aplikasi Laravel mengakses database dari server; policy ini melindungi
-- akses bila tabel dipakai langsung melalui Supabase API.
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
-- STORAGE BUCKET PUBLIC UNTUK URL FILE/GAMBAR
-- Buat juga S3 Access Key di dashboard Supabase dan isi .env.
-- =========================================================
insert into storage.buckets (id, name, public)
values
  ('profile-photos', 'profile-photos', true),
  ('materi-files', 'materi-files', true),
  ('event-images', 'event-images', true)
on conflict (id) do update set public = true;

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
