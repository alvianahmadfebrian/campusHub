<script setup>
import { computed, ref } from 'vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'

const { isDark, toggleDark } = useDarkMode()

const page = usePage()
const success = computed(() => page.props.flash?.success)
const showPassword = ref(false)

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

function submit() {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Login – CampusHub" />

    <div class="login-shell">
        <!-- Kiri: Brand panel -->
        <div class="login-left">
            <div class="login-left-inner">
                <div class="login-logo">
                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                        <path d="M14 2L26 8V14C26 20.627 20.627 26 14 26C7.373 26 2 20.627 2 14V8L14 2Z" fill="white" fill-opacity="0.25"/>
                        <path d="M14 5L23 9.5V14C23 18.971 18.971 23 14 23C9.029 23 5 18.971 5 14V9.5L14 5Z" fill="white" fill-opacity="0.4"/>
                        <path d="M9 13L13 17L20 10" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span>CampusHub</span>
                </div>

                <div class="login-hero-text">
                    <h1>Portal Mahasiswa<br />Terpadu</h1>
                    <p>Akses materi perkuliahan, jadwal akademik, dan informasi kampus terbaru dalam satu platform modern yang dirancang untuk mendukung kesuksesan studimu.</p>
                </div>

                <div class="login-campus-img">
                    <img
                        src="https://www.unpak.ac.id/images/gedung-unpak.jpg"
                        alt="Unpak"
                    />
                    <div class="login-img-overlay"></div>
                </div>
            </div>
        </div>

        <!-- Kanan: Form -->
        <div class="login-right">
            <div class="login-form-wrap">
                <div class="login-form-header">
    <button type="button" class="auth-theme-toggle" @click="toggleDark">
        {{ isDark ? '☀️ Mode Terang' : '🌙 Mode Gelap' }}
    </button>

    <h2>Selamat Datang</h2>
    <p>Silakan masuk menggunakan akun mahasiswa Anda.</p>
</div>

                <div v-if="success" class="flash-success">{{ success }}</div>

                <form @submit.prevent="submit" class="login-form">
                    <div class="lf-group">
                        <label for="email">Email Mahasiswa</label>
                        <div class="lf-input-wrap">
                            <svg class="lf-icon" viewBox="0 0 20 20" fill="none">
                                <path d="M2.5 6.5L10 11L17.5 6.5M3 5h14a1 1 0 011 1v9a1 1 0 01-1 1H3a1 1 0 01-1-1V6a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                autocomplete="email"
                                autofocus
                                required
                                placeholder="mhs@university.ac.id"
                                :class="{ 'has-error': form.errors.email }"
                            />
                        </div>
                        <span v-if="form.errors.email" class="lf-error">{{ form.errors.email }}</span>
                    </div>

                    <div class="lf-group">
                        <label for="password">Kata Sandi</label>
                        <div class="lf-input-wrap">
                            <svg class="lf-icon" viewBox="0 0 20 20" fill="none">
                                <rect x="4" y="9" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 9V6.5a3 3 0 016 0V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <input
                                id="password"
                                v-model="form.password"
                                :type="showPassword ? 'text' : 'password'"
                                autocomplete="current-password"
                                required
                                placeholder="••••••••"
                                :class="{ 'has-error': form.errors.password }"
                            />
                            <button type="button" class="lf-toggle-pw" @click="showPassword = !showPassword" tabindex="-1">
                                <svg v-if="!showPassword" viewBox="0 0 20 20" fill="none">
                                    <path d="M2 10s3-6 8-6 8 6 8 6-3 6-8 6-8-6-8-6z" stroke="currentColor" stroke-width="1.5"/>
                                    <circle cx="10" cy="10" r="2.5" stroke="currentColor" stroke-width="1.5"/>
                                </svg>
                                <svg v-else viewBox="0 0 20 20" fill="none">
                                    <path d="M3 3l14 14M8.5 8.58A2.5 2.5 0 0111.42 11.5M6.3 6.3C4.6 7.38 3.22 9 2 10c0 0 3 6 8 6a7.7 7.7 0 003.7-.96M10 4c5 0 8 6 8 6a14.5 14.5 0 01-2.24 2.76" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>
                        <span v-if="form.errors.password" class="lf-error">{{ form.errors.password }}</span>
                    </div>

                    <div class="lf-options">
                        <label class="lf-remember">
                            <input type="checkbox" v-model="form.remember" />
                            <span>Ingat saya</span>
                        </label>
                        <a href="#" class="lf-forgot">Lupa kata sandi?</a>
                    </div>

                    <button type="submit" class="lf-submit" :disabled="form.processing">
                        {{ form.processing ? 'Memproses...' : 'Masuk Sekarang' }}
                    </button>

                    <div class="lf-divider"><span>atau</span></div>

                    <p class="lf-register">
                        Belum punya akun?
                        <Link href="/register">Daftar Akun Baru</Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
.login-shell {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 100vh;
    background: #f5f7f6;
}

/* ---- KIRI ---- */
.login-left {
    background: linear-gradient(155deg, #0d9488 0%, #0f766e 45%, #0c5c55 100%);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.login-left::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 20% 20%, rgba(255,255,255,0.08) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 80%, rgba(0,0,0,0.15) 0%, transparent 60%);
}

.login-left-inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 38px 44px 0;
}

.login-logo {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    color: white;
    font-size: 20px;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.login-hero-text {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 40px 0 32px;
}

.login-hero-text h1 {
    margin: 0 0 18px;
    font-size: clamp(32px, 3.5vw, 46px);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.04em;
    color: white;
}

.login-hero-text p {
    margin: 0;
    color: rgba(255,255,255,0.8);
    font-size: 15px;
    line-height: 1.7;
    max-width: 380px;
}

.login-campus-img {
    position: relative;
    border-radius: 18px 18px 0 0;
    overflow: hidden;
    height: 230px;
    margin: 0 0 0;
    flex-shrink: 0;
}

.login-campus-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.login-img-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(to top, rgba(13, 148, 136, 0.5) 0%, transparent 60%);
}

/* ---- KANAN ---- */
.login-right {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 24px;
    background: #fff;
}

.login-form-wrap {
    width: 100%;
    max-width: 400px;
}

.login-form-header {
    margin-bottom: 28px;
}

.login-form-header h2 {
    margin: 0 0 6px;
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #0f172a;
}

.login-form-header p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.flash-success {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #065f46;
    border-radius: 10px;
    padding: 12px 14px;
    font-size: 14px;
    margin-bottom: 20px;
}

/* Form */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.lf-group {
    margin-bottom: 18px;
}

.lf-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 7px;
}

