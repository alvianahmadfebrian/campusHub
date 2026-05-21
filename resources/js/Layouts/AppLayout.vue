<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

function logout() {
    router.post('/logout')
}

function isActive(path) {
    return window.location.pathname === path
}
</script>

<template>
    <div>
        <nav class="nav">
            <div class="nav-inner">
                <Link href="/dashboard" style="font-weight: 800; font-size: 20px;">CampusHub</Link>
                <div class="nav-links">
                    <Link href="/dashboard" class="nav-link" :class="{ active: isActive('/dashboard') }">Dashboard</Link>
                    <Link href="/pengumuman" class="nav-link" :class="{ active: isActive('/pengumuman') }">Pengumuman</Link>
                    <Link href="/materi" class="nav-link" :class="{ active: isActive('/materi') }">Materi</Link>
                    <Link href="/events" class="nav-link" :class="{ active: isActive('/events') }">Event</Link>
                    <Link href="/profile" class="nav-link" :class="{ active: isActive('/profile') }">Profil</Link>
                    <span class="muted" v-if="user">{{ user.email }}</span>
                    <button class="btn secondary" @click="logout">Logout</button>
                </div>
            </div>
        </nav>

        <main class="container">
            <div v-if="page.props.flash?.success" class="flash">
                {{ page.props.flash.success }}
            </div>
            <slot />
        </main>
    </div>
</template>
