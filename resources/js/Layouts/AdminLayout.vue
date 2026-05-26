<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { useDarkMode } from '@/composables/useDarkMode'

const page = usePage()
const { isDark, toggleDark } = useDarkMode()

const sidebarOpen = ref(false)

const user = computed(() => page.props.auth?.user ?? {})
const flash = computed(() => page.props.flash ?? {})
const currentPath = computed(() => page.url.split('?')[0])

function isActive(path) {
    return currentPath.value === path || currentPath.value.startsWith(`${path}/`)
}

function openSidebar() { sidebarOpen.value = true }
function closeSidebar() { sidebarOpen.value = false }

function logout() {
    closeSidebar()
    router.post('/logout')
}

watch(() => page.url, () => { closeSidebar() })

watch(sidebarOpen, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : ''
})

onBeforeUnmount(() => {
    document.body.style.overflow = ''
})
</script>

<template>
    <div class="admin-shell">
        <!-- OVERLAY MOBILE -->
        <transition name="sidebar-fade">
            <div
                v-if="sidebarOpen"
                class="admin-sidebar-overlay"
                aria-hidden="true"
                @click="closeSidebar"
            ></div>
        </transition>

        <!-- SIDEBAR -->
        <aside class="admin-sidebar" :class="{ 'mobile-open': sidebarOpen }">
            <button type="button" class="admin-sidebar-close" aria-label="Tutup menu navigasi" @click="closeSidebar">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18"/><path d="M6 6l12 12"/>
                </svg>
            </button>

            <!-- BRAND -->
            <div class="admin-brand">
                <div class="admin-brand-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
                <div class="admin-brand-texts">
                    <p class="admin-brand-name">CampusHub</p>
                    <p class="admin-brand-sub">Portal Akademik</p>
                </div>
            </div>

            <!-- NAVIGATION -->
            <nav class="admin-nav">
                <Link href="/admin/dashboard" class="admin-nav-item" :class="{ active: isActive('/admin/dashboard') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Dashboard
                </Link>
                <Link href="/admin/akademik" class="admin-nav-item" :class="{ active: isActive('/admin/akademik') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                    Akademik
                </Link>
                <Link href="/admin/jadwal" class="admin-nav-item" :class="{ active: isActive('/admin/jadwal') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Jadwal Kuliah
                </Link>
                <Link href="/admin/konten" class="admin-nav-item" :class="{ active: isActive('/admin/konten') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    Manajemen Konten
                </Link>
                <Link href="/drive" class="admin-nav-item" :class="{ active: isActive('/drive') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7z"/></svg>
                    Drive
                </Link>
                <Link href="/chat" class="admin-nav-item" :class="{ active: isActive('/chat') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9l-5 3v-3H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><path d="M8 10h8"/><path d="M8 14h5"/></svg>
                    Chatbot AI
                </Link>
                <Link href="/admin/laporan" class="admin-nav-item" :class="{ active: isActive('/admin/laporan') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Laporan
                </Link>
                <Link href="/admin/pengaturan" class="admin-nav-item" :class="{ active: isActive('/admin/pengaturan') }" @click="closeSidebar">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    Pengaturan
                </Link>
            </nav>

            <!-- FOOTER -->
            <div class="admin-sidebar-footer">

                <!-- DARK MODE TOGGLE -->
                <button type="button" class="admin-footer-link admin-theme-toggle" @click="toggleDark">
                    <svg v-if="isDark" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"/>
                        <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke-linecap="round"/>
                    </svg>
                    <svg v-else width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke-linejoin="round"/>
                    </svg>
                    {{ isDark ? 'Mode Terang' : 'Mode Gelap' }}
                </button>


                <Link href="/dashboard" class="admin-footer-link" @click="closeSidebar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    Portal Mahasiswa
                </Link>
                <button type="button" class="admin-footer-link admin-logout-btn" @click="logout">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="admin-main">
            <!-- TOPBAR -->
            <header class="admin-topbar">
                <button type="button" class="admin-mobile-menu-btn" aria-label="Buka menu navigasi" :aria-expanded="sidebarOpen" @click="openSidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 6h16"/><path d="M4 12h16"/><path d="M4 18h16"/>
                    </svg>
                </button>

                <div class="admin-search">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" placeholder="Cari mahasiswa, materi, atau event..." />
                </div>

                <div class="admin-topbar-right">
                    <!-- DARK MODE TOGGLE -->
                    <button
                        type="button"
                        class="admin-icon-btn"
                        :title="isDark ? 'Mode Terang' : 'Mode Gelap'"
                        @click="toggleDark"
                    >
                        <svg v-if="isDark" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="5"/>
                            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke-linecap="round"/>
                        </svg>
                        <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button type="button" class="admin-icon-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                        </svg>
                        <span class="admin-notif-dot"></span>
                    </button>

                    <div class="admin-user-pill">
                        <div class="admin-user-avatar">
                            <img v-if="user.avatar || user.avatar_url" :src="user.avatar || user.avatar_url" alt="avatar" />
                            <span v-else>{{ (user.nama || 'A').charAt(0).toUpperCase() }}</span>
                        </div>
                        <div class="admin-user-meta">
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

            <!-- PAGE CONTENT -->
            <main class="admin-body">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Mobile-only styles yang tidak bisa di-handle dari admin.css global */
