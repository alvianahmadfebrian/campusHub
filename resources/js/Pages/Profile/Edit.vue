<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    profile: {
        type: Object,
        default: null,
    },
    jurusan: {
        type: Array,
        default: () => [],
    },
})

const editMode = ref(false)
const avatarPreview = ref(null)

const form = useForm({
    nama: props.profile?.nama || '',
    nim: props.profile?.nim || '',
    jurusan_id: props.profile?.jurusan_id || '',
    semester: props.profile?.semester || '',
    email: props.profile?.email || '',
    no_telfon: props.profile?.no_telfon || props.profile?.telepon || '',
    alamat: props.profile?.alamat || '',
    avatar: null,
})

const initials = computed(() => {
    const name = props.profile?.nama || 'M'

    return name
        .split(' ')
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word[0])
        .join('')
        .toUpperCase()
})

const completionItems = computed(() => {
    return [
        { label: 'Nama lengkap', value: props.profile?.nama },
        { label: 'NIM', value: props.profile?.nim },
        { label: 'Jurusan', value: props.profile?.jurusan_nama },
        { label: 'Semester', value: props.profile?.semester },
        { label: 'Foto profil', value: props.profile?.avatar_url },
        { label: 'Nomor telepon', value: props.profile?.no_telfon || props.profile?.telepon },
        { label: 'Alamat', value: props.profile?.alamat },
        { label: 'Email pribadi', value: props.profile?.email },
    ]
})

const completionPercent = computed(() => {
    const total = completionItems.value.length
    const filled = completionItems.value.filter((item) => Boolean(item.value)).length

    return Math.round((filled / total) * 100)
})

const institutionEmail = computed(() => {
    if (!props.profile?.nim) return '—'

    return `${String(props.profile.nim).toLowerCase()}@student.campushub.ac.id`
})

function onAvatarChange(event) {
    const file = event.target.files?.[0]

    if (!file) return

    form.avatar = file
    avatarPreview.value = URL.createObjectURL(file)
}

function startEdit() {
    form.clearErrors()

    form.nama = props.profile?.nama || ''
    form.nim = props.profile?.nim || ''
    form.jurusan_id = props.profile?.jurusan_id || ''
    form.semester = props.profile?.semester || ''
    form.email = props.profile?.email || ''
    form.no_telfon = props.profile?.no_telfon || props.profile?.telepon || ''
    form.alamat = props.profile?.alamat || ''
    form.avatar = null

    avatarPreview.value = null
    editMode.value = true
}

function cancelEdit() {
    form.clearErrors()

    form.nama = props.profile?.nama || ''
    form.nim = props.profile?.nim || ''
    form.jurusan_id = props.profile?.jurusan_id || ''
    form.semester = props.profile?.semester || ''
    form.email = props.profile?.email || ''
    form.no_telfon = props.profile?.no_telfon || props.profile?.telepon || ''
    form.alamat = props.profile?.alamat || ''
    form.avatar = null

    avatarPreview.value = null
    editMode.value = false
}

function submit() {
    form.post('/profile', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            editMode.value = false
            avatarPreview.value = null
            form.avatar = null
        },
    })
}

function onlyNumber() {
    form.no_telfon = String(form.no_telfon || '').replace(/[^0-9]/g, '')
}
</script>

