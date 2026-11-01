<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    publicClasses: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const mobileMenuOpen = ref(false);
const activeTopicKey = ref('membaca');

const coreTopics = [
    {
        key: 'membaca',
        number: '01',
        title: 'Membaca',
        subtitle: 'Metode Suku Kata Tanpa Mengeja',
        summary: 'Pengenalan bunyi huruf dan suku kata secara bertahap hingga anak lancar membaca kalimat cerita.',
        description:
            'Anak tidak dibebani menghafal abjad secara kaku, melainkan mengenal bunyi suku kata terbuka dan tertutup menggunakan media kartu baca kontekstual hingga mampu memahami isi bacaan pendek.',
        outcomes: [
            'Mengenal bunyi huruf vokal dan konsonan dengan pelafalan tepat',
            'Membaca gabungan suku kata tanpa mengeja huruf demi huruf',
            'Memahami isi kalimat sederhana dan bacaan cerita anak',
        ],
    },
    {
        key: 'menulis',
        number: '02',
        title: 'Menulis',
        subtitle: 'Motorik Halus & Kerapian Huruf',
        summary: 'Latihan postur memegang pensil, kontrol garis, hingga menulis kata dan kalimat dikte di buku kotak.',
        description:
            'Pendampingan dimulai dari kesiapan jari dan genggaman pensil, latihan menarik garis dasar, menebalkan pola huruf, hingga menyalin kata dan menulis kalimat dikte secara proporsional.',
        outcomes: [
            'Koordinasi motorik halus jari dan genggaman alat tulis yang stabil',
            'Menulis bentuk huruf lepas dan angka secara rapi di buku kotak',
            'Menyalin kalimat pendek dan menulis dikte sederhana secara mandiri',
        ],
    },
    {
        key: 'berhitung',
        number: '03',
        title: 'Berhitung',
        subtitle: 'Konsep Bilangan & Logika Dasar',
        summary: 'Memahami konsep jumlah, urutan angka, serta penjumlahan dan pengurangan dengan alat peraga.',
        description:
            'Mengenalkan konsep matematika awal melalui benda konkret agar anak memahami arti bilangan, perbandingan jumlah, serta operasi hitung penjumlahan dan pengurangan secara logis.',
        outcomes: [
            'Mengenal lambang bilangan dan membilang jumlah benda dengan tepat',
            'Memahami konsep perbandingan lebih banyak, lebih sedikit, dan sama dengan',
            'Menyelesaikan penjumlahan dan pengurangan dasar dengan percaya diri',
        ],
    },
    {
        key: 'mengaji',
        number: '04',
        title: 'Mengaji',
        subtitle: 'Huruf Hijaiyah, Iqra & Doa Harian',
        summary: 'Bimbingan bacaan Iqra dan Al-Qur’an sesuai makhraj serta pembiasaan hafalan doa dan surat pendek.',
        description:
            'Setiap anak disimak secara individual sesuai halaman capaian Iqra masing-masing, disertai pengenalan tanda baca (harakat), perbaikan makharijul huruf, serta hafalan doa harian.',
        outcomes: [
            'Mengenal dan melafalkan huruf hijaiyah sesuai makhraj dan harakat',
            'Setoran bacaan Iqra individual sesuai tingkat capaian masing-masing anak',
            'Hafalan doa sehari-hari, adab belajar, dan surat-surat pendek Juz Amma',
        ],
    },
];

const activeTopic = computed(() => coreTopics.find((t) => t.key === activeTopicKey.value) || coreTopics[0]);

