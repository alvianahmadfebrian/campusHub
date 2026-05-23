-- Ganti email berikut dengan email admin yang sudah terdaftar di Supabase Auth.
update public.profiles p
set role = 'admin',
    updated_at = now()
from auth.users u
where p.id = u.id
  and u.email = 'EMAIL_ADMIN_ANDA';
