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
const newsletterEmail = ref('');
const newsletterSubmitted = ref(false);

function submitNewsletter() {
    if (!newsletterEmail.value.trim()) return;
    newsletterSubmitted.value = true;
    newsletterEmail.value = '';
}

const coreTopics = [
    {
        key: 'membaca',
        title: 'Membaca',
        cardBg: 'bg-[#E7F1FF]',
        cardBorder: 'border-[#CFE2FC]',
        badgeBg: 'bg-[#CBE0FF]',
        badgeText: 'text-[#1554B4]',
        shortDesc: 'Mengenal huruf, kata, dan kalimat dengan cara menyenangkan.',
        bannerBadge: 'Metode Suku Kata Tanpa Mengeja',
        bannerTitle: 'Metode Suku Kata Tanpa Mengeja',
        bannerDesc:
            'Anak tidak dibebani menghafal abjad secara kaku, melainkan mengenal bunyi suku kata terlebih dahulu dan bertahap menggunakan media kartu baca interaktif hingga mampu membaca lancar tanpa mengeja.',
        benefits: [
            'Belajar lebih mudah & menyenangkan',
            'Melatih fokus dan daya ingat',
            'Membangun kepercayaan diri anak',
        ],
    },
    {
        key: 'menulis',
        title: 'Menulis',
        cardBg: 'bg-[#E9F8EC]',
        cardBorder: 'border-[#C6EED0]',
        badgeBg: 'bg-[#C8F0D2]',
        badgeText: 'text-[#16793B]',
        shortDesc: 'Melatih motorik halus dan menulis dengan rapi.',
        bannerBadge: 'Motorik Halus & Kerapian Huruf',
        bannerTitle: 'Menulis Rapi & Terstruktur di Buku Kotak',
        bannerDesc:
            'Pendampingan dimulai dari postur memegang pensil yang benar, latihan menarik garis dasar, menebalkan pola huruf, hingga menyalin kata dan menulis kalimat dikte secara rapi.',
        benefits: [
            'Koordinasi motorik halus jari yang stabil',
            'Bentuk huruf dan angka proporsional',
            'Siap menulis mandiri untuk jenjang SD',
        ],
    },
    {
        key: 'berhitung',
        title: 'Berhitung',
        cardBg: 'bg-[#FFF3DF]',
        cardBorder: 'border-[#FBE0B4]',
        badgeBg: 'bg-[#FDE2B2]',
        badgeText: 'text-[#B46208]',
        shortDesc: 'Memahami konsep angka dan berhitung secara interaktif.',
        bannerBadge: 'Konsep Bilangan & Logika Dasar',
        bannerTitle: 'Berhitung Logis Tanpa Menghafal Rumus',
        bannerDesc:
            'Mengenalkan konsep matematika awal menggunakan kartu angka dan alat peraga konkret agar anak memahami arti jumlah, perbandingan, serta penjumlahan dan pengurangan dengan gembira.',
        benefits: [
            'Memahami konsep jumlah secara visual',
            'Penjumlahan & pengurangan dasar yang seru',
            'Melatih ketelitian dan nalar anak',
        ],
    },
    {
        key: 'mengaji',
        title: 'Mengaji',
        cardBg: 'bg-[#F1EBFF]',
        cardBorder: 'border-[#DDD0FC]',
        badgeBg: 'bg-[#E2D6FE]',
        badgeText: 'text-[#5B21B6]',
        shortDesc: "Belajar Iqra dan Al-Qur'an dengan metode praktis.",
        bannerBadge: 'Huruf Hijaiyah, Iqra & Doa Harian',
        bannerTitle: 'Bimbingan Mengaji Iqra & Hafalan Doa',
        bannerDesc:
            'Setiap anak disimak secara individual sesuai halaman capaian Iqra masing-masing, disertai pengenalan makharijul huruf, tanda baca (harakat), serta pembiasaan doa harian dan surat pendek.',
        benefits: [
            'Setoran bacaan Iqra privat sesuai capaian',
            'Pelafalan huruf hijaiyah sesuai makhraj',
            'Hafalan doa harian & surat pendek Juz Amma',
        ],
    },
];

const activeTopic = computed(() => coreTopics.find((t) => t.key === activeTopicKey.value) || coreTopics[0]);

const agePrograms = [
    {
        title: 'Kelas Preschool',
        ageBadge: 'Usia 3 - 6 Tahun',
        badgeClass: 'bg-[#2B7FFF] text-white',
        image: '/images/class-preschool.jpg',
        description: 'Kelas bermain sambil belajar untuk usia dini.',
        groupParam: 'Kelompok Pracalis Pagi',
    },
    {
        title: 'Calistung Dasar',
        ageBadge: 'Usia 4 - 6 Tahun',
        badgeClass: 'bg-[#34A853] text-white',
        image: '/images/class-calistung-dasar.jpg',
        description: 'Membaca, menulis, dan berhitung dengan metode menyenangkan.',
        groupParam: 'Kelompok Calistung A',
    },
    {
        title: 'Calistung Lanjutan',
        ageBadge: 'Usia 6 - 8 Tahun',
        badgeClass: 'bg-[#F59E0B] text-white',
        image: '/images/class-calistung-lanjutan.jpg',
        description: 'Menguatkan kemampuan akademik untuk jenjang berikutnya.',
        groupParam: 'Kelompok Calistung B',
    },
    {
        title: 'Kelas Mengaji & Iqra',
        ageBadge: '6+ Tahun',
        badgeClass: 'bg-[#8B5CF6] text-white',
        image: '/images/class-mengaji-iqra.jpg',
        description: 'Mengaji dengan tartil dan pemahaman yang baik.',
        groupParam: 'Kelompok Mengaji Sore',
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
            description: `Program ${cls.category} mencakup pendampingan Membaca, Menulis, Berhitung, dan Mengaji dalam kelompok kecil.`,
        }));
    }
    return defaultShowcaseClasses;
});
</script>