const agePrograms = [
    {
        title: 'Kelas Pracalis',
        ageRange: 'Usia 3,5 - 4,5 Tahun',
        focus: 'Kesiapan Motorik & Pra-Membaca',
        description:
            'Melatih koordinasi tangan dan mata, cara memegang pensil, mengenal bentuk, warna, bunyi huruf vokal, membilang 1-10, dan pengenalan awal huruf hijaiyah.',
    },
    {
        title: 'Calistung Dasar',
        ageRange: 'Usia 4,5 - 6 Tahun',
        focus: 'Suku Kata, Buku Kotak & Angka',
        description:
            'Belajar membaca suku kata tanpa mengeja, menulis huruf dan angka rapi di buku kotak, berhitung benda konkret, serta bimbingan bacaan Iqra jilid awal.',
    },
    {
        title: 'Calistung Lanjutan',
        ageRange: 'Usia 6 - 7 Tahun',
        focus: 'Kesiapan Masuk Sekolah Dasar',
        description:
            'Kelancaran membaca cerita pendek, menulis dikte kalimat, penjumlahan dan pengurangan bersusun sederhana, serta kelancaran membaca Iqra dan hafalan surat pendek.',
    },
    {
        title: 'Kelas Mengaji & Iqra',
        ageRange: 'Usia 4 - 8 Tahun',
        focus: 'Tahsin Iqra, Al-Qur’an & Doa Harian',
        description:
            'Program khusus pendampingan membaca Iqra hingga Al-Qur’an secara privat dalam kelompok kecil, perbaikan pelafalan huruf hijaiyah, dan hafalan doa sehari-hari.',
    },
];

const defaultShowcaseClasses = [
    {
        id: 'default-1',
        name: 'Kelompok Pracalis Pagi',
        category: 'Pracalis',
        schedule: 'Senin - Jumat, 08.30 - 09.30',
        quota: 8,
        remaining_quota: 8,
        description: 'Motorik halus, pengenalan bunyi huruf, angka 1-10, dan mengaji dasar.',
    },
    {
        id: 'default-2',
        name: 'Kelompok Calistung A',
        category: 'Calistung',
        schedule: 'Senin - Jumat, 10.00 - 11.00',
        quota: 8,
        remaining_quota: 8,
        description: 'Membaca suku kata, menulis rapi di buku kotak, berhitung, dan mengaji Iqra.',
    },
    {
        id: 'default-3',
        name: 'Kelompok Calistung B',
        category: 'Calistung',
        schedule: 'Senin - Jumat, 11.15 - 12.15',
        quota: 8,
        remaining_quota: 8,
        description: 'Kelancaran membaca kalimat, menulis dikte, berhitung penjumlahan, dan mengaji.',
    },
    {
        id: 'default-4',
        name: 'Kelompok Mengaji Sore',
        category: 'Mengaji',
        schedule: 'Senin - Jumat, 15.30 - 16.30',
        quota: 10,
        remaining_quota: 10,
        description: 'Bimbingan bacaan Iqra / Al-Qur’an, makharijul huruf, doa harian, dan surat pendek.',
    },
];

const displayClasses = computed(() => {
    if (props.publicClasses && props.publicClasses.length > 0) {
        return props.publicClasses.map((cls) => ({
            ...cls,
            description: `Program ${cls.category} mencakup pendampingan Membaca, Menulis, Berhitung, dan Mengaji dengan kuota kelas terbatas.`,
        }));
    }
    return defaultShowcaseClasses;
});
</script>