<template>
    <Head title="Profil Mahasiswa" />

    <AppLayout>
        <div class="profile-page">
            <section class="profile-hero">
                <div class="profile-hero-bg"></div>

                <div class="profile-hero-content">
                    <div class="profile-avatar-wrap">
                        <img
                            v-if="avatarPreview || profile?.avatar_url"
                            :src="avatarPreview || profile.avatar_url"
                            :alt="profile?.nama || 'Avatar'"
                            class="profile-avatar-img"
                        />

                        <div v-else class="profile-avatar-fallback">
                            {{ initials }}
                        </div>

                        <label
                            v-if="editMode"
                            class="avatar-upload-btn"
                            title="Ganti foto"
                        >
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.5"
                            >
                                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                <circle cx="12" cy="13" r="4" />
                            </svg>

                            <input
                                type="file"
                                accept="image/*"
                                class="sr-only"
                                @change="onAvatarChange"
                            />
                        </label>
                    </div>

                    <div class="profile-hero-info">
                        <p class="profile-badge">
                            {{ profile?.role === 'admin' ? 'Administrator' : 'Mahasiswa' }}
                        </p>

                        <h1 class="profile-name">
                            {{ profile?.nama || 'Nama belum diisi' }}
                        </h1>

                        <p class="profile-sub">
                            <svg
                                width="13"
                                height="13"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                <line x1="4" y1="22" x2="4" y2="15" />
                            </svg>

                            {{ profile?.nim || 'NIM belum diisi' }}
                            ·
                            {{ profile?.jurusan_nama || 'Jurusan belum dipilih' }}
                        </p>
                    </div>

                    <div class="profile-hero-actions">
                        <button
                            v-if="!editMode"
                            type="button"
                            class="ph-btn ph-btn-primary"
                            @click="startEdit"
                        >
                            <svg
                                width="14"
                                height="14"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2.5"
                            >
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>

                            Edit Profil
                        </button>

                        <template v-else>
                            <button
                                type="button"
                                class="ph-btn ph-btn-primary"
                                :disabled="form.processing"
                                @click="submit"
                            >
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>

                            <button
                                type="button"
                                class="ph-btn ph-btn-ghost"
                                :disabled="form.processing"
                                @click="cancelEdit"
                            >
                                Batal
                            </button>
                        </template>
                    </div>
                </div>
            </section>

            <div class="profile-body">
                <aside class="profile-col-left">
                    <section class="pcard">
                        <div class="pcard-head">
                            <span class="pcard-title">Kelengkapan Profil</span>

                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="var(--muted)"
                                stroke-width="2"
                            >
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                                <polyline points="22 4 12 14.01 9 11.01" />
                            </svg>
                        </div>

                        <div class="progress-wrap">
                            <div class="progress-head">
                                <span>Progress</span>
                                <strong>{{ completionPercent }}%</strong>
                            </div>

                            <div class="progress-track">
                                <div
                                    class="progress-fill"
                                    :style="{ width: completionPercent + '%' }"
                                ></div>
                            </div>
                        </div>

                        <ul class="checklist">
                            <li
                                v-for="item in completionItems"
                                :key="item.label"
                            >
                                <span
                                    class="check-icon"
                                    :class="{ done: item.value }"
                                >
                                    <svg
                                        v-if="item.value"
                                        width="10"
                                        height="10"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    >
                                        <polyline points="20 6 9 17 4 12" />
                                    </svg>

                                    <svg
                                        v-else
                                        width="10"
                                        height="10"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="3"
                                    >
                                        <line x1="18" y1="6" x2="6" y2="18" />
                                        <line x1="6" y1="6" x2="18" y2="18" />
                                    </svg>
                                </span>

                                <span :class="{ muted: !item.value }">
                                    {{ item.label }}
                                </span>
                            </li>
                        </ul>

                        <button
                            v-if="!editMode && completionPercent < 100"
                            type="button"
                            class="pcard-link-btn"
                            @click="startEdit"
                        >
                            Lengkapi profil →
                        </button>
                    </section>

                    <section class="pcard">
                        <div class="pcard-head">
                            <span class="pcard-title">Ringkasan Akademik</span>

                            <svg
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="var(--muted)"
                                stroke-width="2"
                            >
                                <rect x="3" y="3" width="7" height="7" />
                                <rect x="14" y="3" width="7" height="7" />
                                <rect x="14" y="14" width="7" height="7" />
                                <rect x="3" y="14" width="7" height="7" />
                            </svg>
                        </div>

                        <div class="stat-row">
                            <div class="stat-box">
                                <p class="stat-box-val">
                                    {{ profile?.semester || '—' }}
                                </p>
                                <p class="stat-box-label">Semester</p>
                            </div>

                            <div class="stat-box">
                                <p class="stat-box-val">
                                    {{ profile?.role === 'admin' ? 'Admin' : 'Aktif' }}
                                </p>
                                <p class="stat-box-label">Status</p>
                            </div>
                        </div>
                    </section>
                </aside>

                <main class="profile-col-right">
                    <template v-if="!editMode">
                        <section class="pcard">
                            <p class="pcard-title">Informasi Pribadi</p>

                            <div class="info-grid">
                                <div class="info-item">
                                    <p class="info-label">Nama Lengkap</p>
                                    <p class="info-val">{{ profile?.nama || '—' }}</p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Nomor Induk Mahasiswa</p>
                                    <p class="info-val">{{ profile?.nim || '—' }}</p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Fakultas / Jurusan</p>
                                    <p class="info-val">{{ profile?.jurusan_nama || '—' }}</p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Program Studi</p>
                                    <p class="info-val">
                                        {{ profile?.jurusan_nama ? 'S1 ' + profile.jurusan_nama : '—' }}
                                    </p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Tahun Angkatan</p>
                                    <p class="info-val">
                                        {{ profile?.created_at ? new Date(profile.created_at).getFullYear() : '—' }}
                                    </p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Status Akademik</p>
                                    <p class="info-val status-aktif">● Aktif</p>
                                </div>
                            </div>
                        </section>

                        <section class="pcard">
                            <p class="pcard-title">Kontak &amp; Akun</p>

                            <div class="info-grid">
                                <div class="info-item">
                                    <p class="info-label">Email Institusi</p>
                                    <p class="info-val icon-row">
                                        <svg
                                            width="13"
                                            height="13"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <rect x="2" y="4" width="20" height="16" rx="2" />
                                            <path d="m22 7-10 7L2 7" />
                                        </svg>

                                        {{ institutionEmail }}
                                    </p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Email Pribadi</p>
                                    <p class="info-val icon-row">
                                        <svg
                                            width="13"
                                            height="13"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <rect x="2" y="4" width="20" height="16" rx="2" />
                                            <path d="m22 7-10 7L2 7" />
                                        </svg>

                                        {{ profile?.email || '—' }}
                                    </p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Nomor Telepon</p>
                                    <p class="info-val icon-row">
                                        <svg
                                            width="13"
                                            height="13"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.72 19.72 0 0 1 3.08 4.18 2 2 0 0 1 5.07 2h3a2 2 0 0 1 2 1.72 12.05 12.05 0 0 0 .67 2.81 2 2 0 0 1-.45 2.11L9.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.05 12.05 0 0 0 2.81.67A2 2 0 0 1 23 16.92z" />
                                        </svg>

                                        {{ profile?.no_telfon || profile?.telepon || '—' }}
                                    </p>
                                </div>

                                <div class="info-item">
                                    <p class="info-label">Alamat</p>
                                    <p class="info-val icon-row">
                                        <svg
                                            width="13"
                                            height="13"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="2"
                                        >
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>

                                        {{ profile?.alamat || '—' }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        <section class="pcard pcard-sertif">
                            <div class="sertif-icon">
                                <svg
                                    width="22"
                                    height="22"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="white"
                                    stroke-width="2"
                                >
                                    <circle cx="12" cy="8" r="6" />
                                    <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11" />
                                </svg>
                            </div>

                            <div class="sertif-text">
                                <p class="sertif-title">Sertifikasi &amp; Pencapaian</p>
                                <p class="sertif-sub">
                                    Kelola sertifikat akademik dan non-akademik kamu di sini.
                                </p>
                            </div>

                            <button type="button" class="ph-btn-outline-sm">
                                Kelola Sertifikat
                            </button>
                        </section>
                    </template>

                    <section v-else class="pcard">
                        <div class="edit-head">
                            <div>
                                <p class="pcard-title">Edit Profil</p>
                                <p class="edit-subtitle">
                                    Nomor telepon dan alamat bersifat opsional.
                                </p>
                            </div>

                            <div class="edit-actions-top">
                                <button
                                    type="button"
                                    class="ph-btn ph-btn-ghost"
                                    :disabled="form.processing"
                                    @click="cancelEdit"
                                >
                                    Batal
                                </button>

                                <button
                                    type="button"
                                    class="ph-btn ph-btn-primary"
                                    :disabled="form.processing"
                                    @click="submit"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                                </button>
                            </div>
                        </div>

                        <form class="edit-form" @submit.prevent="submit">
                            <div class="form-grid">
                                <div class="ph-form-row">
                                    <label class="ph-label">Nama Lengkap</label>
                                    <input
                                        v-model="form.nama"
                                        class="ph-input"
                                        required
                                        placeholder="Nama lengkap"
                                    />
                                    <p v-if="form.errors.nama" class="error">
                                        {{ form.errors.nama }}
                                    </p>
                                </div>

                                <div class="ph-form-row">
                                    <label class="ph-label">NIM</label>
                                    <input
                                        v-model="form.nim"
                                        class="ph-input"
                                        placeholder="Nomor Induk Mahasiswa"
                                    />
                                    <p v-if="form.errors.nim" class="error">
                                        {{ form.errors.nim }}
                                    </p>
                                </div>

                                <div class="ph-form-row">
                                    <label class="ph-label">Jurusan</label>
                                    <select
                                        v-model="form.jurusan_id"
                                        class="ph-input"
                                        :required="profile?.role !== 'admin'"
                                    >
                                        <option value="">
                                            Pilih jurusan
                                        </option>

                                        <option
                                            v-for="item in jurusan"
                                            :key="item.id"
                                            :value="item.id"
                                        >
                                            {{ item.nama }}{{ item.kode ? ` (${item.kode})` : '' }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.jurusan_id" class="error">
                                        {{ form.errors.jurusan_id }}
                                    </p>
                                </div>

                                <div class="ph-form-row">
                                    <label class="ph-label">Semester</label>
                                    <input
                                        v-model="form.semester"
                                        class="ph-input"
                                        type="number"
                                        min="1"
                                        max="14"
                                        placeholder="1–14"
                                    />
                                    <p v-if="form.errors.semester" class="error">
                                        {{ form.errors.semester }}
                                    </p>
                                </div>

                                <div class="ph-form-row">
                                    <label class="ph-label">Email Pribadi</label>
                                    <input
                                        v-model="form.email"
                                        class="ph-input"
                                        type="email"
                                        placeholder="email@gmail.com"
                                    />
                                    <p v-if="form.errors.email" class="error">
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <div class="ph-form-row">
                                    <label class="ph-label">
                                        Nomor Telepon <span>Opsional</span>
                                    </label>
                                    <input
                                        v-model="form.no_telfon"
                                        class="ph-input"
                                        type="tel"
                                        inputmode="numeric"
                                        placeholder="08xxxxxxxxxx"
                                        @input="onlyNumber"
                                    />
                                    <p v-if="form.errors.no_telfon" class="error">
                                        {{ form.errors.no_telfon }}
                                    </p>
                                    <p v-if="form.errors.telepon" class="error">
                                        {{ form.errors.telepon }}
                                    </p>
                                </div>

                                <div class="ph-form-row full">
                                    <label class="ph-label">
                                        Alamat <span>Opsional</span>
                                    </label>
                                    <textarea
                                        v-model="form.alamat"
                                        class="ph-input ph-textarea"
                                        rows="3"
                                        placeholder="Jl. ..."
                                    ></textarea>
                                    <p v-if="form.errors.alamat" class="error">
                                        {{ form.errors.alamat }}
                                    </p>
                                </div>
                            </div>

                            <div class="edit-actions-bottom">
                                <button
                                    type="button"
                                    class="ph-btn ph-btn-ghost"
                                    :disabled="form.processing"
                                    @click="cancelEdit"
                                >
                                    Batal
                                </button>

                                <button
                                    type="submit"
                                    class="ph-btn ph-btn-primary"
                                    :disabled="form.processing"
                                >
                                    {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                                </button>
                            </div>
                        </form>
                    </section>
                </main>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.profile-page {
    display: flex;
    flex-direction: column;
    gap: 24px;
    padding-bottom: 28px;
}

.profile-hero {
    position: relative;
    border: 1px solid var(--border);
    border-radius: 24px;
    background: var(--surface);
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.04);

    /*
     * Jangan hidden, supaya avatar tidak kepotong.
     */
    overflow: visible;
}

.profile-hero-bg {
    height: 120px;
    border-radius: 24px 24px 0 0;
    background:
        radial-gradient(circle at top left, rgba(255, 255, 255, 0.24), transparent 34%),
        linear-gradient(135deg, #0f766e, #0d9488 48%, #14b8a6);
}

.profile-hero-content {
    position: relative;
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 22px 28px 26px;
}

.profile-avatar-wrap {
    position: relative;
    flex-shrink: 0;

    /*
     * Avatar naik sedikit, tapi tidak sampai keluar terlalu jauh.
     */
    margin-top: -72px;
}

.profile-avatar-img,
.profile-avatar-fallback {
    width: 118px;
    height: 118px;
    border: 6px solid var(--surface);
    border-radius: 30px;
    box-shadow: 0 18px 38px rgba(15, 23, 42, 0.18);
}

.profile-avatar-img {
    object-fit: cover;
    background: #e2e8f0;
}

.profile-avatar-fallback {
    display: grid;
    place-items: center;
    background: linear-gradient(135deg, #ccfbf1, #99f6e4);
    color: #0f766e;
    font-size: 38px;
    font-weight: 900;
}

.avatar-upload-btn {
    position: absolute;
    right: -6px;
    bottom: -6px;
    display: grid;
    place-items: center;
    width: 36px;
    height: 36px;
    border: 3px solid var(--surface);
    border-radius: 999px;
    background: #0d9488;
    color: #ffffff;
    cursor: pointer;
}

.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
}

.profile-hero-info {
    flex: 1;
    min-width: 0;
}

.profile-badge {
    display: inline-flex;
    margin: 0 0 7px;
    padding: 5px 9px;
    border-radius: 999px;
    background: #ecfdf5;
    color: #047857;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
}

.profile-name {
    margin: 0;
    color: var(--text);
    font-size: 29px;
    font-weight: 850;
    line-height: 1.15;
}

.profile-sub {
    display: flex;
    align-items: center;
    gap: 7px;
    margin: 8px 0 0;
    color: var(--muted);
    font-size: 13px;
}

.profile-hero-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: auto;
}

.ph-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 40px;
    padding: 10px 14px;
    border-radius: 12px;
    border: 1px solid transparent;
    cursor: pointer;
    font-weight: 750;
    font-size: 13px;
    transition: 0.2s ease;
}

.ph-btn:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

.ph-btn-primary {
    background: #0d9488;
    color: #ffffff;
}

.ph-btn-primary:hover:not(:disabled) {
    background: #0f766e;
}

.ph-btn-ghost {
    border-color: var(--border);
    background: var(--surface);
    color: var(--text);
}

.ph-btn-ghost:hover:not(:disabled) {
    background: var(--background);
}

.profile-body {
    display: grid;
    grid-template-columns: 320px minmax(0, 1fr);
    gap: 22px;
}

.profile-col-left,
.profile-col-right {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.pcard {
    border: 1px solid var(--border);
    border-radius: 20px;
    background: var(--surface);
    padding: 20px;
    box-shadow: 0 14px 32px rgba(15, 23, 42, 0.04);
}

.pcard-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.pcard-title {
    margin: 0;
    color: var(--text);
    font-size: 15px;
    font-weight: 850;
}

.progress-wrap {
    margin-top: 16px;
}

.progress-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 7px;
    color: var(--muted);
    font-size: 12px;
}

.progress-head strong {
    color: #0d9488;
    font-size: 14px;
}

.progress-track {
    height: 9px;
    overflow: hidden;
    border-radius: 999px;
    background: var(--border);
}

.progress-fill {
    height: 100%;
    border-radius: 999px;
    background: #0d9488;
    transition: width 0.35s ease;
}

.checklist {
    display: flex;
    flex-direction: column;
    gap: 9px;
    list-style: none;
    padding: 0;
    margin: 16px 0 0;
}

.checklist li {
    display: flex;
    align-items: center;
    gap: 9px;
    color: var(--text);
    font-size: 13px;
}

.check-icon {
    display: grid;
    place-items: center;
    width: 19px;
    height: 19px;
    flex-shrink: 0;
    border-radius: 999px;
    background: var(--border);
    color: var(--muted);
}

.check-icon.done {
    background: #ccfbf1;
    color: #0d9488;
}

.muted {
    color: var(--muted);
}

.pcard-link-btn {
    margin-top: 16px;
    padding: 0;
    border: none;
    background: transparent;
    color: #0d9488;
    cursor: pointer;
    font-size: 13px;
    font-weight: 750;
}

.stat-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 10px;
    margin-top: 14px;
}

