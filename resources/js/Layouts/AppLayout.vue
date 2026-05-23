<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)
const isAdmin = computed(() => user.value?.role === 'admin')
const homeUrl = computed(() => (isAdmin.value ? '/admin/dashboard' : '/dashboard'))

function logout() {
    router.post('/logout')
}

function isActive(path) {
    return page.url === path || page.url.startsWith(`${path}?`)
}
</script>

<template>
    <div>
        <nav class="nav">
            <div class="nav-inner">
                <Link :href="homeUrl" class="brand">
                    <span class="brand-mark">C</span>
                    CampusHub
                </Link>

                <div class="nav-links">
                    <Link
                        :href="homeUrl"
                        class="nav-link"
                        :class="{ active: isActive(homeUrl) }"
                    >
                        {{ isAdmin ? 'Dashboard Admin' : 'Dashboard' }}
                    </Link>
                    <Link href="/pengumuman" class="nav-link" :class="{ active: isActive('/pengumuman') }">
                        Pengumuman
                    </Link>
                    <Link href="/materi" class="nav-link" :class="{ active: isActive('/materi') }">
                        Materi
                    </Link>
                    <Link href="/events" class="nav-link" :class="{ active: isActive('/events') }">
                        Event
                    </Link>
                    <Link href="/profile" class="nav-link" :class="{ active: isActive('/profile') }">
                        Profil
                    </Link>

                    <span v-if="user" class="user-pill">
                        {{ user.email }}
                    </span>

                    <button type="button" class="btn secondary small" @click="logout">
                        Logout
                    </button>
                </div>
            </div>
        </nav>

        <main class="container page-content">
            <div v-if="page.props.flash?.success" class="flash">
                {{ page.props.flash.success }}
            </div>

            <slot />
        </main>
    </div>
</template>
