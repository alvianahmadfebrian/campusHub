<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    nama: '',
    nim: '',
    jurusan: '',
    semester: '',
    email: '',
    password: '',
    password_confirmation: '',
})

function submit() {
    form.post('/register')
}
</script>

<template>
    <Head title="Register" />
    <div class="container" style="max-width: 560px; min-height: 100vh; display: grid; place-items: center;">
        <div class="card" style="width: 100%;">
            <h1 class="title">Daftar CampusHub</h1>
            <p class="muted">Buat akun mahasiswa baru.</p>

            <form @submit.prevent="submit" style="margin-top: 22px;">
                <div class="grid grid-2">
                    <div class="form-row">
                        <label class="label">Nama</label>
                        <input class="input" v-model="form.nama" />
                        <div v-if="form.errors.nama" class="error">{{ form.errors.nama }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">NIM</label>
                        <input class="input" v-model="form.nim" />
                        <div v-if="form.errors.nim" class="error">{{ form.errors.nim }}</div>
                    </div>
                </div>

                <div class="grid grid-2">
                    <div class="form-row">
                        <label class="label">Jurusan</label>
                        <input class="input" v-model="form.jurusan" />
                        <div v-if="form.errors.jurusan" class="error">{{ form.errors.jurusan }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Semester</label>
                        <input class="input" v-model="form.semester" type="number" />
                        <div v-if="form.errors.semester" class="error">{{ form.errors.semester }}</div>
                    </div>
                </div>

                <div class="form-row">
                    <label class="label">Email</label>
                    <input class="input" v-model="form.email" type="email" />
                    <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
                </div>

                <div class="grid grid-2">
                    <div class="form-row">
                        <label class="label">Password</label>
                        <input class="input" v-model="form.password" type="password" />
                        <div v-if="form.errors.password" class="error">{{ form.errors.password }}</div>
                    </div>
                    <div class="form-row">
                        <label class="label">Konfirmasi Password</label>
                        <input class="input" v-model="form.password_confirmation" type="password" />
                    </div>
                </div>

                <button class="btn" type="submit" :disabled="form.processing" style="width: 100%;">
                    {{ form.processing ? 'Memproses...' : 'Register' }}
                </button>
            </form>

            <p class="muted" style="margin-top: 18px;">
                Sudah punya akun?
                <Link href="/login" style="color:#3730a3;font-weight:700;">Login</Link>
            </p>
        </div>
    </div>
</template>
