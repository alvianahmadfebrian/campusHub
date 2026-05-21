<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'

const form = useForm({
    email: '',
    password: '',
})

function submit() {
    form.post('/login')
}
</script>

<template>
    <Head title="Login" />
    <div class="container" style="max-width: 480px; min-height: 100vh; display: grid; place-items: center;">
        <div class="card" style="width: 100%;">
            <h1 class="title">Login CampusHub</h1>
            <p class="muted">Masuk ke portal mahasiswa.</p>

            <form @submit.prevent="submit" style="margin-top: 22px;">
                <div class="form-row">
                    <label class="label">Email</label>
                    <input class="input" v-model="form.email" type="email" autofocus />
                    <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
                </div>

                <div class="form-row">
                    <label class="label">Password</label>
                    <input class="input" v-model="form.password" type="password" />
                    <div v-if="form.errors.password" class="error">{{ form.errors.password }}</div>
                </div>

                <button class="btn" type="submit" :disabled="form.processing" style="width: 100%;">
                    {{ form.processing ? 'Memproses...' : 'Login' }}
                </button>
            </form>

            <p class="muted" style="margin-top: 18px;">
                Belum punya akun?
                <Link href="/register" style="color:#3730a3;font-weight:700;">Register</Link>
            </p>
        </div>
    </div>
</template>