.admin-mobile-menu-btn,
.admin-sidebar-close { display: none; }
.admin-sidebar-overlay { display: none; }

.sidebar-fade-enter-active,
.sidebar-fade-leave-active { transition: opacity 0.2s ease; }
.sidebar-fade-enter-from,
.sidebar-fade-leave-to { opacity: 0; }

@media (max-width: 900px) {
    .admin-shell { display: block !important; min-height: 100vh; }

    .admin-mobile-menu-btn {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 39px;
        height: 39px;
        border: 1px solid var(--admin-border, #e2e8f0);
        border-radius: 11px;
        background: var(--admin-surface, #ffffff);
        color: var(--admin-text, #334155);
        cursor: pointer;
    }

    .admin-mobile-menu-btn:hover {
        border-color: #99f6e4;
        background: var(--admin-nav-active-bg, #f0fdfa);
        color: #0f766e;
    }

    .admin-sidebar-overlay {
        position: fixed;
        z-index: 999;
        inset: 0;
        display: block;
        background: rgba(15, 23, 42, 0.38);
        backdrop-filter: blur(2px);
    }

    .admin-sidebar {
        position: fixed !important;
        z-index: 1000;
        top: 0;
        left: 0;
        bottom: 0;
        display: flex !important;
        flex-direction: column;
        width: 265px !important;
        height: 100vh !important;
        overflow-y: auto;
        transform: translateX(-104%);
        transition: transform 0.25s ease;
    }

    .admin-sidebar.mobile-open {
        transform: translateX(0);
        box-shadow: 14px 0 42px rgba(15, 23, 42, 0.14);
    }

    .admin-sidebar-close {
        position: absolute;
        z-index: 2;
        top: 16px;
        right: 12px;
        display: grid;
        place-items: center;
        width: 35px;
        height: 35px;
        border: 1px solid var(--admin-border, #e2e8f0);
        border-radius: 10px;
        background: var(--admin-surface, #ffffff);
        color: var(--admin-muted, #475569);
        cursor: pointer;
    }

    .admin-sidebar-close:hover {
        background: var(--admin-nav-hover, #f1f5f9);
        color: var(--admin-text, #0f172a);
    }

    .admin-brand { padding-right: 54px !important; }
    .admin-main { width: 100% !important; min-width: 0; margin-left: 0 !important; }
    .admin-topbar { display: flex; align-items: center; gap: 10px; padding: 0 14px !important; }
    .admin-search { flex: 1; width: auto !important; max-width: none !important; min-width: 0; }
    .admin-search input { width: 100%; min-width: 0; }
    .admin-body { padding: 20px 16px !important; }
}

@media (max-width: 560px) {
    .admin-topbar { gap: 8px; padding: 0 10px !important; }
    .admin-mobile-menu-btn { width: 37px; height: 37px; }
    .admin-search input { font-size: 12px; }
    .admin-search input::placeholder { color: transparent; }
    .admin-user-pill { padding: 4px !important; gap: 0 !important; }
    .admin-user-meta { display: none; }
    .admin-body { padding: 18px 12px !important; }
}
</style>