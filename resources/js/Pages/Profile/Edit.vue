<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    profile: { type: Object, default: null },
    jurusan: { type: Array, default: () => [] },
})

const editMode = ref(false)
const avatarPreview = ref(null)

const form = useForm({
    nama: props.profile?.nama || '',
    nim: props.profile?.nim || '',
    jurusan_id: props.profile?.jurusan_id || '',
    semester: props.profile?.semester || '',
    avatar: null,
})

const initials = computed(() => {
    const name = props.profile?.nama || 'M'
    return name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
})

function onAvatarChange(e) {
    const file = e.target.files[0]
    if (!file) return
    form.avatar = file
    avatarPreview.value = URL.createObjectURL(file)
}

function submit() {
    form.post('/profile', {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => { editMode.value = false },
    })
}

function cancelEdit() {
    form.reset()
    form.nama = props.profile?.nama || ''
    form.nim = props.profile?.nim || ''
    form.jurusan_id = props.profile?.jurusan_id || ''
    form.semester = props.profile?.semester || ''
    form.avatar = null
    avatarPreview.value = null
    editMode.value = false
}
</script>

<template>
    <Head title="Profil Mahasiswa" />

    <AppLayout>
        <div class="profile-page">

            <!-- HERO BANNER -->
            <div class="profile-hero">
                <div class="profile-hero-bg"></div>
                <div class="profile-hero-content">
                    <div class="profile-avatar-wrap">
                        <img
                            v-if="avatarPreview || profile?.avatar_url"
                            :src="avatarPreview || profile.avatar_url"
                            :alt="profile?.nama"
                            class="profile-avatar-img"
                        />
                        <div v-else class="profile-avatar-fallback">{{ initials }}</div>

                        <label v-if="editMode" class="avatar-upload-btn" title="Ganti foto">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            <input type="file" accept="image/*" class="sr-only" @change="onAvatarChange" />
                        </label>
                    </div>

                    <div class="profile-hero-info">
                        <h1 class="profile-name">{{ profile?.nama || '—' }}</h1>
                        <p class="profile-sub">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                            {{ profile?.nim || '—' }} · {{ profile?.jurusan_nama || 'Jurusan belum dipilih' }}
                        </p>
                    </div>

                    <div class="profile-hero-actions">
                        <button v-if="!editMode" class="ph-btn ph-btn-primary" @click="editMode = true">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit Profil
                        </button>
                        <template v-else>
                            <button class="ph-btn ph-btn-primary" :disabled="form.processing" @click="submit">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                            </button>
                            <button class="ph-btn ph-btn-ghost" @click="cancelEdit">Batal</button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- BODY -->
            <div class="profile-body">

                <!-- Kolom Kiri: Statistik -->
                <div class="profile-col-left">
                    <div class="pcard">
                        <div class="pcard-head">
                            <span class="pcard-title">Statistik Akademik</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        </div>

                        <div class="stat-ipk-row">
                            <div>
                                <p class="stat-label">IPK Saat Ini</p>
                                <p class="stat-ipk">3.57</p>
                            </div>
                            <div class="stat-grade-badge">—</div>
                        </div>

                        <div class="stat-row">
                            <div class="stat-box">
                                <p class="stat-box-val">144</p>
                                <p class="stat-box-label">SKS Lulus</p>
                            </div>
                            <div class="stat-box">
                                <p class="stat-box-val">{{ profile?.semester || '—' }}</p>
                                <p class="stat-box-label">Semester</p>
                            </div>
                        </div>

                        <a href="#" class="pcard-link">Lihat Transkrip Nilai →</a>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="profile-col-right">

                    <!-- Informasi Pribadi -->
                    <div class="pcard">
                        <p class="pcard-title">Informasi Pribadi</p>

                        <div v-if="!editMode" class="info-grid">
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
                                <p class="info-val">{{ profile?.jurusan_nama ? 'S1 ' + profile.jurusan_nama : '—' }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Tahun Angkatan</p>
                                <p class="info-val">{{ profile?.created_at ? new Date(profile.created_at).getFullYear() : '—' }}</p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Status Akademik</p>
                                <p class="info-val status-aktif">● Aktif</p>
                            </div>
                        </div>

                        <!-- Form Edit -->
                        <form v-else @submit.prevent="submit" class="edit-form">
                            <div class="form-grid">
                                <div class="ph-form-row">
                                    <label class="ph-label">Nama Lengkap</label>
                                    <input v-model="form.nama" class="ph-input" required />
                                    <p v-if="form.errors.nama" class="error">{{ form.errors.nama }}</p>
                                </div>
                                <div class="ph-form-row">
                                    <label class="ph-label">NIM</label>
                                    <input v-model="form.nim" class="ph-input" placeholder="Nomor Induk Mahasiswa" />
                                    <p v-if="form.errors.nim" class="error">{{ form.errors.nim }}</p>
                                </div>
                                <div class="ph-form-row">
                                    <label class="ph-label">Jurusan</label>
                                    <select v-model="form.jurusan_id" class="ph-input" :required="profile?.role !== 'admin'">
                                        <option value="" disabled>Pilih jurusan</option>
                                        <option v-for="item in jurusan" :key="item.id" :value="item.id">
                                            {{ item.nama }}{{ item.kode ? ` (${item.kode})` : '' }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.jurusan_id" class="error">{{ form.errors.jurusan_id }}</p>
                                </div>
                                <div class="ph-form-row">
                                    <label class="ph-label">Semester</label>
                                    <input v-model="form.semester" class="ph-input" type="number" min="1" max="14" placeholder="1–14" />
                                    <p v-if="form.errors.semester" class="error">{{ form.errors.semester }}</p>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Kontak & Akun -->
                    <div v-if="!editMode" class="pcard">
                        <p class="pcard-title">Kontak &amp; Akun</p>
                        <div class="info-grid">
                            <div class="info-item">
                                <p class="info-label">Email Institusi</p>
                                <p class="info-val icon-row">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/></svg>
                                    {{ profile?.nim ? profile.nim.toLowerCase() + '@student.campushub.ac.id' : '—' }}
                                </p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Email Pribadi</p>
                                <p class="info-val icon-row">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 7L2 7"/></svg>
                                    {{ profile?.email || '—' }}
                                </p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Nomor Telepon</p>
                                <p class="info-val icon-row">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2A19.72 19.72 0 0 1 3.08 4.18 2 2 0 0 1 5.07 2h3a2 2 0 0 1 2 1.72 12.05 12.05 0 0 0 .67 2.81 2 2 0 0 1-.45 2.11L9.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.05 12.05 0 0 0 2.81.67A2 2 0 0 1 23 16.92z"/></svg>
                                    {{ profile?.telepon || '—' }}
                                </p>
                            </div>
                            <div class="info-item">
                                <p class="info-label">Alamat</p>
                                <p class="info-val icon-row">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                    {{ profile?.alamat || '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Sertifikasi -->
                    <div v-if="!editMode" class="pcard pcard-sertif">
                        <div class="sertif-icon">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
                        </div>
                        <div class="sertif-text">
                            <p class="sertif-title">Sertifikasi &amp; Pencapaian</p>
                            <p class="sertif-sub">Kelola sertifikat akademik dan non-akademik kamu di sini.</p>
                        </div>
                        <button class="ph-btn-outline-sm">Kelola Sertifikat</button>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>