.lf-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.lf-icon {
    position: absolute;
    left: 13px;
    width: 18px;
    height: 18px;
    color: #94a3b8;
    flex-shrink: 0;
    pointer-events: none;
}

.lf-input-wrap input {
    width: 100%;
    padding: 11px 42px 11px 40px;
    border: 1.5px solid #e2e8f0;
    border-radius: 11px;
    font-size: 14px;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
}

.lf-input-wrap input:focus {
    border-color: #0d9488;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
}

.lf-input-wrap input.has-error {
    border-color: #ef4444;
}

.lf-toggle-pw {
    position: absolute;
    right: 12px;
    background: none;
    border: none;
    cursor: pointer;
    color: #94a3b8;
    display: grid;
    place-items: center;
    padding: 0;
}

.lf-toggle-pw svg {
    width: 18px;
    height: 18px;
}

.lf-toggle-pw:hover {
    color: #475569;
}

.lf-error {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #ef4444;
}

.lf-options {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 22px;
}

.lf-remember {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 13px;
    color: #475569;
}

.lf-remember input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #0d9488;
    cursor: pointer;
}

.lf-forgot {
    font-size: 13px;
    font-weight: 600;
    color: #0d9488;
    text-decoration: none;
}

.lf-forgot:hover {
    text-decoration: underline;
}

.lf-submit {
    width: 100%;
    padding: 13px;
    background: #0d9488;
    color: white;
    border: none;
    border-radius: 11px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
    letter-spacing: -0.01em;
}

.lf-submit:hover:not(:disabled) {
    background: #0f766e;
}

.lf-submit:active:not(:disabled) {
    transform: scale(0.99);
}

.lf-submit:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.lf-divider {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 22px 0;
    color: #cbd5e1;
    font-size: 13px;
}

.lf-divider::before,
.lf-divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #e2e8f0;
}

.lf-divider span {
    color: #94a3b8;
}

.lf-register {
    text-align: center;
    font-size: 14px;
    color: #64748b;
    margin: 0;
}

.lf-register a {
    color: #0d9488;
    font-weight: 700;
    text-decoration: none;
}

.lf-register a:hover {
    text-decoration: underline;
}

.lf-footer {
    display: flex;
    justify-content: center;
    gap: 24px;
    margin-top: 36px;
    padding-top: 20px;
    border-top: 1px solid #f1f5f9;
}

.lf-footer a {
    font-size: 12px;
    color: #94a3b8;
    text-decoration: none;
}

.lf-footer a:hover {
    color: #64748b;
}

/* Responsive */
@media (max-width: 820px) {
    .login-shell {
        grid-template-columns: 1fr;
    }
    .login-left {
        min-height: 280px;
    }
    .login-campus-img {
        height: 160px;
    }
    .login-hero-text h1 {
        font-size: 28px;
    }
    .login-left-inner {
        padding: 28px 28px 0;
    }
}
/* Dark mode untuk auth ditangani secara terpusat di app.css */

.auth-theme-toggle {
    margin-bottom: 18px;
    padding: 10px 14px;
    border: 1px solid #dbe3ea;
    border-radius: 12px;
    background: #ffffff;
    color: #0f766e;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s, border-color 0.15s, color 0.15s;
}
</style>