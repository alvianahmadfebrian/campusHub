<script setup>
import { Head, useForm } from '@inertiajs/vue3'
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

const form = useForm({
    nama: props.profile?.nama || '',
    nim: props.profile?.nim || '',
    jurusan_id: props.profile?.jurusan_id || '',
    semester: props.profile?.semester || '',
    avatar: null,
})

function submit() {
    form.post('/profile', {
        forceFormData: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Profil" />

    <AppLayout>
        <header class="page-header">
            <div>
                <span class="eyebrow">Akun Saya</span>
                <h1 class="title">Profil Mahasiswa</h1>
                <p class="muted">Perbarui data akademik dan foto profil.</p>
            </div>
        </header>

        <div class="card profile-form section-gap">
            <div class="profile-summary profile-preview">
                <div class="avatar-placeholder large">
                    <img v-if="profile?.avatar_url" :src="profile.avatar_url" :alt="profile.nama" />
                    <span v-else>{{ profile?.nama?.charAt(0)?.toUpperCase() || 'M' }}</span>
                </div>
                <div>
                    <strong>{{ profile?.nama || 'Mahasiswa' }}</strong>
                    <p class="muted">{{ profile?.nim || 'Lengkapi profil kamu.' }}</p>
                    <p class="muted">{{ profile?.jurusan_nama || 'Jurusan belum dipilih' }}</p>
                </div>
            </div>

            <form @submit.prevent="submit">
                <div class="grid grid-2">
                    <div class="form-row">
                        <label class="label" for="nama">Nama</label>
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
                        <label class="label" for="jurusan_id">Jurusan{{ profile?.role === 'admin' ? ' (opsional untuk admin)' : '' }}</label>
                        <select id="jurusan_id" v-model="form.jurusan_id" class="input" :required="profile?.role !== 'admin'">
                            <option value="" :disabled="profile?.role !== 'admin'">{{ profile?.role === 'admin' ? 'Tanpa jurusan' : 'Pilih jurusan' }}</option>
                            <option v-for="item in jurusan" :key="item.id" :value="item.id">
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
                    <label class="label" for="avatar">Foto Profil</label>
                    <input id="avatar" class="input file-input" type="file" accept="image/*" @input="form.avatar = $event.target.files[0]" />
                    <div v-if="form.errors.avatar" class="error">{{ form.errors.avatar }}</div>
                </div>

                <button class="btn" type="submit" :disabled="form.processing">
                    {{ form.processing ? 'Menyimpan...' : 'Simpan Profil' }}
                </button>
            </form>
        </div>
    </AppLayout>
</template>