.stat-box {
    padding: 14px;
    border-radius: 15px;
    background: var(--background);
}

.stat-box-val {
    margin: 0;
    color: var(--text);
    font-size: 20px;
    font-weight: 850;
}

.stat-box-label {
    margin: 4px 0 0;
    color: var(--muted);
    font-size: 12px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
    margin-top: 16px;
}

.info-item {
    padding: 15px;
    border: 1px solid var(--border);
    border-radius: 15px;
    background: var(--background);
}

.info-label {
    margin: 0 0 6px;
    color: var(--muted);
    font-size: 12px;
}

.info-val {
    margin: 0;
    color: var(--text);
    font-size: 14px;
    font-weight: 750;
    word-break: break-word;
}

.icon-row {
    display: flex;
    align-items: center;
    gap: 7px;
}

.status-aktif {
    color: #0d9488;
}

.pcard-sertif {
    display: flex;
    align-items: center;
    gap: 14px;
}

.sertif-icon {
    display: grid;
    place-items: center;
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    border-radius: 16px;
    background: linear-gradient(135deg, #0d9488, #14b8a6);
}

.sertif-text {
    flex: 1;
    min-width: 0;
}

.sertif-title {
    margin: 0;
    color: var(--text);
    font-size: 15px;
    font-weight: 850;
}

.sertif-sub {
    margin: 4px 0 0;
    color: var(--muted);
    font-size: 12px;
}

.ph-btn-outline-sm {
    padding: 9px 12px;
    border: 1px solid #99f6e4;
    border-radius: 11px;
    background: #f0fdfa;
    color: #0f766e;
    cursor: pointer;
    font-size: 12px;
    font-weight: 800;
}

.edit-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 18px;
}