<template>
    <Head title="Sabila Fun Learning - Belajar Ceria Membaca, Menulis, Berhitung & Mengaji" />

    <div class="min-h-dvh bg-white text-[#0E2A47] flex flex-col selection:bg-[#1E6AD4] selection:text-white overflow-x-hidden font-sans">
        <!-- 1. TOP NAVBAR: Persis Referensi (Logo Kiri, Menu Tengah dengan Garis Aktif, Tombol Pill Kanan) -->
        <header class="sticky top-0 z-40 bg-[#EDF5FF]/90 backdrop-blur-md border-b border-blue-100/80">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
                <!-- Brand Logo -->
                <a href="#beranda" class="flex items-center shrink-0 focus:outline-none focus:ring-2 focus:ring-[#1E6AD4] rounded-xl py-1 group">
                    <img
                        src="/logo-sabiladunlearning.png"
                        alt="Sabila Fun Learning"
                        class="h-11 sm:h-12 w-auto object-contain transition-transform duration-300 group-hover:scale-105"
                    />
                </a>

                <!-- Center Navigation Links -->
                <nav class="hidden lg:flex items-center justify-center gap-7" aria-label="Navigasi Utama">
                    <a
                        href="#beranda"
                        class="relative py-2 inline-flex items-center gap-1.5 text-sm font-extrabold text-[#1E6AD4] after:content-[''] after:absolute after:bottom-0 after:left-0 after:right-0 after:h-[3px] after:rounded-full after:bg-[#1E6AD4]"
                    >
                        <svg class="w-4 h-4 text-[#1E6AD4]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 3l9 8h-3v9h-5v-6h-2v6H6v-9H3l9-8z" />
                        </svg>
                        <span>Beranda</span>
                    </a>
                    <a
                        href="#program"
                        class="py-2 inline-flex items-center gap-1 text-sm font-bold text-[#1E3A5F] hover:text-[#1E6AD4] transition-colors"
                    >
                        <span>Program Kelas</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </a>
                    <a
                        href="#jadwal"
                        class="py-2 text-sm font-bold text-[#1E3A5F] hover:text-[#1E6AD4] transition-colors"
                    >
                        Jadwal Kelas
                    </a>
                    <a
                        href="#portal"
                        class="py-2 text-sm font-bold text-[#1E3A5F] hover:text-[#1E6AD4] transition-colors"
                    >
                        Portal Orang Tua
                    </a>
                </nav>

                <!-- Right Pill Action Buttons -->
                <div class="hidden sm:flex items-center gap-3 shrink-0">
                    <template v-if="user">
                        <Link
                            href="/"
                            class="px-5 py-2.5 rounded-full bg-gradient-to-r from-[#2575E6] to-[#1858B8] hover:from-[#1E64C8] hover:to-[#134798] text-white text-xs sm:text-sm font-extrabold shadow-md transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center gap-1.5"
                        >
                            <span>Dashboard ({{ user.name }})</span>
                            <span aria-hidden="true">&rarr;</span>
                        </Link>
                    </template>
                    <template v-else>
                        <Link
                            href="/login"
                            class="px-5 py-2.5 rounded-full bg-white hover:bg-blue-50 border border-blue-200/90 text-[#1E3A5F] text-xs sm:text-sm font-extrabold shadow-2xs transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center"
                        >
                            Masuk Akun
                        </Link>
                        <Link
                            href="/login?register=1"
                            class="px-5 py-2.5 rounded-full bg-gradient-to-r from-[#2575E6] to-[#1858B8] hover:from-[#1E64C8] hover:to-[#134798] text-white text-xs sm:text-sm font-extrabold shadow-md transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center gap-1.5"
                        >
                            <span>Daftar Murid Baru</span>
                            <span aria-hidden="true">&rarr;</span>
                        </Link>
                    </template>
                </div>

                <!-- Mobile Hamburger Button -->
                <button
                    type="button"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    class="lg:hidden w-11 h-11 rounded-2xl border border-blue-200 bg-white flex items-center justify-center text-[#0E2A47] hover:bg-blue-50 cursor-pointer"
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
            <div v-if="mobileMenuOpen" class="lg:hidden border-t border-blue-100 bg-white px-4 pt-3 pb-5 space-y-3 shadow-lg">
                <div class="grid grid-cols-1 gap-1 text-sm font-bold text-[#0E2A47]">
                    <a href="#beranda" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl bg-[#EBF3FE] text-[#1E6AD4]">
                        Beranda
                    </a>
                    <a href="#aktivitas" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#1E6AD4]">
                        4 Materi Unggulan
                    </a>
                    <a href="#program" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#1E6AD4]">
                        Program Kelas Usia
                    </a>
                    <a href="#jadwal" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#1E6AD4]">
                        Jadwal Kelas
                    </a>
                    <a href="#portal" @click="mobileMenuOpen = false" class="px-3.5 py-2.5 rounded-xl hover:bg-[#EBF3FE] hover:text-[#1E6AD4]">
                        Portal Orang Tua
                    </a>
                </div>
                <div class="pt-3 border-t border-slate-100 flex flex-col sm:hidden gap-2">
                    <Link
                        v-if="user"
                        href="/"
                        class="w-full py-3 rounded-full bg-[#1E6AD4] text-white text-center text-sm font-extrabold shadow-sm"
                    >
                        Buka Dashboard ({{ user.name }})
                    </Link>
                    <template v-else>
                        <Link
                            href="/login?register=1"
                            class="w-full py-3 rounded-full bg-[#1E6AD4] text-white text-center text-sm font-extrabold shadow-sm"
                        >
                            Daftar Murid Baru &rarr;
                        </Link>
                        <Link
                            href="/login"
                            class="w-full py-3 rounded-full border border-blue-200 text-[#0E2A47] text-center text-sm font-extrabold"
                        >
                            Masuk Akun
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <main id="beranda" class="flex-1">
            <!-- 2. HERO SECTION: Persis Gambar Referensi -->
            <section class="relative bg-gradient-to-b from-[#E5F1FF] via-[#F2F8FF] to-white pt-8 pb-14 sm:pt-12 sm:pb-18 lg:pt-14 lg:pb-20 overflow-hidden">
                <!-- Pernak-pernik Latar Lembut (Awan, Bintang, Tanda Plus) -->
                <div class="pointer-events-none select-none" aria-hidden="true">
                    <svg class="hidden lg:block absolute top-10 left-[44%] w-12 h-12 text-[#F59E0B] animate-wiggle-gentle" viewBox="0 0 64 64" fill="none">
                        <!-- Bohlam Ide Ceria -->
                        <circle cx="32" cy="28" r="14" fill="#FDE047" stroke="#F59E0B" stroke-width="3" />
                        <path d="M26 42h12M28 47h8" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" />
                        <path d="M32 6v5M14 14l4 4M50 14l-4 4M10 28h5M54 28h-5" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" />
                    </svg>
                    <span class="hidden sm:block absolute top-24 right-10 text-2xl font-black text-emerald-400/70 animate-float-slow">+</span>
                    <span class="hidden sm:block absolute bottom-28 left-[46%] text-xl font-black text-amber-400/80 animate-float-delayed">★</span>
                </div>

                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-6 items-center">
                        <!-- Left Hero Column (6 Cols) -->
                        <div class="lg:col-span-6 text-center lg:text-left sabila-fade-up">
                            <h1 class="text-[34px] sm:text-5xl lg:text-[46px] xl:text-[52px] font-black tracking-tight leading-[1.15]">
                                <span class="block text-[#0E2A47]">Belajar Ceria</span>
                                <span class="block text-[#1E6AD4] mt-0.5">Membaca, Menulis,</span>
                                <span class="block text-[#F58A07] mt-0.5">Berhitung &amp; Mengaji</span>
                                <span class="inline-flex items-center gap-2.5 text-[#0E2A47] mt-0.5">
                                    <span>Setiap Hari</span>
                                    <!-- 3 Garis Sinar Kuning Ceria seperti di gambar referensi -->
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-[#F59E0B] shrink-0" viewBox="0 0 40 40" fill="none">
                                        <path d="M6 12L22 6" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                                        <path d="M8 21L26 20" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                                        <path d="M6 30L21 33" stroke="currentColor" stroke-width="4" stroke-linecap="round" />
                                    </svg>
                                </span>
                            </h1>

                            <p class="mt-4 sm:mt-5 text-sm sm:text-base lg:text-[17px] text-[#334E68] max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium">
                                Sabila Fun Learning mendampingi putra-putri Ayah &amp; Bunda membangun fondasi akademik dan mengaji Islam secara bertahap dalam kelompok kecil, didampingi guru berpengalaman.
                            </p>

                            <!-- 2 Pill CTAs dengan Ikon Bulat Play -->
                            <div class="mt-6 sm:mt-7 flex flex-wrap items-center justify-center lg:justify-start gap-3.5">
                                <Link
                                    href="/login?register=1"
                                    class="pl-3.5 pr-6 py-3 rounded-full bg-gradient-to-r from-[#2473E0] to-[#1655B4] hover:from-[#1B62C6] hover:to-[#114493] text-white font-extrabold text-sm sm:text-base shadow-lg shadow-blue-500/20 transition-all duration-300 hover:-translate-y-0.5 inline-flex items-center gap-3"
                                >
                                    <span class="w-7 h-7 rounded-full bg-white text-[#1E6AD4] flex items-center justify-center shadow-2xs shrink-0">
                                        <svg class="w-3.5 h-3.5 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </span>
                                    <span>Daftarkan Anak Sekarang &rarr;</span>
                                </Link>

                                <Link
                                    href="/login"
                                    class="pl-3.5 pr-6 py-3 rounded-full bg-white hover:bg-blue-50/70 border border-blue-200 text-[#1E3A5F] font-extrabold text-sm sm:text-base shadow-sm transition-all duration-300 hover:-translate-y-0.5 inline-flex items-center gap-3"
                                >
                                    <span class="w-7 h-7 rounded-full bg-[#E6F0FF] text-[#1E6AD4] flex items-center justify-center shrink-0">
                                        <svg class="w-3.5 h-3.5 ml-0.5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </span>
                                    <span>Masuk Portal Orang Tua</span>
                                </Link>
                            </div>

                            <!-- 3 Highlights Strip Card di Bawah CTA (4 Materi Inti | 8-10 Anak | Laporan Harian) -->
                            <div class="mt-8 bg-white/95 rounded-2xl p-3.5 sm:p-4 border border-blue-100 shadow-md max-w-xl mx-auto lg:mx-0 grid grid-cols-3 divide-x divide-slate-200/80 text-left">
                                <!-- Item 1 -->
                                <div class="flex items-center gap-2.5 pr-2 sm:pr-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#E7F1FF] text-[#1E6AD4] flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 4h-1V2h-2v2H8V2H6v2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2zm0 16H5V10h14v10zm-7-8H7v5h5v-5z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-black text-[#0E2A47] truncate">4 Materi Inti</p>
                                        <p class="text-[11px] text-slate-500 font-semibold truncate">Belajar &amp; Iqra</p>
                                    </div>
                                </div>

                                <!-- Item 2 -->
                                <div class="flex items-center gap-2.5 px-2 sm:px-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#E7F1FF] text-[#1E6AD4] flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-black text-[#0E2A47] truncate">8 - 10 Anak</p>
                                        <p class="text-[11px] text-slate-500 font-semibold truncate">Dalam Satu Kelas</p>
                                    </div>
                                </div>

                                <!-- Item 3 -->
                                <div class="flex items-center gap-2.5 pl-2 sm:pl-3">
                                    <div class="w-10 h-10 rounded-xl bg-[#FFF3D6] text-[#F59E0B] flex items-center justify-center shrink-0">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l7.1-1.01L12 2z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-black text-[#0E2A47] truncate">Laporan Harian</p>
                                        <p class="text-[11px] text-slate-500 font-semibold truncate">Untuk Orang Tua</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Hero Column: Anak Laki-laki (Kartu ب) & Anak Perempuan Berhijab (Kartu 123) + Balon Kata (6 Cols) -->
                        <div class="lg:col-span-6 relative sabila-fade-up-delayed">
                            <!-- Speech Bubble Cloud Kanan Atas: "Anak Hebat Dimulai dari Belajar yang Seru!" -->
                            <div class="hidden sm:block absolute -top-3 right-2 z-20 animate-float-slow">
                                <div class="relative bg-white px-4 py-2.5 rounded-3xl shadow-md border border-blue-100 text-center max-w-[185px]">
                                    <!-- 3 garis sinar kuning di atas balon -->
                                    <svg class="w-6 h-6 text-[#F59E0B] absolute -top-4 -right-2" viewBox="0 0 24 24" fill="none">
                                        <path d="M4 10L10 4M11 13L18 9M13 18L20 17" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" />
                                    </svg>
                                    <p class="text-xs font-black text-[#0E2A47] leading-snug">
                                        Anak Hebat Dimulai dari Belajar yang Seru!
                                    </p>
                                </div>
                            </div>

                            <!-- Bingkai Ilustrasi Hero Anak Laki-laki & Perempuan Berhijab -->
                            <div class="relative mx-auto max-w-xl rounded-[36px] overflow-hidden shadow-xl border-4 border-white bg-[#FDF8EC]">
                                <img
                                    src="/images/sabila-hero-kids.jpg"
                                    alt="Anak laki-laki memegang kartu huruf hijaiyah Ba dan anak perempuan berhijab memegang kartu angka 123 di Sabila Fun Learning"
                                    class="w-full h-[300px] sm:h-[390px] object-cover object-center"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 3. SECTION: EMPAT MATERI UNGGULAN DALAM SETIAP SESI BELAJAR (#aktivitas) -->
            <section id="aktivitas" class="py-12 sm:py-16 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                    <!-- Section Heading dengan Ornamen Sinar Kuning Kiri & Kanan -->
                    <div class="text-center max-w-3xl mx-auto space-y-2.5">
                        <div class="inline-flex items-center justify-center gap-3">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-[#F59E0B] shrink-0" viewBox="0 0 32 32" fill="none">
                                <path d="M26 8L14 4M24 16L10 16M26 24L14 28" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" />
                            </svg>
                            <h2 class="text-2xl sm:text-3xl lg:text-[32px] font-black text-[#0E2A47]">
                                Empat Materi Unggulan dalam Setiap Sesi Belajar
                            </h2>
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-[#F59E0B] shrink-0" viewBox="0 0 32 32" fill="none">
                                <path d="M6 8L18 4M8 16L22 16M6 24L18 28" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <p class="text-sm sm:text-base text-[#334E68] font-medium leading-relaxed">
                            Metode pembelajaran yang menyenangkan dan terstruktur untuk mengembangkan potensi anak di bidang membaca, menulis, berhitung, dan mengaji.
                        </p>
                    </div>

                    <!-- 4 Kartu Pastel Materi (Membaca, Menulis, Berhitung, Mengaji) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                        <button
                            v-for="topic in coreTopics"
                            :key="topic.key"
                            type="button"
                            @click="activeTopicKey = topic.key"
                            :class="[
                                'group relative rounded-[28px] p-6 pt-10 text-center border-2 transition-all duration-300 cursor-pointer flex flex-col items-center',
                                topic.cardBg,
                                activeTopicKey === topic.key
                                    ? 'border-[#1E6AD4] shadow-lg -translate-y-1.5 ring-4 ring-blue-100'
                                    : `${topic.cardBorder} hover:-translate-y-1 hover:shadow-md`,
                            ]"
                        >
                            <!-- Ikon 3D Menonjol di Atas Kartu -->
                            <div class="w-16 h-16 rounded-2xl bg-white shadow-md border border-white flex items-center justify-center -mt-16 mb-3 transition-transform duration-300 group-hover:scale-110">
                                <!-- 1. Ikon Buku Membaca -->
                                <svg v-if="topic.key === 'membaca'" class="w-10 h-10" viewBox="0 0 48 48" fill="none">
                                    <path d="M6 12c0-2.2 1.8-4 4-4h10c2.2 0 4 1.8 4 4v26c0-1.7-1.3-3-3-3H10c-2.2 0-4-1.8-4-4V12z" fill="#3B82F6" />
                                    <path d="M42 12c0-2.2-1.8-4-4-4H28c-2.2 0-4 1.8-4 4v26c0-1.7 1.3-3 3-3h11c2.2 0 4-1.8 4-4V12z" fill="#60A5FA" />
                                    <path d="M11 16h8M11 22h8M29 16h8M29 22h8" stroke="white" stroke-width="2.5" stroke-linecap="round" />
                                </svg>

                                <!-- 2. Ikon Pensil Menulis -->
                                <svg v-else-if="topic.key === 'menulis'" class="w-10 h-10" viewBox="0 0 48 48" fill="none">
                                    <path d="M30 8l10 10-20 20H10V28L30 8z" fill="#F59E0B" />
                                    <path d="M26 12l10 10" stroke="#FDE68A" stroke-width="3" />
                                    <path d="M10 38l6-2-4-4-2 6z" fill="#1E293B" />
                                    <rect x="28" y="26" width="14" height="6" rx="3" transform="rotate(-45 28 26)" fill="#22C55E" />
                                </svg>

                                <!-- 3. Ikon 123 Berhitung -->
                                <div v-else-if="topic.key === 'berhitung'" class="text-2xl font-black tracking-tighter text-[#F58A07] drop-shadow-2xs">
                                    123
                                </div>

                                <!-- 4. Ikon Kubah Masjid Mengaji -->
                                <svg v-else class="w-10 h-10" viewBox="0 0 48 48" fill="none">
                                    <path d="M24 8c-7 5-12 10-12 16h24c0-6-5-11-12-16z" fill="#F59E0B" />
                                    <rect x="10" y="24" width="28" height="16" rx="2" fill="#7C3AED" />
                                    <path d="M20 40v-8a4 4 0 018 0v8" fill="#FDE68A" />
                                    <circle cx="24" cy="6" r="2" fill="#F59E0B" />
                                </svg>
                            </div>

                            <h3 class="text-lg sm:text-xl font-black text-[#0E2A47]">
                                {{ topic.title }}
                            </h3>
                            <p class="mt-2 text-xs sm:text-sm text-[#334E68] font-medium leading-relaxed">
                                {{ topic.shortDesc }}
                            </p>
                        </button>
                    </div>

                    <!-- 4. BANNER HIGHLIGHT: METODE SUKU KATA TANPA MENGEJA (Interaktif mengikuti kartu di atas) -->
                    <Transition name="topic-fade" mode="out-in">
                        <div
                            :key="activeTopic.key"
                            class="relative rounded-[32px] bg-gradient-to-r from-[#DCEBFE] via-[#E8F3FF] to-[#DFEDFF] border border-blue-200/80 p-6 sm:p-8 lg:p-10 shadow-sm overflow-hidden"
                        >
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                                <!-- Left: Visual Laptop Ustadzah + Balon "Belajar Lebih Seru!" (4 Cols) -->
                                <div class="lg:col-span-4 relative">
                                    <div class="relative rounded-2xl overflow-hidden shadow-md border-2 border-white bg-white">
                                        <img
                                            src="/images/sabila-laptop-ustadzah.jpg"
                                            alt="Metode pembelajaran interaktif Sabila Fun Learning"
                                            class="w-full h-52 sm:h-56 object-cover object-center"
                                        />
                                    </div>
                                    <!-- Balon "Belajar Lebih Seru!" -->
                                    <div class="absolute -bottom-3 -right-2 sm:right-2 bg-white px-4 py-2 rounded-2xl shadow-md border border-blue-100 animate-float-slow">
                                        <p class="text-xs font-black text-[#0E2A47]">Belajar Lebih Seru!</p>
                                    </div>
                                </div>

                                <!-- Middle: Metode Suku Kata Tanpa Mengeja (5 Cols) -->
                                <div class="lg:col-span-5 space-y-3">
                                    <span class="inline-block px-3.5 py-1 rounded-full bg-[#C7DFFF] text-[#1554B4] text-xs font-extrabold">
                                        {{ activeTopic.bannerBadge }}
                                    </span>
                                    <h3 class="text-2xl sm:text-3xl font-black text-[#0E2A47] leading-tight">
                                        {{ activeTopic.bannerTitle }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[#334E68] leading-relaxed font-medium">
                                        {{ activeTopic.bannerDesc }}
                                    </p>
                                </div>

                                <!-- Right: 3 Poin Centang Hijau + Tombol Pill Biru (3 Cols) -->
                                <div class="lg:col-span-3 space-y-4">
                                    <ul class="space-y-2.5">
                                        <li
                                            v-for="item in activeTopic.benefits"
                                            :key="item"
                                            class="flex items-center gap-2.5 text-xs sm:text-sm font-bold text-[#0E2A47]"
                                        >
                                            <span class="w-5 h-5 rounded-full bg-[#22C55E] text-white flex items-center justify-center text-xs shrink-0 shadow-2xs">
                                                ✓
                                            </span>
                                            <span>{{ item }}</span>
                                        </li>
                                    </ul>

                                    <div class="pt-1">
                                        <a
                                            href="#program"
                                            class="w-full sm:w-auto px-6 py-3 rounded-full bg-gradient-to-r from-[#2575E6] to-[#1858B8] hover:from-[#1B62C6] hover:to-[#114493] text-white text-xs sm:text-sm font-extrabold shadow-md transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center justify-center gap-2"
                                        >
                                            <span>Pelajari Lebih Lanjut</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Transition>
                </div>
            </section>

            <!-- 5. SECTION: PILIHAN KELAS SESUAI TAHAP USIA & KESIAPAN ANAK (#program) -->
            <section id="program" class="py-12 sm:py-16 bg-gradient-to-b from-white via-[#F5F9FF] to-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                    <!-- Section Header dengan Sinar Kuning Kiri & Kanan -->
                    <div class="text-center max-w-3xl mx-auto space-y-2">
                        <div class="inline-flex items-center justify-center gap-3">
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-[#F59E0B] shrink-0" viewBox="0 0 32 32" fill="none">
                                <path d="M26 8L14 4M24 16L10 16M26 24L14 28" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" />
                            </svg>
                            <h2 class="text-2xl sm:text-3xl lg:text-[32px] font-black text-[#0E2A47]">
                                Pilihan Kelas Sesuai Tahap Usia &amp; Kesiapan Anak
                            </h2>
                            <svg class="w-6 h-6 sm:w-7 sm:h-7 text-[#F59E0B] shrink-0" viewBox="0 0 32 32" fill="none">
                                <path d="M6 8L18 4M8 16L22 16M6 24L18 28" stroke="currentColor" stroke-width="3.2" stroke-linecap="round" />
                            </svg>
                        </div>
                        <p class="text-sm sm:text-base text-[#334E68] font-medium">
                            Setiap anak punya ritme belajar yang berbeda. Pilih program yang paling sesuai untuk buah hati Anda.
                        </p>
                    </div>

                    <!-- 4 Kartu Kelas Usia dengan Foto Anak & Pill Usia di Sudut Atas -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div
                            v-for="prog in agePrograms"
                            :key="prog.title"
                            class="bg-white rounded-[28px] p-3.5 border border-blue-100/90 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between group"
                        >
                            <div>
                                <!-- Gambar Anak dengan Badge Usia -->
                                <div class="relative rounded-2xl overflow-hidden h-44 bg-[#EEF5FF]">
                                    <img
                                        :src="prog.image"
                                        :alt="prog.title"
                                        class="w-full h-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                                    />
                                    <span
                                        :class="[
                                            'absolute top-3 left-3 px-3 py-1 rounded-full text-[11px] font-extrabold shadow-sm',
                                            prog.badgeClass,
                                        ]"
                                    >
                                        {{ prog.ageBadge }}
                                    </span>
                                </div>

                                <!-- Konten Teks Kartu -->
                                <div class="px-2 pt-4 pb-2 space-y-1.5">
                                    <h3 class="text-lg font-black text-[#0E2A47]">
                                        {{ prog.title }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-[#334E68] font-medium leading-relaxed">
                                        {{ prog.description }}
                                    </p>
                                </div>
                            </div>

                            <div class="px-2 pt-3 pb-1">
                                <Link
                                    :href="`/login?register=1&group=${encodeURIComponent(prog.groupParam)}`"
                                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-extrabold text-[#1E6AD4] group-hover:text-[#144DA0] transition-colors"
                                >
                                    <span>Lihat Detail</span>
                                    <span class="transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 6. SECTION: JADWAL & KUOTA KELOMPOK KELAS (#jadwal) -->
            <section id="jadwal" class="py-12 sm:py-16 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                        <div class="space-y-1.5 max-w-2xl">
                            <span class="inline-block px-3 py-1 rounded-full bg-[#E7F1FF] text-[#1E6AD4] text-xs font-extrabold">
                                Jadwal Sesi Belajar
                            </span>
                            <h2 class="text-2xl sm:text-3xl font-black text-[#0E2A47]">
                                Jadwal &amp; Kuota Kelompok Kelas Tersedia
                            </h2>
                            <p class="text-sm sm:text-base text-[#334E68] font-medium">
                                Pilih jadwal hari dan jam belajar yang paling sesuai untuk buah hati Anda.
                            </p>
                        </div>
                        <Link
                            href="/login?register=1"
                            class="px-5 py-2.5 rounded-full bg-[#1E6AD4] hover:bg-[#1554B4] text-white text-xs sm:text-sm font-extrabold self-start sm:self-auto transition shadow-sm"
                        >
                            + Daftar Murid Baru
                        </Link>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div
                            v-for="cls in displayClasses"
                            :key="cls.id"
                            class="bg-[#F8FBFF] rounded-[24px] border border-blue-100 hover:border-[#1E6AD4] p-5 flex flex-col justify-between space-y-4 shadow-2xs hover:shadow-md transition-all duration-300"
                        >
                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-3 py-0.5 rounded-full bg-[#E7F1FF] text-[#1E6AD4] text-xs font-extrabold">
                                        {{ cls.category }}
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full bg-[#E9F8EC] text-[#16793B] text-xs font-extrabold">
                                        Kuota: {{ cls.remaining_quota }}/{{ cls.quota }}
                                    </span>
                                </div>

                                <h3 class="text-base font-black text-[#0E2A47]">
                                    {{ cls.name }}
                                </h3>

                                <p class="text-xs text-[#334E68] font-medium leading-relaxed">
                                    {{ cls.description }}
                                </p>

                                <div class="pt-2 border-t border-blue-100/80 flex items-center gap-2 text-xs font-extrabold text-[#1E3A5F]">
                                    <svg class="w-4 h-4 text-[#1E6AD4] shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                        <rect x="3" y="4" width="18" height="18" rx="2" />
                                        <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                                    </svg>
                                    <span>{{ cls.schedule }}</span>
                                </div>
                            </div>

                            <div class="pt-2 flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-500">Pendaftaran Aktif</span>
                                <Link
                                    :href="`/login?register=1&group=${encodeURIComponent(cls.name)}`"
                                    class="px-4 py-1.5 rounded-full bg-[#1E6AD4] hover:bg-[#1554B4] text-white text-xs font-extrabold transition shadow-2xs"
                                >
                                    Pilih Kelas &rarr;
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 7. BANNER: MENGAPA MEMILIH SABILA FUN LEARNING? (#portal) - Persis Gambar Referensi -->
            <section id="portal" class="py-10 sm:py-14 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="relative rounded-[32px] bg-gradient-to-r from-[#DFECFF] via-[#EFF6FF] to-[#FFF7E2] border border-blue-100 p-6 sm:p-8 lg:p-10 shadow-sm overflow-hidden">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-center">
                            <!-- Kiri: Judul, Deskripsi, Tombol Tentang Sabila (4 Cols) -->
                            <div class="lg:col-span-4 space-y-3 text-center lg:text-left">
                                <h2 class="text-2xl sm:text-[28px] font-black text-[#0E2A47] leading-tight">
                                    Mengapa Memilih Sabila Fun Learning?
                                </h2>
                                <p class="text-xs sm:text-sm text-[#334E68] font-medium leading-relaxed">
                                    Karena kami berkomitmen memberikan pendidikan terbaik dengan cara yang menyenangkan dan efektif.
                                </p>
                                <div class="pt-1 flex flex-wrap justify-center lg:justify-start gap-2.5">
                                    <Link
                                        href="/login"
                                        class="px-5 py-2.5 rounded-full bg-gradient-to-r from-[#2575E6] to-[#1858B8] hover:from-[#1B62C6] hover:to-[#114493] text-white text-xs sm:text-sm font-extrabold shadow-md transition-all duration-200 hover:-translate-y-0.5 inline-flex items-center gap-1.5"
                                    >
                                        <span>Tentang Sabila</span>
                                        <span aria-hidden="true">&rarr;</span>
                                    </Link>
                                </div>
                            </div>

                            <!-- Tengah: 4 Kartu Putih Ikon (Guru Berpengalaman, Metode Terbukti, Pendampingan Orang Tua, Laporan Berkala) (5 Cols) -->
                            <div class="lg:col-span-5 grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <!-- 1. Guru Berpengalaman (Ikon Piala Emas) -->
                                <div class="bg-white rounded-2xl p-3.5 text-center shadow-xs border border-white flex flex-col items-center justify-center gap-2 hover:-translate-y-1 transition-transform">
                                    <div class="w-11 h-11 rounded-2xl bg-[#FFF6DB] flex items-center justify-center">
                                        <svg class="w-7 h-7 text-[#F59E0B]" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M19 5h-2V3H7v2H5c-1.1 0-2 .9-2 2v1c0 2.55 1.92 4.63 4.39 4.94A5.01 5.01 0 0011 15.9V19H7v2h10v-2h-4v-3.1a5.01 5.01 0 003.61-2.96C19.08 12.63 21 10.55 21 8V7c0-1.1-.9-2-2-2zM5 8V7h2v3.82C5.84 10.4 5 9.3 5 8zm14 0c0 1.3-.84 2.4-2 2.82V7h2v1z" />
                                        </svg>
                                    </div>
                                    <p class="text-[11px] sm:text-xs font-black text-[#0E2A47] leading-tight">
                                        Guru Berpengalaman
                                    </p>
                                </div>

                                <!-- 2. Metode Terbukti (Ikon Perisai Hijau Centang) -->
                                <div class="bg-white rounded-2xl p-3.5 text-center shadow-xs border border-white flex flex-col items-center justify-center gap-2 hover:-translate-y-1 transition-transform">
                                    <div class="w-11 h-11 rounded-2xl bg-[#E8F8EE] flex items-center justify-center">
                                        <svg class="w-7 h-7 text-[#22C55E]" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z" />
                                        </svg>
                                    </div>
                                    <p class="text-[11px] sm:text-xs font-black text-[#0E2A47] leading-tight">
                                        Metode Terbukti
                                    </p>
                                </div>

                                <!-- 3. Pendampingan Orang Tua (Ikon Hati Merah) -->
                                <div class="bg-white rounded-2xl p-3.5 text-center shadow-xs border border-white flex flex-col items-center justify-center gap-2 hover:-translate-y-1 transition-transform">
                                    <div class="w-11 h-11 rounded-2xl bg-[#FFEBEB] flex items-center justify-center">
                                        <svg class="w-7 h-7 text-[#EF4444]" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                        </svg>
                                    </div>
                                    <p class="text-[11px] sm:text-xs font-black text-[#0E2A47] leading-tight">
                                        Pendampingan Orang Tua
                                    </p>
                                </div>

                                <!-- 4. Laporan Berkala (Ikon Headset/Laporan Biru) -->
                                <div class="bg-white rounded-2xl p-3.5 text-center shadow-xs border border-white flex flex-col items-center justify-center gap-2 hover:-translate-y-1 transition-transform">
                                    <div class="w-11 h-11 rounded-2xl bg-[#E7F1FF] flex items-center justify-center">
                                        <svg class="w-7 h-7 text-[#1E6AD4]" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 1a9 9 0 00-9 9v7c0 1.66 1.34 3 3 3h3v-8H5v-2c0-3.87 3.13-7 7-7s7 3.13 7 7v2h-4v8h3c1.66 0 3-1.34 3-3v-7a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <p class="text-[11px] sm:text-xs font-black text-[#0E2A47] leading-tight">
                                        Laporan Berkala
                                    </p>
                                </div>
                            </div>

                            <!-- Kanan: Gambar Anak Ceria & Bintang Kuning (3 Cols) -->
                            <div class="lg:col-span-3 flex justify-center lg:justify-end relative">
                                <div class="relative w-48 sm:w-56 h-44 sm:h-48 rounded-3xl overflow-hidden border-4 border-white shadow-md bg-white">
                                    <img
                                        src="/images/class-calistung-lanjutan.jpg"
                                        alt="Anak ceria belajar di Sabila Fun Learning"
                                        class="w-full h-full object-cover object-top"
                                    />
                                </div>
                                <svg class="w-7 h-7 text-[#F59E0B] absolute -top-2 right-2 animate-wiggle-gentle" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l7.1-1.01L12 2z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- 8. FOOTER NAVY TUA DENGAN WAVE DIVIDER & KARTU NEWSLETTER PUTIH (Persis Gambar Referensi) -->
        <footer class="relative bg-[#0B2240] text-white pt-16 pb-10 mt-6 overflow-hidden">
            <!-- Top Wave Divider -->
            <div class="absolute top-0 left-0 right-0 overflow-hidden leading-none pointer-events-none" aria-hidden="true">
                <svg class="relative block w-full h-8 sm:h-11 text-white" viewBox="0 0 1440 60" preserveAspectRatio="none" fill="currentColor">
                    <path d="M0 0H1440V25C1200 60 960 5 720 30C480 55 240 10 0 35V0Z" />
                </svg>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 items-start">
                    <!-- Col 1: Logo & Tagline (3 Cols) -->
                    <div class="lg:col-span-3 space-y-3">
                        <div class="inline-flex items-center bg-white rounded-2xl px-3.5 py-2 shadow-xs">
                            <img
                                src="/logo-sabiladunlearning.png"
                                alt="Sabila Fun Learning"
                                class="h-10 w-auto object-contain"
                            />
                        </div>
                        <p class="text-xs sm:text-sm text-blue-100/90 font-semibold">
                            Belajar Ceria, Masa Depan Cerah
                        </p>
                    </div>

                    <!-- Col 2: Navigasi Cepat (2 Cols) -->
                    <div class="lg:col-span-2 space-y-2.5">
                        <h3 class="text-sm font-extrabold text-white">Navigasi Cepat</h3>
                        <ul class="space-y-2 text-xs sm:text-sm text-blue-100/80 font-medium">
                            <li><a href="#beranda" class="hover:text-white transition">Beranda</a></li>
                            <li><a href="#program" class="hover:text-white transition">Program Kelas</a></li>
                            <li><a href="#jadwal" class="hover:text-white transition">Jadwal Kelas</a></li>
                            <li><a href="#portal" class="hover:text-white transition">Portal Orang Tua</a></li>
                        </ul>
                    </div>

                    <!-- Col 3: Kontak Kami (2.5 Cols) -->
                    <div class="lg:col-span-2 space-y-2.5">
                        <h3 class="text-sm font-extrabold text-white">Kontak Kami</h3>
                        <ul class="space-y-2 text-xs sm:text-sm text-blue-100/85 font-medium">
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#F59E0B] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M6.62 10.79a15.05 15.05 0 006.59 6.59l2.2-2.2a1 1 0 011.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 011 1V20a1 1 0 01-1 1C10.61 21 3 13.39 3 4a1 1 0 011-1h3.5a1 1 0 011 1c0 1.25.2 2.46.57 3.58a1 1 0 01-.25 1.01l-2.2 2.2z" />
                                </svg>
                                <span>0812 3456 7890</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#60A5FA] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" />
                                </svg>
                                <span>info@sabila.id</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-[#F59E0B] shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z" />
                                </svg>
                                <span>Tasikmalaya, Indonesia</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Col 4: Ikuti Kami (2 Cols) -->
                    <div class="lg:col-span-2 space-y-2.5">
                        <h3 class="text-sm font-extrabold text-white">Ikuti Kami</h3>
                        <div class="flex items-center gap-2.5">
                            <a
                                href="#beranda"
                                aria-label="YouTube Sabila Fun Learning"
                                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21.8 8.001a2.75 2.75 0 00-1.94-1.94C18.14 5.6 12 5.6 12 5.6s-6.14 0-7.86.46a2.75 2.75 0 00-1.94 1.94C1.74 9.72 1.74 12 1.74 12s0 2.28.46 3.999a2.75 2.75 0 001.94 1.94c1.72.46 7.86.46 7.86.46s6.14 0 7.86-.46a2.75 2.75 0 001.94-1.94c.46-1.72.46-4 .46-4s0-2.28-.46-3.999zM9.95 15.02V8.98L15.2 12l-5.25 3.02z" />
                                </svg>
                            </a>
                            <a
                                href="#beranda"
                                aria-label="Instagram Sabila Fun Learning"
                                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 01-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 017.8 2zm-.2 2A3.6 3.6 0 004 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 003.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6zm9.65 1.5a1.25 1.25 0 110 2.5 1.25 1.25 0 010-2.5zM12 7a5 5 0 110 10 5 5 0 010-10zm0 2a3 3 0 100 6 3 3 0 000-6z" />
                                </svg>
                            </a>
                            <a
                                href="#beranda"
                                aria-label="Facebook Sabila Fun Learning"
                                class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M14 13.5h2.5l1-4H14v-2c0-1.03 0-2 2-2h1.5V2.14c-.326-.043-1.557-.14-2.857-.14C11.928 2 10 3.657 10 6.7v2.8H7v4h3V22h4v-8.5z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <!-- Col 5: Kartu Putih "Dapatkan Informasi Terbaru" (3 Cols) -->
                    <div class="lg:col-span-3">
                        <div class="bg-white text-[#0E2A47] rounded-2xl p-4 shadow-md space-y-2.5">
                            <h3 class="text-sm font-black text-[#0E2A47]">Dapatkan Informasi Terbaru</h3>
                            <p class="text-[11px] text-slate-500 font-medium">
                                Tips belajar dan info program terbaru
                            </p>
                            <form @submit.prevent="submitNewsletter" class="flex items-center gap-1.5">
                                <input
                                    v-model="newsletterEmail"
                                    type="email"
                                    required
                                    placeholder="Email Anda"
                                    class="w-full px-3 py-2 rounded-xl bg-[#F2F6FC] border border-blue-100 text-xs text-[#0E2A47] placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-[#1E6AD4]"
                                />
                                <button
                                    type="submit"
                                    aria-label="Kirim email berlangganan"
                                    class="w-9 h-9 rounded-xl bg-[#1E6AD4] hover:bg-[#1554B4] text-white flex items-center justify-center shrink-0 cursor-pointer transition"
                                >
                                    <span class="text-xs font-bold">&#8599;</span>
                                </button>
                            </form>
                            <p v-if="newsletterSubmitted" class="text-[11px] font-bold text-emerald-600">
                                Terima kasih! Email Anda telah terdaftar.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Copyright Bar -->
                <div class="mt-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-blue-200/75">
                    <p>&copy; {{ new Date().getFullYear() }} Sabila Fun Learning. Semua hak dilindungi.</p>
                    <div class="flex items-center gap-3">
                        <a href="#beranda" class="hover:text-white transition">Syarat &amp; Ketentuan</a>
                        <span>|</span>
                        <a href="#beranda" class="hover:text-white transition">Kebijakan Privasi</a>
                    </div>
                </div>
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
        transform: translateY(-6px);
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
