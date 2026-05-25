<script setup>
import { Link, router, usePage } from '@inertiajs/vue3'
import { computed, onBeforeUnmount, ref, watch } from 'vue'
import { useDarkMode } from '@/composables/useDarkMode'

const page = usePage()
const { isDark, toggleDark } = useDarkMode()

const sidebarOpen = ref(false)

const authUser = computed(() => page.props.auth?.user ?? {})
const profile = computed(() => page.props.profile ?? page.props.auth?.profile ?? null)
const flash = computed(() => page.props.flash ?? {})
const currentPath = computed(() => page.url.split('?')[0])

const displayName = computed(() => {
    return profile.value?.nama
        || authUser.value?.nama
        || authUser.value?.email
        || 'Mahasiswa'
})

const displayNim = computed(() => {
    return profile.value?.nim || ''
})

const avatarUrl = computed(() => {
    return profile.value?.avatar_url || authUser.value?.avatar_url || null
})

const initials = computed(() => {
    return String(displayName.value)
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((item) => item.charAt(0))
        .join('')
        .toUpperCase() || 'M'
})

function isActive(path) {
    return currentPath.value === path || currentPath.value.startsWith(`${path}/`)
}

function openSidebar() {
    sidebarOpen.value = true
}

function closeSidebar() {
    sidebarOpen.value = false
}

function logout() {
    closeSidebar()
    router.post('/logout')
}

watch(
    () => page.url,
    () => {
        closeSidebar()
    }
)

watch(sidebarOpen, (opened) => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = opened ? 'hidden' : ''
    }
})

onBeforeUnmount(() => {
    if (typeof document !== 'undefined') {
        document.body.style.overflow = ''
    }
})
</script>

