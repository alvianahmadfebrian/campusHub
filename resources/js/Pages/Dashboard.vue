<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineProps({
    stats: Object,
    profile: Object,
    pengumuman: Array,
    materi: Array,
    events: Array,
})
</script>

<template>
    <Head title="Dashboard" />
    <AppLayout>
        <h1 class="title">Dashboard Mahasiswa</h1>
        <p class="muted">Ringkasan portal akademik berbasis cloud.</p>

        <div class="grid grid-3" style="margin-top: 22px;">
            <div class="card">
                <p class="muted">Pengumuman</p>
                <h2>{{ stats.pengumuman }}</h2>
            </div>
            <div class="card">
                <p class="muted">Materi</p>
                <h2>{{ stats.materi }}</h2>
            </div>
            <div class="card">
                <p class="muted">Event</p>
                <h2>{{ stats.events }}</h2>
            </div>
        </div>

        <div class="grid grid-2" style="margin-top: 22px;">
            <div class="card">
                <h2>Profil</h2>
                <p class="muted" v-if="!profile">Profil belum lengkap.</p>
                <div v-else>
                    <img v-if="profile.avatar_url" :src="profile.avatar_url" style="width:72px;height:72px;border-radius:999px;object-fit:cover;" />
                    <p><b>{{ profile.nama }}</b></p>
                    <p class="muted">{{ profile.nim }} · {{ profile.jurusan }} · Semester {{ profile.semester }}</p>
                </div>
                <Link class="btn secondary" href="/profile">Edit Profil</Link>
            </div>

            <div class="card">
                <h2>Pengumuman Terbaru</h2>
                <p v-if="pengumuman.length === 0" class="muted">Belum ada pengumuman.</p>
                <div v-for="item in pengumuman" :key="item.id" style="margin-bottom: 12px;">
                    <b>{{ item.judul }}</b>
                    <p class="muted">{{ item.kategori || 'Umum' }}</p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
