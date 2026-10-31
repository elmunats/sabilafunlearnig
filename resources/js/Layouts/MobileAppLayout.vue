<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    showBack: {
        type: Boolean,
        default: false,
    },
    backUrl: {
        type: String,
        default: '',
    },
    activeTab: {
        type: String,
        default: 'beranda', // beranda, kelas, notifikasi, profil
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const pendingEnrollmentsCount = computed(() => page.props.auth?.pendingEnrollmentsCount || 0);
const unreadNotificationsCount = computed(() => page.props.auth?.unreadNotificationsCount || 0);
const flashSuccess = computed(() => page.props.flash?.success);
const flashError = computed(() => page.props.flash?.error);

function handleBack() {
    if (props.backUrl) {
        router.visit(props.backUrl);
    } else if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit('/');
    }
}
</script>

<template>
    <div class="w-full min-h-dvh bg-[#F4F7FB] flex flex-col justify-between relative">
        <!-- Sticky Top Subpage Header (Full Width Responsive with Sabila Logo) -->
        <header
            v-if="title"
            class="sticky top-0 z-20 w-full bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs"
        >
            <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2 min-w-[40px]">
                    <button
                        v-if="showBack"
                        type="button"
                        @click="handleBack"
                        class="w-10 h-10 -ml-1 rounded-full flex items-center justify-center text-slate-700 hover:bg-slate-100 transition cursor-pointer shrink-0"
                        aria-label="Kembali"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <Link href="/" class="hidden sm:inline-flex items-center shrink-0" aria-label="Beranda Sabila Fun Learning">
                        <img
                            src="/logo-sabiladunlearning.png"
                            alt="Sabila Fun Learning"
                            class="h-7 w-auto object-contain"
                        />
                    </Link>
                </div>

                <h1 class="text-[17px] sm:text-lg font-extrabold text-slate-800 tracking-tight text-center flex-1 truncate">
                    {{ title }}
                </h1>

                <div class="min-w-[40px] flex items-center justify-end">
                    <slot name="headerRight" />
                </div>
            </div>
        </header>

        <!-- Main Responsive Viewport Content -->
        <main class="flex-1 w-full max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-28">
            <!-- Flash Feedback Banners -->
            <div
                v-if="flashSuccess"
                class="mt-4 px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs"
            >
                <span>{{ flashSuccess }}</span>
            </div>
            <div
                v-if="flashError"
                class="mt-4 px-4 py-3 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-2xs"
            >
                <span>{{ flashError }}</span>
            </div>

            <slot />
        </main>

        <!-- Floating Bottom Navigation Menu (Beranda, Kelas [Guru/Admin], Notifikasi, Profil) -->
        <div class="fixed bottom-4 inset-x-0 z-40 pointer-events-none flex justify-center px-4">
            <nav
                aria-label="Navigasi Utama"
                class="pointer-events-auto bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-full shadow-[0_10px_35px_rgba(15,23,42,0.16)] px-3 sm:px-5 py-1.5 flex items-center justify-center gap-2 sm:gap-4"
            >
                <!-- 1. Beranda -->
                <Link
                    href="/"
                    :class="[
                        'flex items-center gap-1.5 py-2 px-4 rounded-full text-xs sm:text-sm transition min-h-[44px]',
                        activeTab === 'beranda'
                            ? 'bg-[#2E6FBE] text-white font-bold shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100 font-semibold',
                    ]"
                >
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" :fill="activeTab === 'beranda' ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5L12 3l9 7.5V20a1 1 0 01-1 1h-5.5v-6h-5v6H4a1 1 0 01-1-1v-9.5z" />
                    </svg>
                    <span>Beranda</span>
                </Link>

                <!-- 2. Kelas (For Teacher & Admin) -->
                <Link
                    v-if="user?.can_manage_reports"
                    href="/kelas"
                    :class="[
                        'relative flex items-center gap-1.5 py-2 px-4 rounded-full text-xs sm:text-sm transition min-h-[44px]',
                        activeTab === 'kelas'
                            ? 'bg-[#2E6FBE] text-white font-bold shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100 font-semibold',
                    ]"
                >
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Kelas</span>
                    <span
                        v-if="pendingEnrollmentsCount > 0"
                        class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-extrabold"
                    >
                        {{ pendingEnrollmentsCount }}
                    </span>
                </Link>

                <!-- 3. Notifikasi -->
                <Link
                    href="/notifikasi"
                    :class="[
                        'relative flex items-center gap-1.5 py-2 px-4 rounded-full text-xs sm:text-sm transition min-h-[44px]',
                        activeTab === 'notifikasi'
                            ? 'bg-[#2E6FBE] text-white font-bold shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100 font-semibold',
                    ]"
                >
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Notifikasi</span>
                    <span
                        v-if="unreadNotificationsCount > 0"
                        class="px-1.5 py-0.2 rounded-full bg-rose-500 text-white text-[10px] font-extrabold"
                    >
                        {{ unreadNotificationsCount }}
                    </span>
                </Link>

                <!-- 4. Profil -->
                <Link
                    href="/profil"
                    :class="[
                        'flex items-center gap-1.5 py-2 px-4 rounded-full text-xs sm:text-sm transition min-h-[44px]',
                        activeTab === 'profil'
                            ? 'bg-[#2E6FBE] text-white font-bold shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100 font-semibold',
                    ]"
                >
                    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="8" r="4" :fill="activeTab === 'profil' ? 'currentColor' : 'none'" />
                        <path stroke-linecap="round" d="M5 20c1.5-3.5 4.2-5 7-5s5.5 1.5 7 5" />
                    </svg>
                    <span>Profil</span>
                </Link>
            </nav>
        </div>
    </div>
</template>