<template>
    <div class="student-shell">
        <!-- OVERLAY MOBILE -->
        <transition name="drawer-fade">
            <div
                v-if="sidebarOpen"
                class="student-overlay"
                @click="closeSidebar"
            ></div>
        </transition>

        <!-- SIDEBAR -->
        <aside
            class="student-sidebar"
            :class="{ open: sidebarOpen }"
        >
            <button
                type="button"
                class="sidebar-close"
                aria-label="Tutup menu"
                @click="closeSidebar"
            >
                <svg viewBox="0 0 24 24" fill="none">
                    <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                </svg>
            </button>

            <!-- BRAND -->
            <div class="student-brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path
                            d="M12 3 21 7.5 12 12 3 7.5 12 3Z"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linejoin="round"
                        />
                        <path
                            d="M7 10v5c3 2.7 7 2.7 10 0v-5"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linejoin="round"
                        />
                    </svg>
                </div>

                <div>
                    <p class="brand-name">CampusHub</p>
                    <p class="brand-subtitle">Student Portal</p>
                </div>
            </div>

            <!-- MENU -->
            <nav class="student-nav">
                <Link href="/dashboard" class="nav-item" :class="{ active: isActive('/dashboard') }" @click="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="3" width="7" height="7" stroke="currentColor" stroke-width="2" />
                        <rect x="14" y="3" width="7" height="7" stroke="currentColor" stroke-width="2" />
                        <rect x="3" y="14" width="7" height="7" stroke="currentColor" stroke-width="2" />
                        <rect x="14" y="14" width="7" height="7" stroke="currentColor" stroke-width="2" />
                    </svg>
                    Dashboard
                </Link>

                <Link href="/profile" class="nav-item" :class="{ active: isActive('/profile') }" @click="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="2" />
                        <path d="M4 21c0-4 3.6-7 8-7s8 3 8 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Profil Mahasiswa
                </Link>

                <Link href="/pengumuman" class="nav-item" :class="{ active: isActive('/pengumuman') }" @click="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 5h16v13H8l-4 3V5Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Pengumuman
                </Link>

                <Link href="/materi" class="nav-item" :class="{ active: isActive('/materi') }" @click="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M5 3h10l4 4v14H5V3Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <path d="M15 3v5h4M9 12h6M9 16h6" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Materi Kuliah
                </Link>

                <Link href="/events" class="nav-item" :class="{ active: isActive('/events') }" @click="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2" />
                        <path d="M8 3v5M16 3v5M3 10h18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Event Kampus
                </Link>

                <Link href="/drive" class="nav-item" :class="{ active: isActive('/drive') }" @click="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M3 7a2 2 0 0 1 2-2h5l2 2h7a2 2 0 0 1 2 2v10H3V7Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                    </svg>
                    Drive
                </Link>

                <Link href="/chat" class="nav-item" :class="{ active: isActive('/chat') }" @click="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 4h16a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H9l-5 3v-3a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <path d="M8 10h8M8 14h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Chatbot
                </Link>
            </nav>

            <!-- FOOTER -->
            <div class="sidebar-footer">
                <!-- DARK MODE TOGGLE -->
                <button type="button" class="footer-link theme-toggle" @click="toggleDark">
                    <svg v-if="isDark" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>

                    <svg v-else viewBox="0 0 24 24" fill="none">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                    </svg>

                    {{ isDark ? 'Mode Terang' : 'Mode Gelap' }}
                </button>

                <a href="#" class="footer-link" @click.prevent="closeSidebar">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2" />
                        <path d="M12 11v6M12 8h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                    Help Center
                </a>

                <button type="button" class="footer-link logout-button" @click="logout">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="2" />
                        <path d="m16 17 5-5-5-5M21 12H9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Logout
                </button>
            </div>
        </aside>

        <!-- MAIN -->
        <div class="student-main">
            <!-- TOPBAR -->
            <header class="student-topbar">
                <button
                    type="button"
                    class="mobile-menu-button"
                    aria-label="Buka menu"
                    :aria-expanded="sidebarOpen"
                    @click="openSidebar"
                >
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </button>

                <div class="student-search">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                        <path d="m20 20-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                    </svg>

                    <input type="text" placeholder="Cari" />
                </div>

                <div class="topbar-right">
                    <!-- DARK MODE TOGGLE -->
                    <button
                        type="button"
                        class="icon-button"
                        :title="isDark ? 'Mode Terang' : 'Mode Gelap'"
                        @click="toggleDark"
                    >
                        <svg v-if="isDark" viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 2v2M12 20v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M2 12h2M20 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                        </svg>

                        <svg v-else viewBox="0 0 24 24" fill="none">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" stroke="currentColor" stroke-width="2" stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <button type="button" class="icon-button settings-button" title="Pengaturan">
                        <svg viewBox="0 0 24 24" fill="none">
                            <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" />
                            <path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9 7 7M17 17l2.1 2.1M4.9 19.1 7 17M17 7l2.1-2.1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>

                    <div class="user-pill">
                        <div class="user-avatar">
                            <img
                                v-if="avatarUrl"
                                :src="avatarUrl"
                                :alt="displayName"
                            />
                            <span v-else>{{ initials }}</span>
                        </div>

                        <div class="user-meta">
                            <p class="user-name">{{ displayName }}</p>
                            <p class="user-nim">{{ displayNim }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- CONTENT -->
            <main class="student-content">
                <div v-if="flash.success" class="student-flash">
                    {{ flash.success }}
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
.student-shell {
    --primary: #0d9488;
    --primary-dark: #0f766e;
    --primary-soft: #f0fdf9;
    --border: #e2e8f0;
    --text: #0f172a;
    --muted: #64748b;
    --bg: #f5f8fc;
    --surface: #ffffff;
    --sidebar-bg: #ffffff;
    --topbar-bg: #ffffff;
    --nav-hover: #f8fafc;
    --nav-active: #f0fdf9;

    display: flex;
    min-height: 100vh;
    background: var(--bg);
    color: var(--text);
    transition: background 0.2s, color 0.2s;
}

/* DARK MODE */
:global(html.dark) .student-shell {
    --border: #1e293b;
    --text: #f1f5f9;
    --muted: #94a3b8;
    --bg: #0f172a;
    --surface: #1e293b;
    --sidebar-bg: #1e293b;
    --topbar-bg: #1e293b;
    --nav-hover: #0f172a;
    --nav-active: #134e4a;
}

.student-sidebar {
    position: sticky;
    top: 0;
    display: flex;
    flex-direction: column;
    flex-shrink: 0;
    width: 210px;
    height: 100vh;
    border-right: 1px solid var(--border);
    background: var(--sidebar-bg);
    transition: background 0.2s, border-color 0.2s;
}

.sidebar-close,
.mobile-menu-button {
    display: none;
}

.student-brand {
    display: flex;
    align-items: center;
    gap: 11px;
    height: 70px;
    padding: 0 17px;
    border-bottom: 1px solid var(--border);
}

.brand-icon {
    display: grid;
    place-items: center;
    width: 35px;
    height: 35px;
    border-radius: 10px;
    background: var(--primary);
    color: #ffffff;
}

.brand-icon svg {
    width: 20px;
    height: 20px;
}

.brand-name {
    margin: 0;
    color: var(--text);
    font-size: 14px;
    font-weight: 750;
}

.brand-subtitle {
    margin: 2px 0 0;
    color: #94a3b8;
    font-size: 11px;
}

.student-nav {
    display: flex;
    flex: 1;
    flex-direction: column;
    gap: 5px;
    padding: 18px 10px;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 11px 12px;
    border-radius: 10px;
    color: var(--muted);
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: 0.18s ease;
}

.nav-item svg {
    width: 17px;
    height: 17px;
    flex-shrink: 0;
}

.nav-item:hover {
    background: var(--nav-hover);
    color: var(--text);
}

.nav-item.active {
    background: var(--nav-active);
    color: var(--primary);
    font-weight: 700;
}

.sidebar-footer {
    display: flex;
    flex-direction: column;
    gap: 3px;
    padding: 13px 10px;
    border-top: 1px solid var(--border);
}

.footer-link {
    display: flex;
    align-items: center;
    gap: 11px;
    width: 100%;
    padding: 10px 12px;
    border: none;
    border-radius: 9px;
    background: transparent;
    color: var(--muted);
    font: inherit;
    font-size: 13px;
    text-align: left;
    text-decoration: none;
    cursor: pointer;
    transition: 0.15s;
}

.footer-link:hover {
    background: var(--nav-hover);
    color: var(--text);
}

.footer-link svg {
    width: 16px;
    height: 16px;
}

.theme-toggle {
    color: var(--primary);
    font-weight: 600;
}

.theme-toggle:hover {
    background: var(--nav-active);
}

.student-main {
    display: flex;
    flex: 1;
    flex-direction: column;
    min-width: 0;
}

.student-topbar {
    position: sticky;
    top: 0;
    z-index: 40;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 62px;
    padding: 0 26px;
    border-bottom: 1px solid var(--border);
    background: var(--topbar-bg);
    transition: background 0.2s, border-color 0.2s;
}

.student-search {
    position: relative;
    width: 230px;
}

.student-search svg {
    position: absolute;
    top: 50%;
    left: 12px;
    width: 15px;
    height: 15px;
    color: #94a3b8;
    transform: translateY(-50%);
}

.student-search input {
    width: 100%;
    padding: 10px 12px 10px 35px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--nav-hover);
    color: var(--text);
    font: inherit;
    font-size: 13px;
    outline: none;
    transition: 0.15s;
}

