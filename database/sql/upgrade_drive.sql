-- CampusHub upgrade: Drive pribadi dengan folder, file, dan share URL publik
-- Jalankan pada Supabase SQL Editor SETELAH upgrade_jurusan_target.sql.
-- File Drive disimpan pada bucket private; URL publik dilayani oleh aplikasi Laravel.

create extension if not exists "pgcrypto";

-- =========================================================
-- DRIVE FOLDERS
-- is_public = true memberi link folder publik tanpa login.
-- Subfolder dan file di dalam folder yang dibagikan ikut dapat dibuka lewat link folder.
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

-- =========================================================
-- DRIVE FILES
-- File tetap berada pada bucket private.
-- is_public hanya menentukan apakah route share/file dapat membuka file tersebut.
-- =========================================================
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

create index if not exists drive_folders_owner_parent_index
  on public.drive_folders(owner_id, parent_id);

create index if not exists drive_files_owner_folder_index
  on public.drive_files(owner_id, folder_id);

create index if not exists drive_folders_public_token_index
  on public.drive_folders(share_token)
  where is_public = true;

create index if not exists drive_files_public_token_index
  on public.drive_files(share_token)
  where is_public = true;

-- updated_at otomatis
create or replace function public.set_updated_at()
returns trigger
language plpgsql
as $$
begin
  new.updated_at = now();
  return new;
end;
$$;

drop trigger if exists drive_folders_set_updated_at on public.drive_folders;
create trigger drive_folders_set_updated_at
before update on public.drive_folders
for each row execute function public.set_updated_at();

drop trigger if exists drive_files_set_updated_at on public.drive_files;
create trigger drive_files_set_updated_at
before update on public.drive_files
for each row execute function public.set_updated_at();

-- Tabel tidak diekspos langsung melalui Supabase Data API.
-- CRUD dilakukan oleh aplikasi Laravel berdasarkan user session.
alter table public.drive_folders enable row level security;
alter table public.drive_files enable row level security;

-- Bucket Drive HARUS PRIVATE. Jangan ubah menjadi public.
insert into storage.buckets (id, name, public)
values ('drive-files', 'drive-files', false)
on conflict (id) do update set public = false;

-- Tidak membuat policy public pada storage.objects untuk bucket drive-files.
-- Pengambilan file public dilakukan melalui route Laravel yang memeriksa share_token.
