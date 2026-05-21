create extension if not exists "pgcrypto";

create table if not exists profiles (
  id uuid primary key references auth.users(id) on delete cascade,
  nama text not null,
  nim text,
  jurusan text,
  semester int,
  role text default 'mahasiswa',
  avatar_url text,
  created_at timestamptz default now()
);

create table if not exists jadwal (
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

create table if not exists pengumuman (
  id uuid primary key default gen_random_uuid(),
  judul text not null,
  isi text not null,
  kategori text,
  created_by uuid references auth.users(id) on delete set null,
  created_at timestamptz default now()
);

create table if not exists materi (
  id uuid primary key default gen_random_uuid(),
  judul text not null,
  mata_kuliah text,
  deskripsi text,
  file_url text,
  uploaded_by uuid references auth.users(id) on delete set null,
  created_at timestamptz default now()
);

create table if not exists forum_posts (
  id uuid primary key default gen_random_uuid(),
  user_id uuid references auth.users(id) on delete cascade,
  judul text not null,
  isi text not null,
  mata_kuliah text,
  created_at timestamptz default now()
);

create table if not exists forum_comments (
  id uuid primary key default gen_random_uuid(),
  post_id uuid references forum_posts(id) on delete cascade,
  user_id uuid references auth.users(id) on delete cascade,
  komentar text not null,
  created_at timestamptz default now()
);

create table if not exists events (
  id uuid primary key default gen_random_uuid(),
  nama_event text not null,
  deskripsi text,
  tanggal date,
  lokasi text,
  gambar_url text,
  link_pendaftaran text,
  created_at timestamptz default now()
);

create table if not exists absensi (
  id uuid primary key default gen_random_uuid(),
  user_id uuid references auth.users(id) on delete cascade,
  mata_kuliah text not null,
  tanggal date default current_date,
  status text default 'hadir',
  created_at timestamptz default now()
);