.student-search input:focus {
    border-color: #99f6e4;
    background: var(--surface);
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 9px;
}

.icon-button {
    position: relative;
    display: grid;
    place-items: center;
    width: 38px;
    height: 38px;
    border: 1px solid var(--border);
    border-radius: 10px;
    background: var(--surface);
    color: var(--muted);
    cursor: pointer;
    transition: 0.15s;
}

.icon-button:hover {
    background: var(--nav-hover);
    color: var(--primary);
}

.icon-button svg {
    width: 18px;
    height: 18px;
}

.user-pill {
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 40px;
    padding: 4px 11px 4px 5px;
    border: 1px solid var(--border);
    border-radius: 11px;
    background: var(--surface);
}

.user-avatar {
    display: grid;
    place-items: center;
    width: 32px;
    height: 32px;
    overflow: hidden;
    border-radius: 999px;
    background: #ccfbf1;
    color: #0f766e;
    font-size: 12px;
    font-weight: 750;
}

.user-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.user-name {
    margin: 0;
    color: var(--text);
    font-size: 12px;
    font-weight: 700;
}

.user-nim {
    margin: 1px 0 0;
    color: #94a3b8;
    font-size: 10px;
}

.student-content {
    flex: 1;
    padding: 27px 30px;
    background: var(--bg);
    transition: background 0.2s;
}

