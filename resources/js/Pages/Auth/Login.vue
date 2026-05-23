<script setup>
import { computed } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const page = usePage()
const success = computed(() => page.props.flash?.success)

const form = useForm({
    email: '',
    password: '',
})

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Login" />

    <div class="auth-shell">
        <section class="auth-panel auth-brand">
            <span class="eyebrow">CampusHub</span>
            <h1>Portal akademik dalam satu tempat.</h1>
            <p>
                Akses pengumuman, materi, dan event kampus secara cepat dari dashboard mahasiswa.
            </p>
        </section>

        <section class="card auth-card">
            <div class="auth-heading">
                <h2>Selamat datang</h2>
                <p class="muted">Masuk menggunakan akun CampusHub.</p>
            </div>

            <div v-if="success" class="flash">
                {{ success }}
            </div>

            <form @submit.prevent="submit">
                <div class="form-row">
                    <label class="label" for="email">Email</label>
                    <input
                        id="email"
                        v-model="form.email"
                        class="input"
                        type="email"
                        autocomplete="email"
                        autofocus
                        required
                        placeholder="nama@email.com"
                    />
                    <div v-if="form.errors.email" class="error">{{ form.errors.email }}</div>
                </div>

                <div class="form-row">
                    <label class="label" for="password">Password</label>
                    <input
                        id="password"
                        v-model="form.password"
                        class="input"
                        type="password"
                        autocomplete="current-password"
                        required
                        placeholder="••••••••"
                    />
                    <div v-if="form.errors.password" class="error">{{ form.errors.password }}</div>
                </div>

                <button class="btn full" type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Memproses...' : 'Login' }}
                </button>
            </form>

            <p class="muted auth-footer">
                Belum punya akun?
                <Link href="/register" class="text-link">Daftar mahasiswa</Link>
            </p>
        </section>
    </div>
</template>
