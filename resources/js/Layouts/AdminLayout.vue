<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user ?? {})
const flash = computed(() => page.props.flash ?? {})

const currentPath = computed(() => page.url)

function isActive(path) {
    return currentPath.value.startsWith(path)
}

function logout() {
    router.post('/logout')
}
</script>

<template>
    <div class="admin-shell">
        <!-- SIDEBAR -->
        <aside class="admin-sidebar">
            <!-- Brand -->
            <div class="admin-brand">
                <div class="admin-brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                </div>
                <div class="admin-brand-texts">
                    <p class="admin-brand-name">CampusHub</p>
                    <p class="admin-brand-sub">Portal Akademik</p>
                </div>
            </div>

            <!-- Nav -->
            <nav class="admin-nav">
                <Link
                    href="/admin/dashboard"
                    class="admin-nav-item"
                    :class="{ active: isActive('/admin/dashboard') }"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </Link>

                <Link
                    href="/admin/akademik"
                    class="admin-nav-item"
                    :class="{ active: isActive('/admin/akademik') }"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    Akademik
                </Link>

                <Link
                    href="/admin/jadwal"
                    class="admin-nav-item"
                    :class="{ active: isActive('/admin/jadwal') }"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Jadwal Kuliah
                </Link>

                <Link
                    href="/admin/dashboard"
                    class="admin-nav-item"
                    :class="{ active: false }"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    Manajemen Konten
                </Link>

                <Link
                    href="/admin/laporan"
                    class="admin-nav-item"
                    :class="{ active: isActive('/admin/laporan') }"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Laporan
                </Link>

                <Link
                    href="/admin/pengaturan"
                    class="admin-nav-item"
                    :class="{ active: isActive('/admin/pengaturan') }"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Pengaturan
                </Link>
            </nav>

            <!-- Footer -->
            <div class="admin-sidebar-footer">
                <Link href="/dashboard" class="admin-footer-link">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Portal Mahasiswa
                </Link>
                <button class="admin-footer-link admin-logout-btn" @click="logout">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="admin-main">
            <!-- TOPBAR -->
            <header class="admin-topbar">
                <div class="admin-search">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" placeholder="Cari mahasiswa, materi, atau event..." />
                </div>
                <div class="admin-topbar-right">
                    <button class="admin-icon-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        <span class="admin-notif-dot"></span>
                    </button>
                    <div class="admin-user-pill">
                        <div class="admin-user-avatar">
                            <img v-if="user.avatar" :src="user.avatar" alt="avatar" />
                            <span v-else>{{ (user.nama || 'A').charAt(0).toUpperCase() }}</span>
                        </div>
                        <div>
                            <p class="admin-user-name">{{ user.nama || 'Administrator' }}</p>
                            <p class="admin-user-role">Admin CampusHub</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- FLASH -->
            <div v-if="flash.success" class="admin-flash" style="margin: 16px 28px 0;">
                {{ flash.success }}
            </div>

            <!-- CONTENT -->
            <div class="admin-body">
                <slot />
            </div>
        </div>
    </div>
</template>