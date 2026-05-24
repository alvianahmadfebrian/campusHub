<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user || null)
const isAdmin = computed(() => user.value?.role === 'admin')
const homeUrl = computed(() => (isAdmin.value ? '/admin/dashboard' : '/dashboard'))

// Ambil nama & NIM dari props profile jika tersedia
const profile = computed(() => page.props.profile || null)
const displayName = computed(() => profile.value?.nama || user.value?.email || 'Mahasiswa')
const displayNim  = computed(() => profile.value?.nim  || '')
const avatarUrl   = computed(() => profile.value?.avatar_url || null)
const initials    = computed(() => {
    const n = displayName.value
    return n.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
})

function logout() { router.post('/logout') }
function isActive(path) {
    const currentPath = page.url.split('?')[0]

    return currentPath === path || currentPath.startsWith(`${path}/`)
}
</script>

<template>
    <div class="sl-shell">

        <!-- ── SIDEBAR ── -->
        <aside class="sl-sidebar">
            <!-- Brand -->
            <div class="sl-brand">
                <div class="sl-brand-icon">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M10 2L18 6V10C18 14.418 14.418 18 10 18C5.582 18 2 14.418 2 10V6L10 2Z" fill="white" fill-opacity="0.35"/>
                        <path d="M7 10L9.5 12.5L14 8" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div>
                    <p class="sl-brand-name">CampusHub</p>
                    <p class="sl-brand-sub">Student Portal</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="sl-nav">
                <Link :href="homeUrl" class="sl-nav-item" :class="{ active: isActive('/dashboard') || isActive('/admin/dashboard') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <rect x="2" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="11" y="2" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="2" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                        <rect x="11" y="11" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.5"/>
                    </svg>
                    Dashboard
                </Link>

                <Link href="/profile" class="sl-nav-item" :class="{ active: isActive('/profile') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="7" r="3.5" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M3 17c0-3.314 3.134-6 7-6s7 2.686 7 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Profil Mahasiswa
                </Link>

                <Link href="/pengumuman" class="sl-nav-item" :class="{ active: isActive('/pengumuman') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M4 4h12a1 1 0 011 1v8a1 1 0 01-1 1H4a1 1 0 01-1-1V5a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M7 8h6M7 11h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Pengumuman
                </Link>

                <Link href="/materi" class="sl-nav-item" :class="{ active: isActive('/materi') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M4 3h8l4 4v10a1 1 0 01-1 1H4a1 1 0 01-1-1V4a1 1 0 011-1z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M12 3v4h4M7 10h6M7 13h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Materi Kuliah
                </Link>

                <Link href="/events" class="sl-nav-item" :class="{ active: isActive('/events') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <rect x="2" y="4" width="16" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M6 2v4M14 2v4M2 9h16" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Event Kampus
                </Link>

                <Link href="/drive" class="sl-nav-item" :class="{ active: isActive('/drive') }">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                        <path d="M2.5 6a2 2 0 012-2h4l2 2H16a2 2 0 012 2v7.5a2 2 0 01-2 2H4.5a2 2 0 01-2-2V6z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    Drive
                </Link>

                <Link href="/chat" class="sl-nav-item" :class="{ active: isActive('/chat') }">
    <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
        <path
            d="M4 4.5h12a2 2 0 012 2v7a2 2 0 01-2 2H9l-4 2v-2H4a2 2 0 01-2-2v-7a2 2 0 012-2z"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linejoin="round"
        />
        <path
            d="M6.5 9h7M6.5 12h4.5"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
        />
    </svg>
    Chatbot
</Link>

            </nav>

            <!-- Footer -->
            <div class="sl-sidebar-footer">
                <a href="#" class="sl-footer-link">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M10 9v5M10 7h.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Help Center
                </a>
                <button @click="logout" class="sl-footer-link sl-logout-btn">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="none">
                        <path d="M13 15l4-5-4-5M17 10H7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 3H4a1 1 0 00-1 1v12a1 1 0 001 1h3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        <!-- ── MAIN ── -->
        <div class="sl-main">

            <!-- Topbar -->
            <header class="sl-topbar">
                <div class="sl-search">
                    <svg width="15" height="15" viewBox="0 0 20 20" fill="none">
                        <circle cx="9" cy="9" r="6" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M13.5 13.5L17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    <input type="text" placeholder="Cari " />
                </div>
                <div class="sl-topbar-right">
                    <button class="sl-icon-btn" title="Notifikasi">
                        <svg width="17" height="17" viewBox="0 0 20 20" fill="none">
                            <path d="M10 2a6 6 0 016 6c0 3.5 1 5 1 5H3s1-1.5 1-5a6 6 0 016-6z" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M8.5 16.5a1.5 1.5 0 003 0" stroke="currentColor" stroke-width="1.5"/>
                        </svg>
                    </button>
                    <button class="sl-icon-btn" title="Pengaturan">
                        <svg width="17" height="17" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="2" stroke="currentColor" stroke-width="1.5"/>
                            <path d="M10 2v2M10 16v2M2 10h2M16 10h2M4.22 4.22l1.42 1.42M14.36 14.36l1.42 1.42M4.22 15.78l1.42-1.42M14.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>
                    </button>
                    <div class="sl-user-pill">
                        <div class="sl-user-avatar">
                            <img v-if="avatarUrl" :src="avatarUrl" :alt="displayName" />
                            <span v-else>{{ initials }}</span>
                        </div>
                        <div class="sl-user-info">
                            <p class="sl-user-name">{{ displayName }}</p>
                            <p class="sl-user-nim">{{ displayNim }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="sl-content">
                <div v-if="page.props.flash?.success" class="sl-flash">
                    {{ page.props.flash.success }}
                </div>
                <slot />
            </main>
        </div>
    </div>
</template>

