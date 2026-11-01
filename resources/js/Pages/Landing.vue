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
        badge: 'Literasi Dasar',
        accentBg: 'bg-[#EBF3FE]',
        accentText: 'text-[#2E6FBE]',
        accentBorder: 'border-[#CFE2FC]',
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
        badge: 'Motorik Halus',
        accentBg: 'bg-[#FEF6E0]',
        accentText: 'text-[#B7791F]',
        accentBorder: 'border-[#F9E4B7]',
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
        badge: 'Logika & Angka',
        accentBg: 'bg-[#EBF8F0]',
        accentText: 'text-[#1E8E5A]',
        accentBorder: 'border-[#CBEAD7]',
        subtitle: 'Konsep Bilangan & Logika Dasar',
        summary: 'Memahami konsep jumlah, urutan angka, serta penjumlahan dan pengurangan dengan alat peraga.',
        description:
            'Mengenalkan konsep matematika awal melalui benda konkret dan sempoa warna-warni agar anak memahami arti bilangan, perbandingan jumlah, serta operasi hitung penjumlahan dan pengurangan secara logis.',
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
        badge: 'Iqra & Akhlak',
        accentBg: 'bg-[#F3EEFD]',
        accentText: 'text-[#6D28D9]',
        accentBorder: 'border-[#DDD6FE]',
        subtitle: 'Huruf Hijaiyah, Iqra & Doa Harian',
        summary: 'Bimbingan bacaan Iqra dan Al-Qur’an sesuai makhraj serta pembiasaan hafalan doa dan surat pendek.',
        description:
            'Setiap anak disimak secara individual sesuai halaman capaian Iqra masing-masing, disertai pengenalan tanda baca (harakat), perbaikan makharijul huruf, serta hafalan doa harian dan surat pendek.',
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
        symbol: 'A B C',
        description:
            'Melatih koordinasi tangan dan mata, cara memegang pensil, mengenal bentuk, warna, bunyi huruf vokal, membilang 1-10, dan pengenalan awal huruf hijaiyah.',
    },
    {
        title: 'Calistung Dasar',
        ageRange: 'Usia 4,5 - 6 Tahun',
        focus: 'Suku Kata, Buku Kotak & Angka',
        symbol: 'ba - ca',
        description:
            'Belajar membaca suku kata tanpa mengeja, menulis huruf dan angka rapi di buku kotak, berhitung benda konkret, serta bimbingan bacaan Iqra jilid awal.',
    },
    {
        title: 'Calistung Lanjutan',
        ageRange: 'Usia 6 - 7 Tahun',
        focus: 'Kesiapan Masuk Sekolah Dasar',
        symbol: '1 + 2 = 3',
        description:
            'Kelancaran membaca cerita pendek, menulis dikte kalimat, penjumlahan dan pengurangan bersusun sederhana, serta kelancaran membaca Iqra dan hafalan surat pendek.',
    },
    {
        title: 'Kelas Mengaji & Iqra',
        ageRange: 'Usia 4 - 8 Tahun',
        focus: 'Tahsin Iqra, Al-Qur’an & Doa Harian',
        symbol: 'أ ب ت',
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

    <div class="min-h-dvh bg-[#F4F7FB] text-slate-800 flex flex-col selection:bg-[#2E6FBE] selection:text-white overflow-x-hidden">
        <!-- Sticky Top Navbar: Seragam dengan Header Aplikasi Sabila -->
        <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-2xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-6">
                <!-- Brand Logo -->
                <a href="#top" class="flex items-center shrink-0 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE] rounded-xl py-1 group">
                    <img
                        src="/logo-sabiladunlearning.png"
                        alt="Sabila Fun Learning"
                        class="h-11 sm:h-[50px] w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                    />
                </a>

                <!-- Desktop Navigation Links -->
                <nav
                    class="hidden lg:flex items-center justify-center gap-1.5 xl:gap-2"
                    aria-label="Navigasi Utama"
                >
                    <a
                        href="#aktivitas"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition-colors"
                    >
                        4 Aktivitas Inti
                    </a>
                    <a
                        href="#program"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition-colors"
                    >
                        Program Usia
                    </a>
                    <a
                        href="#jadwal"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition-colors"
                    >
                        Jadwal Kelas
                    </a>
                    <a
                        href="#portal"
                        class="px-3.5 py-2 rounded-xl text-sm font-bold text-slate-600 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] transition-colors"
                    >
                        Portal Orang Tua
                    </a>
                </nav>

                <!-- Auth Action Buttons -->
                <div class="hidden sm:flex items-center gap-2.5 shrink-0">
                    <template v-if="user">
                        <Link
                            href="/"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs sm:text-sm font-bold shadow-xs transition-all duration-200 hover:-translate-y-0.5 min-h-[42px] inline-flex items-center"
                        >
                            Buka Dashboard ({{ user.name }})
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:text-[#2E6FBE] hover:bg-[#EBF3FE] text-xs sm:text-sm font-bold transition-colors min-h-[42px] inline-flex items-center"
                        >
                            Masuk Akun
                        </Link>
                        <Link
                            href="/login?register=1"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs sm:text-sm font-bold shadow-xs transition-all duration-200 hover:-translate-y-0.5 min-h-[42px] inline-flex items-center"
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
            <!-- 1. HERO SECTION: Ceria dengan Ilustrasi Anak Laki-laki Berpeci & Anak Perempuan Berhijab serta Pernak-pernik Edukatif -->
            <section class="relative bg-gradient-to-b from-[#DCEBFE] via-[#EDF5FF] to-[#F4F7FB] pt-8 pb-16 sm:pt-12 sm:pb-20 lg:pt-14 lg:pb-24 overflow-hidden">
                <!-- Pernak-pernik Dekoratif Latar (Simple, Ceria, Tidak Mengganggu Teks) -->
                <div class="pointer-events-none select-none" aria-hidden="true">
                    <!-- Awan Lembut Kiri Atas -->
                    <svg class="hidden sm:block absolute top-6 left-6 w-28 h-14 text-white/75 animate-float-slow" viewBox="0 0 120 60" fill="currentColor">
                        <path d="M25 50h70a18 18 0 0 0 3-35.7A24 24 0 0 0 52 10a22 22 0 0 0-21.5 17.2A16 16 0 0 0 25 50z" />
                    </svg>
                    <!-- Bintang Kuning Ceria Kanan Atas -->
                    <svg class="hidden md:block absolute top-8 right-12 w-8 h-8 text-amber-400/80 animate-wiggle-gentle" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l7.1-1.01L12 2z" />
                    </svg>
                    <!-- Ornamen Lingkaran Lembut Kiri Bawah -->
                    <div class="hidden lg:block absolute bottom-14 left-10 w-16 h-16 rounded-full border-4 border-[#2E6FBE]/15 animate-float-delayed"></div>
                </div>

                <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-10 items-center">
                        <!-- Left Hero Copy (6 Cols) -->
                        <div class="lg:col-span-6 text-center lg:text-left sabila-fade-up">
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white/90 border border-[#CFE2FC] text-[#2E6FBE] text-xs font-extrabold shadow-2xs mb-4">
                                <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                                <span>Bimbingan Belajar Anak Usia 3,5 - 8 Tahun</span>
                            </div>

                            <h1 class="text-3xl sm:text-4xl lg:text-[40px] xl:text-[42px] font-extrabold text-slate-800 tracking-tight leading-[1.22] sm:leading-[1.2]">
                                Belajar Ceria
                                <span class="text-[#2E6FBE] underline decoration-[#F59E0B] decoration-wavy decoration-2 underline-offset-8">Membaca, Menulis,</span>
                                <span class="text-[#2E6FBE]"> Berhitung &amp; Mengaji</span>
                                Setiap Hari
                            </h1>

                            <p class="mt-4 sm:mt-5 text-base sm:text-lg text-slate-600 max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium">
                                Sabila Fun Learning mendampingi putra-putri Ayah &amp; Bunda membangun fondasi akademik dan mengaji Iqra secara bertahap dalam kelompok kecil, dilengkapi buku penghubung digital harian.
                            </p>

                            <!-- Hero CTAs -->
                            <div class="mt-6 sm:mt-7 flex flex-wrap items-center justify-center lg:justify-start gap-3.5">
                                <Link
                                    href="/login?register=1"
                                    class="px-7 py-3.5 rounded-2xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white font-bold text-sm sm:text-base shadow-md hover:shadow-lg transition-all duration-300 hover:-translate-y-0.5 min-h-[48px] inline-flex items-center justify-center gap-2"
                                >
                                    <span>Daftarkan Anak Sekarang</span>
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                    </svg>
                                </Link>
                                <Link
                                    href="/login"
                                    class="px-6 py-3.5 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200/90 text-slate-700 font-bold text-sm sm:text-base shadow-2xs transition-all duration-300 hover:-translate-y-0.5 min-h-[48px] inline-flex items-center justify-center"
                                >
                                    Masuk Portal Orang Tua
                                </Link>
                            </div>

                            <!-- Key Highlights Strip -->
                            <div class="mt-8 pt-6 border-t border-slate-200/80 grid grid-cols-3 gap-3 sm:gap-4 text-left max-w-xl mx-auto lg:mx-0">
                                <div class="bg-white/90 rounded-2xl p-3 border border-slate-200/80 shadow-2xs transition-transform duration-300 hover:-translate-y-0.5">
                                    <p class="text-sm sm:text-base font-extrabold text-[#2E6FBE]">4 Materi Inti</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Calistung &amp; Iqra</p>
                                </div>
                                <div class="bg-white/90 rounded-2xl p-3 border border-slate-200/80 shadow-2xs transition-transform duration-300 hover:-translate-y-0.5">
                                    <p class="text-sm sm:text-base font-extrabold text-slate-800">8 - 10 Anak</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Kuota Kelas Kecil</p>
                                </div>
                                <div class="bg-white/90 rounded-2xl p-3 border border-slate-200/80 shadow-2xs transition-transform duration-300 hover:-translate-y-0.5">
                                    <p class="text-sm sm:text-base font-extrabold text-[#1E8E5A]">Laporan Harian</p>
                                    <p class="text-[11px] sm:text-xs text-slate-500 font-medium mt-0.5">Dipantau dari HP</p>
                                </div>
                            </div>
                        </div>

                        <!-- Right Hero Visual: Ilustrasi Anak Laki-laki Berpeci & Anak Perempuan Berhijab + Pernak-pernik + Kartu Progres (6 Cols) -->
                        <div class="lg:col-span-6 relative sabila-fade-up-delayed">
                            <!-- Pernak-pernik Floating Badges di Sekitar Ilustrasi -->
                            <div
                                class="hidden sm:flex absolute -top-4 left-4 z-20 items-center gap-2 px-3.5 py-2 rounded-2xl bg-white border border-[#CFE2FC] shadow-md animate-float-slow"
                            >
                                <span class="w-7 h-7 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] font-extrabold text-xs flex items-center justify-center">
                                    ABC
                                </span>
                                <div class="text-left">
                                    <p class="text-[11px] font-extrabold text-slate-800 leading-none">Membaca &amp; Menulis</p>
                                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">Tanpa Mengeja</p>
                                </div>
                            </div>

                            <div
                                class="hidden sm:flex absolute -top-3 right-4 z-20 items-center gap-2 px-3.5 py-2 rounded-2xl bg-white border border-[#DDD6FE] shadow-md animate-float-delayed"
                            >
                                <span class="w-7 h-7 rounded-xl bg-[#F3EEFD] text-[#6D28D9] font-extrabold text-xs flex items-center justify-center">
                                    أ ب
                                </span>
                                <div class="text-left">
                                    <p class="text-[11px] font-extrabold text-slate-800 leading-none">Mengaji Iqra &amp; Doa</p>
                                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">Sesuai Makhraj</p>
                                </div>
                            </div>

                            <!-- Bingkai Utama Ilustrasi & Ringkasan Laporan -->
                            <div class="bg-white rounded-[28px] p-4 sm:p-5 border-2 border-white shadow-xl space-y-4 relative z-10">
                                <!-- Gambar Ilustrasi Anak Laki-laki & Perempuan Berhijab -->
                                <div class="relative rounded-2xl overflow-hidden bg-[#EEF6FF] border border-[#DCEBFE] group">
                                    <img
                                        src="/images/hero-kids-sabila.jpg"
                                        alt="Ilustrasi anak laki-laki berpeci dan anak perempuan berhijab belajar membaca, menulis, berhitung, dan mengaji di Sabila Fun Learning"
                                        class="w-full h-56 sm:h-72 object-cover object-center transition-transform duration-500 group-hover:scale-[1.03]"
                                    />
                                    <!-- Lencana Sudut Bawah Gambar -->
                                    <div class="absolute bottom-3 left-3 right-3 flex flex-wrap items-center justify-between gap-2">
                                        <span class="px-3 py-1 rounded-full bg-white/95 backdrop-blur-xs text-slate-800 text-[11px] font-extrabold shadow-xs border border-slate-200/70">
                                            Suasana Belajar Ramah Anak &amp; Islami
                                        </span>
                                        <span class="px-3 py-1 rounded-full bg-[#EBF8F0]/95 text-[#1E8E5A] text-[11px] font-extrabold shadow-xs border border-[#CBEAD7]">
                                            Hadir Hari Ini • 10.00 - 11.00
                                        </span>
                                    </div>
                                </div>

                                <!-- 4 Core Activities Mini Strip: Seragam dengan Aplikasi Sabila -->
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                                    <div class="p-2.5 rounded-2xl bg-[#EBF3FE] border border-[#CFE2FC] transition-transform duration-200 hover:-translate-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold text-[#2E6FBE]">Membaca</span>
                                            <span class="text-[11px] font-extrabold text-slate-800">85%</span>
                                        </div>
                                        <p class="text-[10px] text-slate-600 font-medium mt-0.5 truncate">Suku kata &amp; cerita</p>
                                    </div>

                                    <div class="p-2.5 rounded-2xl bg-[#FEF6E0] border border-[#F9E4B7] transition-transform duration-200 hover:-translate-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold text-[#B7791F]">Menulis</span>
                                            <span class="text-[11px] font-extrabold text-slate-800">80%</span>
                                        </div>
                                        <p class="text-[10px] text-slate-600 font-medium mt-0.5 truncate">Rapi di buku kotak</p>
                                    </div>

                                    <div class="p-2.5 rounded-2xl bg-[#EBF8F0] border border-[#CBEAD7] transition-transform duration-200 hover:-translate-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold text-[#1E8E5A]">Berhitung</span>
                                            <span class="text-[11px] font-extrabold text-slate-800">88%</span>
                                        </div>
                                        <p class="text-[10px] text-slate-600 font-medium mt-0.5 truncate">Angka &amp; sempoa</p>
                                    </div>

                                    <div class="p-2.5 rounded-2xl bg-[#F3EEFD] border border-[#DDD6FE] transition-transform duration-200 hover:-translate-y-0.5">
                                        <div class="flex items-center justify-between">
                                            <span class="text-[11px] font-extrabold text-[#6D28D9]">Mengaji</span>
                                            <span class="text-[11px] font-extrabold text-slate-800">86%</span>
                                        </div>
                                        <p class="text-[10px] text-slate-600 font-medium mt-0.5 truncate">Iqra &amp; doa harian</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pembatas Gelombang Lembut (Soft Wave Divider) menuju Section Putih -->
                <div class="absolute bottom-0 left-0 right-0 overflow-hidden leading-none pointer-events-none" aria-hidden="true">
                    <svg class="relative block w-full h-7 sm:h-10 text-white" viewBox="0 0 1440 54" preserveAspectRatio="none" fill="currentColor">
                        <path d="M0 24C240 54 480 54 720 28C960 2 1200 2 1440 28V54H0V24Z" />
                    </svg>
                </div>
            </section>

            <!-- 2. 4 AKTIVITAS INTI (#aktivitas): Membaca, Menulis, Berhitung, dan Mengaji -->
            <section id="aktivitas" class="py-16 sm:py-20 bg-white border-b border-slate-200/80 relative">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                        <div class="max-w-2xl space-y-2">
                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#2E6FBE] uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-[#2E6FBE]"></span>
                                Kurikulum Terpadu
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800">
                                Empat Materi Inti dalam Setiap Sesi Belajar
                            </h2>
                            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                                Kurikulum dirancang berimbang antara literasi dasar (Membaca, Menulis, Berhitung) dan pondasi mengaji sejak usia dini. Klik salah satu materi di bawah untuk melihat rinciannya:
                            </p>
                        </div>

                        <!-- Pernak-pernik Badge Kurikulum -->
                        <div class="flex items-center gap-2 self-start md:self-auto px-3.5 py-2 rounded-2xl bg-[#F4F7FB] border border-slate-200/80 text-xs font-bold text-slate-700">
                            <span class="px-2 py-0.5 rounded-lg bg-[#EBF3FE] text-[#2E6FBE] font-extrabold">ABC</span>
                            <span class="px-2 py-0.5 rounded-lg bg-[#FEF6E0] text-[#B7791F] font-extrabold">123</span>
                            <span class="px-2 py-0.5 rounded-lg bg-[#F3EEFD] text-[#6D28D9] font-extrabold">أ ب ت</span>
                            <span class="ml-1">Terjadwal Setiap Pertemuan</span>
                        </div>
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
                                    'w-full text-left p-4 rounded-2xl border transition-all duration-200 cursor-pointer flex items-start gap-3.5',
                                    activeTopicKey === topic.key
                                        ? 'border-[#2E6FBE] bg-[#EBF3FE] shadow-sm -translate-y-0.5'
                                        : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50',
                                ]"
                            >
                                <span
                                    :class="[
                                        'w-11 h-11 rounded-xl flex items-center justify-center text-sm font-extrabold shrink-0 transition-colors',
                                        activeTopicKey === topic.key
                                            ? 'bg-[#2E6FBE] text-white shadow-2xs'
                                            : `${topic.accentBg} ${topic.accentText}`,
                                    ]"
                                >
                                    {{ topic.number }}
                                </span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <h3 class="text-base font-extrabold text-slate-800">
                                            {{ topic.title }}
                                        </h3>
                                        <span
                                            :class="[
                                                'px-2 py-0.5 rounded-full text-[10px] font-bold border',
                                                topic.accentBg,
                                                topic.accentText,
                                                topic.accentBorder,
                                            ]"
                                        >
                                            {{ topic.badge }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium mt-1 leading-snug">
                                        {{ topic.summary }}
                                    </p>
                                </div>
                            </button>
                        </div>

                        <!-- Right Active Topic Detail Box with Smooth Transition -->
                        <div class="lg:col-span-7">
                            <Transition name="topic-fade" mode="out-in">
                                <div
                                    :key="activeTopic.key"
                                    class="bg-[#F4F7FB] rounded-3xl p-6 sm:p-8 border border-slate-200/80 space-y-6 shadow-2xs"
                                >
                                    <div class="border-b border-slate-200 pb-4 flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <span
                                                :class="[
                                                    'inline-block px-3 py-1 rounded-full text-xs font-extrabold border mb-2',
                                                    activeTopic.accentBg,
                                                    activeTopic.accentText,
                                                    activeTopic.accentBorder,
                                                ]"
                                            >
                                                Fokus Materi {{ activeTopic.number }}: {{ activeTopic.title }}
                                            </span>
                                            <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800">
                                                {{ activeTopic.subtitle }}
                                            </h3>
                                        </div>
                                        <Link
                                            href="/login?register=1"
                                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs font-bold transition-all duration-200 hover:-translate-y-0.5 shadow-xs"
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
                            </Transition>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. PROGRAM BERDASARKAN USIA (#program) -->
            <section id="program" class="py-16 sm:py-20 bg-[#F4F7FB] relative overflow-hidden">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                    <div class="max-w-3xl space-y-2">
                        <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#2E6FBE] uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                            Jenjang Belajar
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800">
                            Penempatan Kelas Sesuai Tahap Usia &amp; Kesiapan Anak
                        </h2>
                        <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                            Setiap kelompok belajar dibagi berdasarkan rentang usia dan kemampuan awal anak agar proses pendampingan berjalan kondusif dan menyenangkan.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div
                            v-for="(prog, idx) in agePrograms"
                            :key="prog.title"
                            class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-2xs flex flex-col justify-between space-y-4 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group"
                        >
                            <div class="space-y-3.5">
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

                                    <!-- Pernak-pernik Simbol Edukasi per Program -->
                                    <span
                                        :class="[
                                            'px-2.5 py-1 rounded-xl text-xs font-extrabold tracking-wide transition-transform duration-300 group-hover:scale-105',
                                            idx === 0
                                                ? 'bg-[#EBF3FE]/70 text-[#2E6FBE]'
                                                : idx === 1
                                                  ? 'bg-[#FEF6E0]/70 text-[#B7791F]'
                                                  : idx === 2
                                                    ? 'bg-[#EBF8F0]/70 text-[#1E8E5A]'
                                                    : 'bg-[#F3EEFD]/70 text-[#6D28D9]',
                                        ]"
                                    >
                                        {{ prog.symbol }}
                                    </span>
                                </div>

                                <div>
                                    <p class="text-xs font-bold text-slate-400">{{ prog.focus }}</p>
                                    <h3 class="text-lg sm:text-xl font-extrabold text-slate-800 mt-0.5">
                                        {{ prog.title }}
                                    </h3>
                                </div>

                                <p class="text-sm text-slate-600 leading-relaxed font-medium">
                                    {{ prog.description }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-semibold text-slate-500">Materi: Calistung &amp; Mengaji</span>
                                <Link
                                    href="/login?register=1"
                                    class="text-xs font-bold text-[#2E6FBE] group-hover:underline inline-flex items-center gap-1"
                                >
                                    <span>Daftar Program</span>
                                    <span aria-hidden="true">&rarr;</span>
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
                            <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#2E6FBE] uppercase tracking-wider">
                                <span class="w-2 h-2 rounded-full bg-[#1E8E5A]"></span>
                                Jadwal Sesi Belajar
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800">
                                Jadwal &amp; Kuota Kelompok Kelas
                            </h2>
                            <p class="text-sm sm:text-base text-slate-600">
                                Pilih kelompok belajar yang tersedia sesuai jadwal hari dan jam sesi yang paling nyaman bagi buah hati Anda.
                            </p>
                        </div>
                        <Link
                            href="/login?register=1"
                            class="px-5 py-2.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-sm font-bold self-start sm:self-auto transition-all duration-200 hover:-translate-y-0.5 shadow-xs"
                        >
                            + Daftar Murid Baru
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div
                            v-for="cls in displayClasses"
                            :key="cls.id"
                            class="bg-white rounded-3xl border border-slate-200/90 hover:border-[#2E6FBE] shadow-2xs hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden"
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
                                    class="px-3.5 py-1.5 rounded-xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-xs font-bold transition shadow-2xs"
                                >
                                    Pilih Kelas
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. BUKU PENGHUBUNG DIGITAL & ILUSTRASI BELAJAR BERSAMA (#portal) -->
            <section id="portal" class="py-16 sm:py-20 bg-[#F4F7FB]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                        <!-- Left Column: Ilustrasi Anak Belajar di Meja + Copy Portal -->
                        <div class="lg:col-span-5 space-y-5">
                            <div class="space-y-2.5">
                                <span class="inline-flex items-center gap-1.5 text-xs font-extrabold text-[#2E6FBE] uppercase tracking-wider">
                                    <span class="w-2 h-2 rounded-full bg-[#2E6FBE]"></span>
                                    Buku Penghubung Digital
                                </span>
                                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 leading-tight">
                                    Pantau Perkembangan Harian &amp; Bulanan Anak dari Smartphone
                                </h2>
                                <p class="text-sm sm:text-base text-slate-600 leading-relaxed font-medium">
                                    Setiap orang tua murid memiliki akun khusus untuk memantau aktivitas belajar di kelas, mengajukan izin atau sakit, serta melihat evaluasi bulanan secara transparan.
                                </p>
                            </div>

                            <!-- Kartu Ilustrasi Kegiatan Belajar Anak Laki-laki & Perempuan Berhijab -->
                            <div class="bg-white p-3.5 rounded-3xl border border-slate-200/90 shadow-sm space-y-3 group">
                                <div class="rounded-2xl overflow-hidden bg-[#F4F7FB] border border-slate-100">
                                    <img
                                        src="/images/learning-activity-kids.jpg"
                                        alt="Ilustrasi anak laki-laki berpeci dan perempuan berhijab belajar berhitung sempoa, menulis, dan membaca Iqra bersama di kelas"
                                        class="w-full h-48 sm:h-56 object-cover object-center transition-transform duration-500 group-hover:scale-[1.03]"
                                    />
                                </div>
                                <div class="flex items-center justify-between px-1 text-xs">
                                    <span class="font-extrabold text-slate-700">Pendampingan Kelompok Kecil</span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-[#EBF3FE] text-[#2E6FBE] font-bold">
                                        Calistung &amp; Iqra
                                    </span>
                                </div>
                            </div>

                            <div class="pt-1 flex flex-wrap gap-3">
                                <Link
                                    href="/login"
                                    class="px-5 py-3 rounded-2xl bg-[#2E6FBE] hover:bg-[#245ca3] text-white text-sm font-bold transition-all duration-200 hover:-translate-y-0.5 shadow-sm"
                                >
                                    Masuk ke Portal Orang Tua
                                </Link>
                                <Link
                                    href="/login?register=1"
                                    class="px-5 py-3 rounded-2xl bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-sm font-bold transition-all duration-200 hover:-translate-y-0.5 shadow-2xs"
                                >
                                    Buat Akun Baru
                                </Link>
                            </div>
                        </div>

                        <!-- Right Column: 4 Kartu Fitur Portal Orang Tua -->
                        <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="inline-block px-3 py-1 rounded-full bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold">
                                        Laporan Harian &amp; Presensi
                                    </span>
                                    <span class="w-8 h-8 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] flex items-center justify-center font-extrabold text-xs">
                                        01
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Catatan Materi &amp; Kehadiran
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                                    Mencatat kegiatan Membaca, Menulis, Berhitung, dan Mengaji setiap pertemuan, dilengkapi fitur pengajuan izin atau sakit online.
                                </p>
                            </div>

                            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="inline-block px-3 py-1 rounded-full bg-[#FEF6E0] text-[#B7791F] text-xs font-bold">
                                        Evaluasi Bulanan
                                    </span>
                                    <span class="w-8 h-8 rounded-xl bg-[#FEF6E0] text-[#B7791F] flex items-center justify-center font-extrabold text-xs">
                                        02
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Rekap Capaian 4 Aspek
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                                    Laporan perkembangan setiap akhir bulan untuk skor dan catatan Membaca, Menulis, Berhitung, Mengaji, serta Sikap Belajar.
                                </p>
                            </div>

                            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="inline-block px-3 py-1 rounded-full bg-[#EBF8F0] text-[#1E8E5A] text-xs font-bold">
                                        Dokumentasi Kelas
                                    </span>
                                    <span class="w-8 h-8 rounded-xl bg-[#EBF8F0] text-[#1E8E5A] flex items-center justify-center font-extrabold text-xs">
                                        03
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Galeri Foto Kegiatan Privat
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                                    Dokumentasi foto kegiatan belajar anak di kelas yang diamankan dan hanya dapat diakses oleh orang tua murid terkait.
                                </p>
                            </div>

                            <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md hover:-translate-y-1 transition-all duration-300 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="inline-block px-3 py-1 rounded-full bg-[#F3EEFD] text-[#6D28D9] text-xs font-bold">
                                        Administrasi SPP
                                    </span>
                                    <span class="w-8 h-8 rounded-xl bg-[#F3EEFD] text-[#6D28D9] flex items-center justify-center font-extrabold text-xs">
                                        04
                                    </span>
                                </div>
                                <h3 class="text-base font-extrabold text-slate-800">
                                    Informasi Tagihan &amp; Bukti Bayar
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
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

<style scoped>
@keyframes floatSlow {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-7px);
    }
}

@keyframes wiggleGentle {
    0%, 100% {
        transform: rotate(-4deg) scale(1);
    }
    50% {
        transform: rotate(6deg) scale(1.06);
    }
}

@keyframes fadeUpIn {
    from {
        opacity: 0;
        transform: translateY(14px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-float-slow {
    animation: floatSlow 4.5s ease-in-out infinite;
}

.animate-float-delayed {
    animation: floatSlow 5.2s ease-in-out 1.2s infinite;
}

.animate-wiggle-gentle {
    animation: wiggleGentle 4s ease-in-out infinite;
}

.sabila-fade-up {
    animation: fadeUpIn 0.6s cubic-bezier(0.22, 1, 0.36, 1) both;
}

.sabila-fade-up-delayed {
    animation: fadeUpIn 0.75s cubic-bezier(0.22, 1, 0.36, 1) 0.12s both;
}

.topic-fade-enter-active,
.topic-fade-leave-active {
    transition: opacity 0.22s ease, transform 0.22s ease;
}

.topic-fade-enter-from {
    opacity: 0;
    transform: translateY(8px);
}

.topic-fade-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

@media (prefers-reduced-motion: reduce) {
    .animate-float-slow,
    .animate-float-delayed,
    .animate-wiggle-gentle,
    .sabila-fade-up,
    .sabila-fade-up-delayed {
        animation: none !important;
    }
}
</style>
