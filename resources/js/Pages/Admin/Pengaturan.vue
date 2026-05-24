<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    profile: {
        type: Object,
        default: () => ({
            nama: '',
            avatar_url: '',
            role: 'admin',
        }),
    },
    email: {
        type: String,
        default: '',
    },
})

const form = useForm({
    nama: props.profile?.nama || '',
    avatar_url: props.profile?.avatar_url || '',
})

function submit() {
    form.patch('/admin/pengaturan', {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Pengaturan Admin" />

    <AdminLayout>
        <div class="pg-page">
            <header>
                <p class="eyebrow">PENGATURAN ADMIN</p>
                <h1>Pengaturan</h1>
                <p>Kelola profil administrator CampusHub.</p>
            </header>

            <div class="pg-grid">
                <section class="card profile-card">
                    <div class="avatar">
                        <img
                            v-if="form.avatar_url"
                            :src="form.avatar_url"
                            alt="Avatar admin"
                        />
                        <span v-else>{{ (form.nama || 'A').charAt(0).toUpperCase() }}</span>
                    </div>

                    <h2>{{ form.nama || 'Administrator' }}</h2>
                    <p>{{ email }}</p>

                    <span class="role-badge">Admin CampusHub</span>
                </section>

                <section class="card form-card">
                    <h2>Profil Administrator</h2>

                    <form @submit.prevent="submit">
                        <label>Nama Admin</label>
                        <input v-model="form.nama" required />
                        <small v-if="form.errors.nama" class="error">{{ form.errors.nama }}</small>

                        <label>Email Login</label>
                        <input :value="email" disabled />
                        <small>Email dikelola melalui akun Supabase Authentication.</small>

                        <label>URL Avatar</label>
                        <input
                            v-model="form.avatar_url"
                            type="url"
                            placeholder="https://..."
                        />
                        <small v-if="form.errors.avatar_url" class="error">
                            {{ form.errors.avatar_url }}
                        </small>

                        <button type="submit" :disabled="form.processing">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                        </button>
                    </form>
                </section>

                <section class="card security-card">
                    <h2>Keamanan Akun</h2>

                    <div class="security-row">
                        <div>
                            <strong>Role Pengguna</strong>
                            <p>Akun ini memiliki akses administrator.</p>
                        </div>
                        <span class="safe">Admin</span>
                    </div>

                    <div class="security-row">
                        <div>
                            <strong>Chatbot AI</strong>
                            <p>Chatbot admin tidak memperoleh Drive private mahasiswa lain.</p>
                        </div>
                        <span class="safe">Aman</span>
                    </div>

                    <div class="security-row">
                        <div>
                            <strong>Ubah Password</strong>
                            <p>Password dikelola melalui Supabase Authentication.</p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
.pg-page {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.eyebrow {
    margin: 0;
    color: #0f9488;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .1em;
}

header h1 {
    margin: 5px 0 7px;
    color: #0f172a;
    font-size: 30px;
}

header p:not(.eyebrow) {
    margin: 0;
    color: #64748b;
}

.pg-grid {
    display: grid;
    grid-template-columns: 290px minmax(350px, 520px) minmax(300px, 1fr);
    gap: 16px;
    align-items: start;
}

.card {
    padding: 21px;
    border: 1px solid #e2e8f0;
    border-radius: 17px;
    background: white;
}

.card h2 {
    margin: 0 0 18px;
    color: #0f172a;
    font-size: 18px;
}

.profile-card {
    text-align: center;
}

.avatar {
    display: grid;
    place-items: center;
    width: 78px;
    height: 78px;
    margin: 0 auto 14px;
    overflow: hidden;
    border-radius: 999px;
    background: #ccfbf1;
    color: #0f766e;
    font-size: 28px;
    font-weight: 700;
}

.avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-card h2 {
    margin-bottom: 7px;
}

.profile-card p {
    margin: 0 0 17px;
    color: #64748b;
    font-size: 13px;
}

.role-badge {
    display: inline-flex;
    padding: 7px 12px;
    border-radius: 999px;
    background: #ecfdf5;
    color: #047857;
    font-size: 12px;
    font-weight: 700;
}

.form-card form {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-card label {
    margin-top: 6px;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
}

.form-card input {
    padding: 11px 12px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    font: inherit;
}

.form-card input:disabled {
    background: #f8fafc;
    color: #64748b;
}

.form-card small {
    color: #64748b;
    font-size: 11px;
}

.form-card .error {
    color: #dc2626;
}

.form-card button {
    margin-top: 16px;
    padding: 11px;
    border: 0;
    border-radius: 10px;
    background: #0f9488;
    color: white;
    cursor: pointer;
    font-weight: 700;
}

.security-row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 13px 0;
    border-top: 1px solid #eef2f7;
}

.security-row strong {
    color: #0f172a;
    font-size: 13px;
}

.security-row p {
    margin: 5px 0 0;
    color: #64748b;
    font-size: 12px;
    line-height: 1.5;
}

.safe {
    align-self: flex-start;
    padding: 5px 9px;
    border-radius: 999px;
    background: #ecfdf5;
    color: #047857;
    font-size: 11px;
    font-weight: 700;
}

@media (max-width: 1150px) {
    .pg-grid {
        grid-template-columns: 1fr;
    }
}
</style>