.edit-subtitle {
    margin: 6px 0 0;
    color: var(--muted);
    font-size: 13px;
}

.edit-actions-top,
.edit-actions-bottom {
    display: flex;
    align-items: center;
    gap: 10px;
}

.edit-actions-bottom {
    justify-content: flex-end;
    margin-top: 18px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}

.ph-form-row {
    display: flex;
    flex-direction: column;
    gap: 7px;
}

.ph-form-row.full {
    grid-column: 1 / -1;
}

.ph-label {
    color: var(--text);
    font-size: 12px;
    font-weight: 800;
}

.ph-label span {
    color: var(--muted);
    font-weight: 600;
}

.ph-input {
    width: 100%;
    min-height: 43px;
    border: 1px solid var(--border);
    border-radius: 12px;
    background: var(--background);
    color: var(--text);
    padding: 10px 12px;
    outline: none;
    font: inherit;
    font-size: 14px;
    transition: 0.2s ease;
}

.ph-input:focus {
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.12);
}

.ph-textarea {
    resize: vertical;
    min-height: 88px;
}

.error {
    margin: 0;
    color: #dc2626;
    font-size: 12px;
}

@media (max-width: 980px) {
    .profile-body {
        grid-template-columns: 1fr;
    }

    .profile-hero-content {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .profile-hero-actions {
        width: 100%;
        margin-left: 0;
    }
}

@media (max-width: 640px) {
    .profile-hero-bg {
        height: 95px;
    }

    .profile-hero-content {
        padding: 18px;
        gap: 14px;
    }

    .profile-avatar-wrap {
        margin-top: -58px;
    }

    .profile-avatar-img,
    .profile-avatar-fallback {
        width: 92px;
        height: 92px;
        border-radius: 22px;
    }

    .profile-name {
        font-size: 23px;
    }

    .profile-sub {
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .info-grid,
    .form-grid,
    .stat-row {
        grid-template-columns: 1fr;
    }

    .edit-head {
        flex-direction: column;
    }

    .edit-actions-top,
    .edit-actions-bottom,
    .profile-hero-actions {
        width: 100%;
    }

    .ph-btn {
        flex: 1;
    }

    .pcard-sertif {
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
