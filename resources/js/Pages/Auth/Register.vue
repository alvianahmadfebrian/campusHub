<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { useDarkMode } from '@/composables/useDarkMode'

const { isDark, toggleDark } = useDarkMode()

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
    terms: false,
})

function submit() {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <Head title="Register – CampusHub" />

    <div class="reg-shell">
        <!-- KIRI -->
        <div class="reg-left">
            <div class="reg-left-inner">
                <div class="reg-logo">
                    <div class="reg-logo-icon">
                        <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                            <path d="M10 2L18 6V10C18 14.418 14.418 18 10 18C5.582 18 2 14.418 2 10V6L10 2Z" fill="white" fill-opacity="0.35"/>
                            <path d="M7 10L9.5 12.5L14 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span>CampusHub</span>
                </div>

                <div class="reg-hero">
                    <h1>Gerbang Anda menuju pengalaman akademik yang lebih mudah dan nyaman</h1>
                    <p>Kelola mata kuliah, pantau perkembangan, dan terhubung dengan komunitas kampus dalam satu portal terpadu.</p>
                </div>

                <div class="reg-cards">
                    <div class="reg-card">
                        <div class="reg-card-icon">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                <rect x="2" y="3" width="16" height="14" rx="2" stroke="white" stroke-width="1.5"/>
                                <path d="M6 7h8M6 10h5" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="rc-title">Unified Dashboard</p>
                            <p class="rc-sub">Real-time academic performance tracking.</p>
                        </div>
                    </div>
                    <div class="reg-card">
                        <div class="reg-card-icon">
                            <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                                <path d="M10 2C10 2 4 5 4 10.5C4 13.538 6.686 16 10 16C13.314 16 16 13.538 16 10.5C16 5 10 2 10 2Z" stroke="white" stroke-width="1.5"/>
                                <path d="M10 8v4M10 14h.01" stroke="white" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <p class="rc-title">Live Updates</p>
                            <p class="rc-sub">Instant notifications for campus news.</p>
                        </div>
                    </div>
                </div>

                <div class="reg-social">
                    <div class="reg-avatars">
                        <div class="av" style="background:#7dd3fc">A</div>
                        <div class="av" style="background:#86efac">B</div>
                        <div class="av" style="background:#fca5a5">C</div>
                        <div class="av" style="background:#c4b5fd">D</div>
                    </div>
                    <span>+2,400 students joined this week</span>
                </div>
            </div>
        </div>

        <!-- KANAN -->
        <div class="reg-right">
            <div class="reg-form-wrap">
                <div class="reg-form-header">
    

    <h2>Buat Akun Mahasiswa</h2>
</div>

                <div v-if="props.jurusan.length === 0" class="reg-notice">
                    ⚠ Belum ada jurusan aktif. Hubungi admin agar menambahkan jurusan sebelum kamu mendaftar.
                </div>

                <form @submit.prevent="submit" class="reg-form">
                    <div class="rf-row-2">
                        <div class="rf-group">
                            <label for="nama_depan">Nama depan</label>
                            <input id="nama_depan" v-model="form.nama" placeholder="Muhammad" required />
                            <span v-if="form.errors.nama" class="rf-error">{{ form.errors.nama }}</span>
                        </div>
                        <div class="rf-group">
                            <label for="nama_belakang">Nama belakang</label>
                            <input id="nama_belakang" placeholder="Gerald" />
                        </div>
                    </div>

                    <div class="rf-group">
                        <label for="nim">NPM (Student ID)</label>
                        <div class="rf-input-wrap">
                            <svg class="rf-icon" viewBox="0 0 20 20" fill="none">
                                <rect x="4" y="2" width="12" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 7h6M7 10h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <input id="nim" v-model="form.nim" placeholder="12345678" />
                        </div>
                        <span v-if="form.errors.nim" class="rf-error">{{ form.errors.nim }}</span>
                    </div>

                    <div class="rf-group">
                        <label for="email">Email Kampus</label>
                        <div class="rf-input-wrap">
                            <svg class="rf-icon" viewBox="0 0 20 20" fill="none">
                                <path d="M2.5 6.5L10 11L17.5 6.5M3 5h14a1 1 0 011 1v9a1 1 0 01-1 1H3a1 1 0 01-1-1V6a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <input id="email" v-model="form.email" type="email" placeholder="name@student.university.ac.id" required />
                        </div>
                        <span v-if="form.errors.email" class="rf-error">{{ form.errors.email }}</span>
                    </div>

                    <div class="rf-row-2">
                        <div class="rf-group">
                            <label for="jurusan_id">Jurusan</label>
                            <div class="rf-select-wrap">
                                <select id="jurusan_id" v-model="form.jurusan_id" required>
                                    <option value="" disabled>Pilih Jurusan</option>
                                    <option v-for="item in props.jurusan" :key="item.id" :value="item.id">
                                        {{ item.nama }}{{ item.kode ? ` (${item.kode})` : '' }}
                                    </option>
                                </select>
                                <svg class="rf-select-arrow" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <span v-if="form.errors.jurusan_id" class="rf-error">{{ form.errors.jurusan_id }}</span>
                        </div>
                        <div class="rf-group">
                            <label for="semester">Semester</label>
                            <div class="rf-select-wrap">
                                <select id="semester" v-model="form.semester">
                                    <option value="" disabled>Pilih Semester</option>
                                    <option v-for="n in 14" :key="n" :value="n">Semester {{ n }}</option>
                                </select>
                                <svg class="rf-select-arrow" viewBox="0 0 20 20" fill="none">
                                    <path d="M5 7.5l5 5 5-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <span v-if="form.errors.semester" class="rf-error">{{ form.errors.semester }}</span>
                        </div>
                    </div>

                    <div class="rf-group">
                        <label for="password">Password</label>
                        <div class="rf-input-wrap">
                            <svg class="rf-icon" viewBox="0 0 20 20" fill="none">
                                <rect x="4" y="9" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 9V6.5a3 3 0 016 0V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <input id="password" v-model="form.password" type="password" placeholder="••••••••" required autocomplete="new-password" />
                        </div>
                        <span class="rf-hint">Use 8+ characters with a mix of letters, numbers &amp; symbols.</span>
                        <span v-if="form.errors.password" class="rf-error">{{ form.errors.password }}</span>
                    </div>

                    <div class="rf-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <div class="rf-input-wrap">
                            <svg class="rf-icon" viewBox="0 0 20 20" fill="none">
                                <rect x="4" y="9" width="12" height="9" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 9V6.5a3 3 0 016 0V9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                            <input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="••••••••" required autocomplete="new-password" />
                        </div>
                    </div>

                    <label class="rf-terms">
                        <input type="checkbox" v-model="form.terms" required />
                        <span>I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.</span>
                    </label>

                    <button type="submit" class="rf-submit" :disabled="form.processing || props.jurusan.length === 0">
                        {{ form.processing ? 'Memproses...' : 'Create Account →' }}
                    </button>
                </form>

                <p class="rf-login">
                    Sudah punya akun? <Link href="/login">Masuk Sekarang</Link>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.reg-shell {
    display: grid;
    grid-template-columns: 1fr 1.2fr;
    min-height: 100vh;
}

/* ---- KIRI ---- */
.reg-left {
    background: linear-gradient(150deg, #0d9488 0%, #0f766e 50%, #0a5c55 100%);
    position: relative;
    overflow: hidden;
    display: flex;
}

.reg-left::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(ellipse at 20% 15%, rgba(255,255,255,0.09) 0%, transparent 55%),
        radial-gradient(ellipse at 80% 85%, rgba(0,0,0,0.12) 0%, transparent 55%);
}

.reg-left-inner {
    position: relative;
    z-index: 1;
    display: flex;
    flex-direction: column;
    width: 100%;
    padding: 38px 40px;
}

.reg-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
    font-size: 18px;
    font-weight: 700;
    letter-spacing: -0.02em;
}

