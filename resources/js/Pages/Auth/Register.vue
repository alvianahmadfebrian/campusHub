<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    jurusan: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    nama: '',
    nim: '',
    jurusan_id: '',
    semester: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Register" />

    <div class="auth-shell register-shell">
        <section class="auth-panel auth-brand">
            <span class="eyebrow">Mahasiswa Baru</span>
            <h1>Buat akun CampusHub.</h1>
            <p>
                Pilih jurusan yang sudah disediakan admin agar informasi dan materi yang tampil sesuai program studi kamu.
            </p>
        </section>

        <section class="card auth-card wide">
            <div class="auth-heading">
                <h2>Registrasi akun</h2>
                <p class="muted">Masukkan data profil dan akun login.</p>
            </div>

            <div v-if="props.jurusan.length === 0" class="notice warning">
                Belum ada jurusan aktif. Hubungi admin agar menambahkan jurusan sebelum kamu mendaftar.
            </div>

            <form @submit.prevent="submit">
                <div class="grid grid-2">
                    <div class="form-row">
                        <label class="label" for="nama">Nama lengkap</label>
                        <input id="nama" v-model="form.nama" class="input" required />
                        <div v-if="form.errors.nama" class="error">{{ form.errors.nama }}</div>
                    </div>

                    <div class="form-row">
                        <label class="label" for="nim">NIM</label>
                        <input id="nim" v-model="form.nim" class="input" />
                        <div v-if="form.errors.nim" class="error">{{ form.errors.nim }}</div>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-row">
                        <label class="label" for="jurusan_id">Jurusan</label>
                        <select id="jurusan_id" v-model="form.jurusan_id" class="input" required>
                            <option value="" disabled>Pilih jurusan</option>
                            <option v-for="item in props.jurusan" :key="item.id" :value="item.id">
                                {{ item.nama }}{{ item.kode ? ` (${item.kode})` : '' }}
                            </option>
                        </select>
                        <div v-if="form.errors.jurusan_id" class="error">{{ form.errors.jurusan_id }}</div>
                    </div>

                    <div class="form-row">
                        <label class="label" for="semester">Semester</label>
                        <input id="semester" v-model="form.semester" class="input" type="number" min="1" max="14" />
                        <div v-if="form.errors.semester" class="error">{{ form.errors.semester }}</div>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label" for="email">Email</label>
                    <input id="email" v-model="form.email" class="input" type="email" autocomplete="email" required />
                    <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
                </div>

                <div class="grid grid-2">
                    <div class="form-row">
                        <label class="label" for="password">Password</label>
                        <input id="password" v-model="form.password" class="input" type="password" autocomplete="new-password" required />
                        <div v-if="form.errors.password" class="error">{{ form.errors.password }}</div>
                    </div>

                    <div class="form-row">
                        <label class="label" for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation" v-model="form.password_confirmation" class="input" type="password" autocomplete="new-password" required />
                    </div>
                </div>

                <button class="btn full" type="submit" :disabled="form.processing || props.jurusan.length === 0">
                    {{ form.processing ? 'Memproses...' : 'Register' }}
                </button>
            </form>

            <p class="muted auth-footer">
                Sudah punya akun?
                <Link href="/login" class="text-link">Login</Link>
            </p>
        </section>
    </div>
</template>