.student-flash {
    margin-bottom: 17px;
    padding: 12px 14px;
    border: 1px solid #a7f3d0;
    border-radius: 11px;
    background: #ecfdf5;
    color: #047857;
    font-size: 13px;
}

.student-overlay {
    display: none;
}

.drawer-fade-enter-active,
.drawer-fade-leave-active {
    transition: opacity 0.2s ease;
}

.drawer-fade-enter-from,
.drawer-fade-leave-to {
    opacity: 0;
}

@media (max-width: 900px) {
    .student-shell {
        display: block;
    }

    .student-sidebar {
        position: fixed;
        z-index: 1000;
        top: 0;
        left: 0;
        width: 270px;
        height: 100vh;
        transform: translateX(-104%);
        transition: transform 0.25s ease;
    }

    .student-sidebar.open {
        transform: translateX(0);
        box-shadow: 16px 0 42px rgba(15, 23, 42, 0.17);
    }

    .student-overlay {
        position: fixed;
        z-index: 999;
        inset: 0;
        display: block;
        background: rgba(15, 23, 42, 0.42);
        backdrop-filter: blur(2px);
    }

    .sidebar-close {
        position: absolute;
        top: 17px;
        right: 13px;
        display: grid;
        place-items: center;
        width: 35px;
        height: 35px;
        border: 1px solid var(--border);
        border-radius: 9px;
        background: var(--surface);
        color: #475569;
        cursor: pointer;
    }

    .sidebar-close svg {
        width: 18px;
        height: 18px;
    }

    .student-brand {
        padding-right: 60px;
    }

    .student-main {
        width: 100%;
    }

    .student-topbar {
        justify-content: flex-start;
        gap: 10px;
        padding: 0 14px;
    }

    .mobile-menu-button {
        display: grid;
        place-items: center;
        flex-shrink: 0;
        width: 39px;
        height: 39px;
        border: 1px solid var(--border);
        border-radius: 10px;
        background: var(--surface);
        color: var(--text);
        cursor: pointer;
    }

    .mobile-menu-button svg {
        width: 20px;
        height: 20px;
    }

    .student-search {
        flex: 1;
        width: auto;
        min-width: 0;
    }

    .topbar-right {
        margin-left: auto;
    }

    .student-content {
        padding: 20px 16px;
    }
}

@media (max-width: 580px) {
    .student-topbar {
        gap: 8px;
        padding: 0 10px;
    }

    .mobile-menu-button {
        width: 37px;
        height: 37px;
    }

    .student-search input::placeholder {
        color: transparent;
    }

    .settings-button {
        display: none;
    }

    .user-pill {
        padding: 4px;
    }

    .user-meta {
        display: none;
    }

    .student-content {
        padding: 18px 12px;
    }
}
</style>