<template>
    <Head title="Sabila Fun Learning - Bimbel Membaca, Menulis, Berhitung & Mengaji" />

    <div class="min-h-dvh bg-[#F4F7FB] text-slate-800 flex flex-col selection:bg-[#2E6FBE] selection:text-white">
        <!-- Sticky Top Navbar: Seragam dengan Header Aplikasi Sabila -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-6">
                <!-- Brand Logo -->
                <a href="#top" class="flex items-center shrink-0 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE] rounded-xl py-1">
                    <img
                        src="/logo-sabiladunlearning.png"
                        alt="Sabila Fun Learning"
                        class="h-11 sm:h-[50px] w-auto object-contain"
                    />
                </a>

                <!-- Desktop Navigation Links: Seragam dengan Tab Aplikasi Sabila -->
                <nav
                    class="hidden lg:flex items-center justify-center gap-1.5 xl:gap-2"
                    aria-label="Navigasi Utama"
                >
                    <a
                        href="#aktivitas"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition"
                    >
                        4 Aktivitas Inti
                    </a>
                    <a
                        href="#program"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition"
                    >
                        Program Usia
                    </a>
                    <a
                        href="#jadwal"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition"
                    >
                        Jadwal Kelas
                    </a>
                    <a
                        href="#portal"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition"
                    >
                        Portal Orang Tua
                    </a>
                </nav>

                <!-- Auth Action Buttons: Seragam dengan Tombol Aplikasi & Admin -->
                <div class="hidden sm:flex items-center gap-2.5 shrink-0">
                    <template v-if="user">
                        <Link
                            href="/"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs sm:text-sm font-bold shadow-xs transition min-h-[42px] inline-flex items-center"
                        >
                            Buka Dashboard ({{ user.name }})
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] text-xs sm:text-sm font-bold transition min-h-[42px] inline-flex items-center"
                        >
                            Masuk Akun
                        </Link>
                        <Link
                            href="/login?register=1"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs sm:text-sm font-bold shadow-xs transition min-h-[42px] inline-flex items-center"
                        >
                            Daftar Murid Baru
                        </Link>
                    </template>
                </div>

                <!-- Mobile Hamburger Button -->
                <button
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden w-11 h-11 rounded-xl border border-slate-200 bg-white flex items-center justify-center text-slate-700 hover:bg-slate-50 cursor-pointer"
                    :aria-expanded="mobileMenuOpen"
                    aria-label="Buka menu navigasi"
                >
                    <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Dropdown Menu -->
            <div v-if="mobileMenuOpen" class="lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-5 space-y-3 shadow-lg">
                <div class="grid grid-cols-1 gap-1 text-sm font-bold text-slate-700">
                    <a href="#aktivitas" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#2E6FBE]">
                        4 Aktivitas Inti (Membaca, Menulis, Berhitung, Mengaji)
                    </a>
                    <a href="#program" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#2E6FBE]">
                        Program Sesuai Usia
                    </a>
                    <a href="#jadwal" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#2E6FBE]">
                        Jadwal &amp; Kuota Kelas
                    </a>
                    <a href="#portal" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#2E6FBE]">
                        Portal Pemantauan Orang Tua
                    </a>
                </div>
                <div class="pt-3 border-t border-slate-200 flex flex-col sm:hidden gap-2">
                    <Link
                        v-if="user"
                        href="/"
                        class="w-full py-3 rounded-xl bg-[#2E6FBE] text-white text-center text-sm font-bold shadow-xs"
                    >
                        Buka Dashboard ({{ user.name }})
                    </Link>
                    <template v-else>
                        <Link
                            href="/login?register=1"
                            class="w-full py-3 rounded-xl bg-[#2E6FBE] text-white text-center text-sm font-bold shadow-xs"
                        >
                            Daftar Murid Baru
                        </Link>
                        <Link
                            href="/login"
                            class="w-full py-3 rounded-xl border border-slate-200 text-slate-700 text-center text-sm font-bold"
                        >
                            Masuk Akun
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main id="top" class="flex-1">
            <!-- 1. HERO SECTION: Seragam dengan Palet Sabila App (Light Canvas, Sky Wash & Sabila Blue) -->
            <section class="relative bg-gradient-to-b from-[#DCEBFE] via-[#F4F7FB] to-[#F4F7FB] pt-10 pb-16 sm:pt-14 sm:pb-20 lg:pt-16 lg:pb-24 border-b border-slate-200/80">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                        <!-- Left Hero Copy -->
                        <div class="lg:col-span-7 text-center lg:text-left">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#EBF3FE] border border-[#CFE2FC] text-[#2E6FBE] text-xs font-bold shadow-2xs mb-3 sm:mb-4">
                                Bimbingan Belajar Anak Usia 3,5 - 8 Tahun
                            </span>

                            <h1 class="text-3xl sm:text-4xl lg:text-[40px] xl:text-[42px] font-extrabold text-slate-800 tracking-tight leading-[1.22] sm:leading-[1.2]">
                                Pendampingan Belajar
                                <span class="text-[#2E6FBE]">Membaca, Menulis, Berhitung, dan Mengaji</span>
                                yang Menyenangkan untuk Anak
                            </h1>

                            <p class="mt-4 sm:mt-5 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto lg:mx-0 leading-relaxed font-medium">
                                Sabila Fun Learning mendampingi anak membangun fondasi akademik dan mengaji secara bertahap dalam kelompok kecil, dilengkapi buku penghubung digital yang dapat dipantau langsung oleh Orang Tua setiap hari.
                            </p>

                            <!-- Hero CTAs -->
                            <div class="mt-6 sm:mt-7 flex flex-wrap items-center justify-center lg:justify-start gap-3.5">
                                <Link
                                    href="/login?register=1"
                                    class="px-7 py-3.5 rounded-2xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white font-bold text-sm sm:text-base shadow-sm transition min-h-[48px] inline-flex items-center justify-center"
                                >
                                    Daftarkan Anak Sekarang
                                </Link>
                                <Link
                                    href="/login"
                                    class="px-7 py-3.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-sm sm:text-base shadow-2xs transition min-h-[48px] inline-flex items-center justify-center"
                                >
                                    Masuk Portal Orang Tua
                                </Link>
                            </div>

                            <!-- Key Highlights Strip: Kartu Seragam dengan Dasbor -->
                            <div class="mt-8 pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-3 sm:gap-4 text-left max-w-xl mx-auto lg:mx-0">
                                <div class="bg-white/80 rounded-2xl p-3 border border-slate-200/70 shadow-2xs">
                                    <p class="text-sm sm:text-base font-extrabold text-[#2E6FBE]">4 Materi</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Calistung &amp; Iqra</p>
                                </div>
                                <div class="bg-white/80 rounded-2xl p-3 border border-slate-200/70 shadow-2xs">
                                    <p class="text-sm sm:text-base font-extrabold text-slate-800">8 - 10 Anak</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Kuota Kelas Kecil</p>
                                </div>
                                <div class="bg-white/80 rounded-2xl p-3 border border-slate-200/70 shadow-2xs">
                                    <p class="text-sm sm:text-base font-extrabold text-[#1E8E5A]">Harian</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Laporan Digital</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Hero Live Report Preview Card: Persis Desain Kartu sabila_app -->
                        <div class="lg:col-span-5">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/90 shadow-md space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-full bg-[#EBF3FE] border-2 border-white shadow-2xs flex items-center justify-center text-xl shrink-0">
                                            👧
                                        </div>
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-800">Aisyah Putri</p>
                                            <p class="text-xs text-slate-500 font-medium">Kelompok Calistung A</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full bg-[#EBF8F0] text-[#1E8E5A] border border-[#CBEAD7] text-[11px] font-bold shrink-0">
                                        Hadir • 10.00 - 11.00
                                    </span>
                                </div>

                                <!-- 4 Core Activities Cards: Menggunakan Token Warna Resmi sabila_app -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div class="p-3.5 rounded-2xl bg-[#EBF3FE] border border-[#CFE2FC]">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-[#2E6FBE]">01. Membaca</span>
                                            <span class="text-xs font-extrabold text-slate-800">85%</span>
                                        </div>
                                        <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">Membaca suku kata terbuka dan cerita pendek</p>
                                    </div>

                                    <div class="p-3.5 rounded-2xl bg-[#FEF6E0] border border-[#F9E4B7]">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-[#B7791F]">02. Menulis</span>
                                            <span class="text-xs font-extrabold text-slate-800">80%</span>
                                        </div>
                                        <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">Menulis kata dan kalimat rapi di buku kotak</p>
                                    </div>

                                    <div class="p-3.5 rounded-2xl bg-[#EBF8F0] border border-[#CBEAD7]">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-[#1E8E5A]">03. Berhitung</span>
                                            <span class="text-xs font-extrabold text-slate-800">88%</span>
                                        </div>
                                        <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">Mengenal bilangan dan penjumlahan dasar</p>
                                    </div>

                                    <div class="p-3.5 rounded-2xl bg-[#F3EEFD] border border-[#DDD6FE]">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs font-bold text-[#6D28D9]">04. Mengaji</span>
                                            <span class="text-xs font-extrabold text-slate-800">86%</span>
                                        </div>
                                        <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">Huruf Hijaiyah, bacaan Iqra, dan doa harian</p>
                                    </div>
                                </div>

                                <div class="p-3.5 rounded-2xl bg-[#F4F7FB] border border-slate-200/80 text-xs space-y-1">
                                    <p class="font-bold text-slate-800">Catatan Ustadzah Pengajar:</p>
                                    <p class="text-slate-600 leading-relaxed font-medium">
                                        "Anak mengikuti kegiatan membaca suku kata, menulis di buku kotak, berhitung, serta setoran bacaan Iqra dengan fokus dan antusias."
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 2. 4 AKTIVITAS INTI (#aktivitas): Membaca, Menulis, Berhitung, dan Mengaji -->
            <section id="aktivitas" class="py-16 sm:py-20 bg-white border-b border-slate-200/80">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                    <div class="max-w-3xl space-y-2">
                        <span class="text-xs font-bold text-[#2E6FBE] uppercase tracking-wider">Kurikulum Terpadu</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800">
                            Empat Materi Inti dalam Setiap Sesi Belajar
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                            Kurikulum dirancang berimbang antara literasi dasar (Membaca, Menulis, Berhitung) dan pondasi mengaji sejak usia dini. Pilih salah satu materi di bawah untuk melihat rinciannya:
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <!-- Left Selector Tabs (4 Core Activities) -->
                        <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3">
                            <button
                                v-for="topic in coreTopics"
                                :key="topic.key"
                                type="button"
                                @click="activeTopicKey = topic.key"
                                :class="[
                                    'w-full text-left p-4 rounded-2xl border transition cursor-pointer flex items-start gap-3.5',
                                    activeTopicKey === topic.key
                                        ? 'border-[#2E6FBE] bg-[#EBF3FE] shadow-2xs'
                                        : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50',
                                ]"
                            >
                                <span
                                    :class="[
                                        'w-10 h-10 rounded-xl flex items-center justify-center text-sm font-extrabold shrink-0',
                                        activeTopicKey === topic.key
                                            ? 'bg-[#2E6FBE] text-white'
                                            : 'bg-slate-100 text-slate-700',
                                    ]"
                                >
                                    {{ topic.number }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="text-base font-extrabold text-slate-800">
                                        {{ topic.title }}
                                    </h3>
                                    <p class="text-xs text-slate-600 font-medium mt-0.5 leading-snug">
                                        {{ topic.summary }}
                                    </p>
                                </div>
                            </button>
                        </div>

                        <!-- Right Active Topic Detail Box -->
                        <div class="lg:col-span-7 bg-[#F4F7FB] rounded-3xl p-6 sm:p-8 border border-slate-200/80 space-y-6">
                            <div class="border-b border-slate-200 pb-4 flex flex-wrap items-center justify-between gap-2">
                                <div>
                                    <span class="text-xs font-bold text-[#2E6FBE]">
                                        Fokus Materi {{ activeTopic.number }}: {{ activeTopic.title }}
                                    </span>
                                    <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 mt-1">
                                        {{ activeTopic.subtitle }}
                                    </h3>
                                </div>
                                <Link
                                    href="/login?register=1"
                                    class="px-4 py-2 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs font-bold transition shadow-xs"
                                >
                                    Daftar Program Ini
                                </Link>
                            </div>

                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-medium">
                                {{ activeTopic.description }}
                            </p>

                            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-2xs space-y-3">
                                <h4 class="text-sm font-bold text-slate-800">
                                    Target Capaian Pembelajaran {{ activeTopic.title }}:
                                </h4>
                                <ul class="space-y-2.5 text-sm text-slate-700">
                                    <li v-for="item in activeTopic.outcomes" :key="item" class="flex items-start gap-2.5">
                                        <span class="w-5 h-5 rounded-md bg-[#EBF8F0] text-[#1E8E5A] font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                            ✓
                                        </span>
                                        <span class="font-medium">{{ item }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. PROGRAM BERDASARKAN USIA (#program) -->
            <section id="program" class="py-16 sm:py-20 bg-[#F4F7FB]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                    <div class="max-w-3xl space-y-2">
                        <span class="text-xs font-bold text-[#2E6FBE] uppercase tracking-wider">Jenjang Belajar</span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800">
                            Penempatan Kelas Sesuai Tahap Usia &amp; Kesiapan Anak
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                            Setiap kelompok belajar dibagi berdasarkan rentang usia dan kemampuan awal anak agar proses pendampingan berjalan kondusif.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div
                            v-for="(prog, idx) in agePrograms"
                            :key="prog.title"
                            class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs flex flex-col justify-between space-y-4 hover:shadow-md transition"
                        >
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        :class="[
                                            'px-3 py-1 rounded-full text-xs font-bold',
                                            idx === 0
                                                ? 'bg-[#EBF3FE] text-[#2E6FBE] border border-[#CFE2FC]'
                                                : idx === 1
                                                  ? 'bg-[#FEF6E0] text-[#B7791F] border border-[#F9E4B7]'
                                                  : idx === 2
                                                    ? 'bg-[#EBF8F0] text-[#1E8E5A] border border-[#CBEAD7]'
                                                    : 'bg-[#F3EEFD] text-[#6D28D9] border border-[#DDD6FE]',
                                        ]"
                                    >
                                        {{ prog.ageRange }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-500">
                                        {{ prog.focus }}
                                    </span>
                                </div>

                                <h3 class="text-lg sm:text-xl font-extrabold text-slate-800">
                                    {{ prog.title }}
                                </h3>

                                <p class="text-sm text-slate-600 leading-relaxed font-medium">
                                    {{ prog.description }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500">Materi: Calistung &amp; Mengaji</span>
                                <Link
                                    href="/login?register=1"
                                    class="text-xs font-bold text-[#2E6FBE] hover:underline"
                                >
                                    Daftar Program &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 4. JADWAL & KUOTA KELOMPOK KELAS (#jadwal) -->
            <section id="jadwal" class="py-16 sm:py-20 bg-white border-y border-slate-200/80">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div class="space-y-2 max-w-2xl">
                            <span class="text-xs font-bold text-[#2E6FBE] uppercase tracking-wider">Jadwal Sesi</span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800">
                                Jadwal &amp; Kuota Kelompok Kelas
                            </h2>
                            <p class="text-sm sm:text-base text-slate-600">
                                Pilih kelompok belajar yang tersedia sesuai jadwal hari dan jam sesi yang paling nyaman bagi buah hati Anda.
                            </p>
                        </div>
                        <Link
                            href="/login?register=1"
                            class="px-5 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-sm font-bold self-start sm:self-auto transition shadow-xs"
                        >
                            + Daftar Murid Baru
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div
                            v-for="cls in displayClasses"
                            :key="cls.id"
                            class="bg-white rounded-3xl border border-slate-200/90 hover:border-[#2E6FBE] shadow-2xs hover:shadow-md transition flex flex-col justify-between overflow-hidden"
                        >
                            <div class="p-5 space-y-3.5">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        :class="[
                                            'px-2.5 py-0.5 rounded-full text-xs font-bold',
                                            cls.category === 'Pracalis'
                                                ? 'bg-[#EBF3FE] text-[#2E6FBE]'
                                                : cls.category === 'Mengaji'
                                                  ? 'bg-[#F3EEFD] text-[#6D28D9]'
                                                  : 'bg-[#FEF6E0] text-[#B7791F]'
                                        ]"
                                    >
                                        {{ cls.category }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full bg-[#EBF8F0] text-[#1E8E5A] border border-[#CBEAD7] text-xs font-bold">
                                        Sisa: {{ cls.remaining_quota }}/{{ cls.quota }}
                                    </span>
                                </div>

                                <h3 class="text-base font-extrabold text-slate-800">
                                    {{ cls.name }}
                                </h3>

                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    {{ cls.description }}
                                </p>

                                <div class="pt-2 border-t border-slate-100 flex items-center gap-2 text-xs font-bold text-slate-700">
                                    <svg class="w-4 h-4 text-[#2E6FBE] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" />
                                        <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                    <span>{{ cls.schedule }}</span>
                                </div>
                            </div>

                            <div class="px-5 py-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500">Pendaftaran Dibuka</span>
                                <Link
                                    :href="`/login?register=1&group=${encodeURIComponent(cls.name)}`"
                                    class="px-3 py-1.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs font-bold transition shadow-2xs"
                                >
                                    Pilih Kelas
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. BUKU PENGHUBUNG DIGITAL ORANG TUA (#portal) -->
            <section id="portal" class="py-16 sm:py-20 bg-[#F4F7FB]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div class="lg:col-span-5 space-y-4">
                            <span class="text-xs font-bold text-[#2E6FBE] uppercase tracking-wider">Fitur Sabila App</span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 leading-tight">
                                Pantau Perkembangan Harian &amp; Bulanan Anak dari Smartphone
                            </h2>
                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-medium">
                                Setiap orang tua murid memiliki akun khusus untuk memantau aktivitas belajar di kelas, mengajukan izin atau sakit, serta melihat evaluasi bulanan secara transparan.
                            </p>
                            <div class="pt-2 flex flex-wrap gap-3">
                                <Link
                                    href="/login"
                                    class="px-5 py-3 rounded-2xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-sm font-bold transition shadow-sm"
                                >
                                    Masuk ke Portal Orang Tua
                                </Link>
                                <Link
                                    href="/login?register=1"
                                    class="px-5 py-3 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-sm font-bold transition shadow-2xs"
                                >
                                    Buat Akun Baru
                                </Link>
                            </div>
                        </div>

                        <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-2">
                                <span class="inline-block px-2.5 py-1 rounded-full bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold">
                                    Laporan Harian &amp; Presensi
                                </span>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Catatan Materi &amp; Kehadiran
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    Mencatat kegiatan Membaca, Menulis, Berhitung, dan Mengaji setiap pertemuan, dilengkapi fitur pengajuan izin atau sakit online.
                                </p>
                            </div>

                            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-2">
                                <span class="inline-block px-2.5 py-1 rounded-full bg-[#FEF6E0] text-[#B7791F] text-xs font-bold">
                                    Evaluasi Bulanan
                                </span>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Rekap Capaian 4 Aspek
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    Laporan perkembangan setiap akhir bulan untuk skor dan catatan Membaca, Menulis, Berhitung, Mengaji, serta Sikap Belajar.
                                </p>
                            </div>

                            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-2">
                                <span class="inline-block px-2.5 py-1 rounded-full bg-[#EBF8F0] text-[#1E8E5A] text-xs font-bold">
                                    Dokumentasi Kelas
                                </span>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Galeri Foto Kegiatan Privat
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    Dokumentasi foto kegiatan belajar anak di kelas yang diamankan dan hanya dapat diakses oleh orang tua murid terkait.
                                </p>
                            </div>

                            <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs space-y-2">
                                <span class="inline-block px-2.5 py-1 rounded-full bg-[#F3EEFD] text-[#6D28D9] text-xs font-bold">
                                    Administrasi SPP
                                </span>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Informasi Tagihan &amp; Bukti Bayar
                                </h3>
                                <p class="text-xs text-slate-600 leading-relaxed font-medium">
                                    Rincian tagihan SPP bulanan beserta kemudahan mengunggah bukti pembayaran langsung melalui portal.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer: Elegan & Serasi -->
        <footer class="bg-slate-900 text-slate-300 py-12 border-t border-slate-800 text-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-8">
                <div class="md:col-span-5 space-y-3">
                    <img
                        src="/logo-sabiladunlearning.png"
                        alt="Sabila Fun Learning"
                        class="h-11 w-auto bg-white rounded-xl px-3 py-1.5 object-contain"
                    />
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-sm">
                        Bimbingan belajar anak usia dini dan sekolah dasar dengan fokus Membaca, Menulis, Berhitung, dan Mengaji yang terintegrasi dengan portal pemantauan orang tua.
                    </p>
                </div>

                <div class="md:col-span-3 space-y-2.5">
                    <p class="font-extrabold text-white text-sm">Navigasi Halaman</p>
                    <ul class="space-y-2 text-xs sm:text-sm text-slate-300">
                        <li><a href="#aktivitas" class="hover:text-white transition">4 Aktivitas Inti</a></li>
                        <li><a href="#program" class="hover:text-white transition">Program Sesuai Usia</a></li>
                        <li><a href="#jadwal" class="hover:text-white transition">Jadwal Kelompok Kelas</a></li>
                        <li><a href="#portal" class="hover:text-white transition">Portal Orang Tua</a></li>
                    </ul>
                </div>

                <div class="md:col-span-4 space-y-3">
                    <p class="font-extrabold text-white text-sm">Akses Akun</p>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Masuk menggunakan akun Orang Tua, Guru, atau Admin untuk mengakses laporan perkembangan dan presensi.
                    </p>
                    <div class="flex flex-wrap gap-2.5 pt-1">
                        <Link
                            href="/login"
                            class="px-4 py-2.5 rounded-xl border border-slate-700 bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition"
                        >
                            Masuk ke Akun
                        </Link>
                        <Link
                            href="/login?register=1"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs font-bold transition shadow-xs"
                        >
                            Daftar Murid Baru
                        </Link>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-400">
                <p>&copy; {{ new Date().getFullYear() }} Sabila Fun Learning. Hak cipta dilindungi.</p>
                <p>Membaca • Menulis • Berhitung • Mengaji</p>
            </div>
        </footer>
    </div>
</template>