.reg-logo-icon {
    width: 30px;
    height: 30px;
    background: rgba(255,255,255,0.2);
    border-radius: 8px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.reg-hero {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 40px 0 28px;
}

.reg-hero h1 {
    margin: 0 0 14px;
    font-size: clamp(26px, 2.8vw, 38px);
    font-weight: 800;
    line-height: 1.12;
    letter-spacing: -0.04em;
    color: white;
    max-width: 340px;
}

.reg-hero p {
    margin: 0;
    color: rgba(255,255,255,0.78);
    font-size: 14px;
    line-height: 1.65;
    max-width: 320px;
}

.reg-cards {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-bottom: 28px;
}

.reg-card {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: rgba(255,255,255,0.12);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 14px;
    padding: 14px 16px;
    backdrop-filter: blur(8px);
}

.reg-card-icon {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.rc-title {
    margin: 0 0 3px;
    color: white;
    font-size: 13px;
    font-weight: 700;
}

.rc-sub {
    margin: 0;
    color: rgba(255,255,255,0.68);
    font-size: 12px;
    line-height: 1.5;
}

.reg-social {
    display: flex;
    align-items: center;
    gap: 12px;
}

.reg-avatars {
    display: flex;
}

.av {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.5);
    display: grid;
    place-items: center;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    margin-left: -8px;
    flex-shrink: 0;
}

.av:first-child { margin-left: 0; }

.reg-social span {
    color: rgba(255,255,255,0.8);
    font-size: 12px;
    font-weight: 500;
}

/* ---- KANAN ---- */
.reg-right {
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 32px;
    overflow-y: auto;
}

.reg-form-wrap {
    width: 100%;
    max-width: 440px;
}

.reg-form-header {
    margin-bottom: 24px;
}

.reg-form-header h2 {
    margin: 0 0 5px;
    font-size: 24px;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #0f172a;
}

.reg-form-header p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
}

