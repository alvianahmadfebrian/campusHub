<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({ profile: Object })

const form = useForm({
    nama: props.profile?.nama || '',
    nim: props.profile?.nim || '',
    jurusan: props.profile?.jurusan || '',
    semester: props.profile?.semester || '',
    avatar: null,
})

function submit() {
    form.post('/profile', { forceFormData: true })
}
</script>

<template>
    <Head title="Profil" />
    <AppLayout>
        <h1 class="title">Profil Mahasiswa</h1>
        <p class="muted">Data profil disimpan di Supabase Database, foto disimpan di Supabase Storage.</p>

        <div class="card" style="margin-top: 22px; max-width: 720px;">
            <div v-if="profile?.avatar_url" style="margin-bottom: 18px;">
                <img :src="profile.avatar_url" style="width:96px;height:96px;border-radius:999px;object-fit:cover;" />
            </div>

            <form @submit.prevent="submit">
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
                    <label class="label">Foto Profil</label>
                    <input class="input" type="file" accept="image/*" @input="form.avatar = $event.target.files[0]" />
                    <div v-if="form.errors.avatar" class="error">{{ form.errors.avatar }}</div>
                </div>

                <button class="btn" :disabled="form.processing">Simpan Profil</button>
            </form>
        </div>
    </AppLayout>
</template>