.reg-notice {
    background: #fffbeb;
    border: 1px solid #fde68a;
    color: #92400e;
    border-radius: 10px;
    padding: 11px 14px;
    font-size: 13px;
    margin-bottom: 18px;
}

.reg-form {
    display: flex;
    flex-direction: column;
    gap: 0;
}

.rf-group {
    margin-bottom: 14px;
}

.rf-group label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
}

.rf-input-wrap {
    position: relative;
    display: flex;
    align-items: center;
}

.rf-icon {
    position: absolute;
    left: 11px;
    width: 16px;
    height: 16px;
    color: #94a3b8;
    pointer-events: none;
    flex-shrink: 0;
}

.rf-input-wrap input,
.rf-group input {
    width: 100%;
    padding: 10px 13px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s, background 0.15s;
    font-family: inherit;
}

.rf-input-wrap input {
    padding-left: 36px;
}

.rf-input-wrap input:focus,
.rf-group input:focus {
    border-color: #0d9488;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
}

.rf-select-wrap {
    position: relative;
}

.rf-select-wrap select {
    width: 100%;
    padding: 10px 36px 10px 13px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13px;
    color: #0f172a;
    background: #f8fafc;
    outline: none;
    appearance: none;
    cursor: pointer;
    transition: border-color 0.15s, box-shadow 0.15s;
    font-family: inherit;
}

.rf-select-wrap select:focus {
    border-color: #0d9488;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
}

.rf-select-arrow {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    color: #94a3b8;
    pointer-events: none;
}

.rf-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 14px;
}

.rf-hint {
    display: block;
    margin-top: 4px;
    font-size: 11px;
    color: #94a3b8;
}

.rf-error {
    display: block;
    margin-top: 4px;
    font-size: 12px;
    color: #ef4444;
}

.rf-terms {
    display: flex;
    align-items: flex-start;
    gap: 9px;
    font-size: 13px;
    color: #475569;
    cursor: pointer;
    margin-bottom: 18px;
    margin-top: 4px;
}

.rf-terms input[type="checkbox"] {
    width: 15px;
    height: 15px;
    accent-color: #0d9488;
    margin-top: 1px;
    flex-shrink: 0;
    cursor: pointer;
}

.rf-terms a {
    color: #0d9488;
    font-weight: 600;
    text-decoration: none;
}

.rf-terms a:hover { text-decoration: underline; }

.rf-submit {
    width: 100%;
    padding: 12px;
    background: #0d9488;
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s;
    letter-spacing: -0.01em;
    font-family: inherit;
}

.rf-submit:hover:not(:disabled) { background: #0f766e; }
.rf-submit:active:not(:disabled) { transform: scale(0.99); }
.rf-submit:disabled { opacity: 0.65; cursor: not-allowed; }

.rf-login {
    text-align: center;
    font-size: 13px;
    color: #64748b;
    margin-top: 18px;
}

.rf-login a {
    color: #0d9488;
    font-weight: 700;
    text-decoration: none;
}

.rf-login a:hover { text-decoration: underline; }

/* Responsive */
@media (max-width: 900px) {
    .reg-shell { grid-template-columns: 1fr; }
    .reg-left { min-height: 300px; }
    .reg-left-inner { padding: 28px; }
    .reg-hero h1 { font-size: 26px; }
}

@media (max-width: 500px) {
    .rf-row-2 { grid-template-columns: 1fr; }
    .reg-right { padding: 28px 20px; }
}

</style>