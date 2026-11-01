<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../Layouts/MobileAppLayout.vue';

const props = defineProps({
    latestActivity: {
        type: Object,
        default: null,
    },
    latestReportId: {
        type: Number,
        default: null,
    },
    currentInvoice: {
        type: Object,
        default: null,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const children = computed(() => page.props.auth?.children || []);
const activeChild = computed(() => page.props.auth?.activeChild);
const classGroups = computed(() => page.props.auth?.classGroups || []);
const pendingEnrollmentsCount = computed(() => page.props.auth?.pendingEnrollmentsCount || 0);
const unreadCount = computed(() => page.props.auth?.unreadNotificationsCount || 0);

const showChildDropdown = ref(false);
const showEnrollChildModal = ref(false);
const showLeaveModal = ref(false);
const enrollPhotoPreview = ref(null);

const enrollForm = useForm({
    name: '',
    phone: '',
    birth_date: '2022-08-15',
    gender: 'laki-laki',
    address: '',
    class_group_id: classGroups.value[0]?.id || '',
    photo: null,
});

const leaveForm = useForm({
    child_id: activeChild.value?.id || '',
    report_date: '2026-10-14',
    attendance_status: 'izin',
    absence_reason: '',
});

function handleKeydown(e) {
    if (e.key === 'Escape') {
        showChildDropdown.value = false;
        showEnrollChildModal.value = false;
        showLeaveModal.value = false;
    }
}

onMounted(() => window.addEventListener('keydown', handleKeydown));
onUnmounted(() => window.removeEventListener('keydown', handleKeydown));

function selectChild(childId) {
    showChildDropdown.value = false;
    router.post(
        `/children/${childId}/select`,
        {},
        {
            preserveState: false,
            preserveScroll: true,
        }
    );
}

function openEnrollModal() {
    showChildDropdown.value = false;
    enrollForm.class_group_id = classGroups.value[0]?.id || '';
    enrollForm.address = activeChild.value?.address || page.props.auth?.user?.address || '';
    enrollPhotoPreview.value = null;
    showEnrollChildModal.value = true;
}

function handleEnrollPhoto(e) {
    const file = e.target.files[0];
    if (file) {
        enrollForm.photo = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            enrollPhotoPreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        enrollForm.photo = null;
        enrollPhotoPreview.value = null;
    }
}

function submitEnrollChild() {
    enrollForm.post('/children/enroll', {
        forceFormData: true,
        onSuccess: () => {
            showEnrollChildModal.value = false;
            enrollPhotoPreview.value = null;
            enrollForm.reset('name', 'photo', 'address');
        },
    });
}

function openLeaveModal() {
    leaveForm.child_id = activeChild.value?.id || '';
    showLeaveModal.value = true;
}

function submitLeaveRequest() {
    leaveForm.child_id = activeChild.value?.id || '';
    leaveForm.post('/laporan-harian/izin', {
        onSuccess: () => {
            showLeaveModal.value = false;
            leaveForm.reset('absence_reason');
        },
    });
}
</script>

<template>
    <Head title="Beranda" />

    <MobileAppLayout active-tab="beranda">
        <!-- Invisible Full-Screen Click-Outside Backdrop to Dismiss Child Dropdown Anywhere -->
        <div
            v-if="showChildDropdown"
            @click="showChildDropdown = false"
            class="fixed inset-0 z-20 bg-slate-900/10 backdrop-blur-[1px]"
            aria-hidden="true"
        ></div>

        <div class="pt-4 pb-6 space-y-5">
            <!-- Brand Header Bar with Sabila Fun Learning Logo -->
            <div class="flex items-center justify-between gap-3 pb-3 border-b border-slate-200/70">
                <div class="flex items-center gap-2.5">
                    <img
                        src="/logo-sabiladunlearning.png"
                        alt="Sabila Fun Learning"
                        class="h-9 sm:h-11 w-auto object-contain"
                    />
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full bg-white border border-slate-200/90 text-[11px] font-bold text-slate-600 shadow-2xs">
                        {{ user?.role === 'teacher' ? 'Guru' : user?.role === 'admin' ? 'Admin' : 'Orang Tua' }} • {{ user?.name }}
                    </span>
                    <button
                        type="button"
                        @click="router.post('/logout')"
                        class="px-2.5 py-1 rounded-full bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-[11px] font-bold transition cursor-pointer"
                    >
                        Keluar
                    </button>
                </div>
            </div>

            <!-- Top Greeting Bar (Avatar + "Halo, Bunda" + Synchronized Child Selector + Bell) -->
            <div class="flex items-center justify-between gap-3 relative z-30">
                <div class="flex items-center gap-3.5 min-w-0">
                    <img
                        :src="activeChild?.photo_url"
                        :alt="activeChild?.name || 'Anak'"
                        class="w-12 h-12 sm:w-14 sm:h-14 rounded-full object-cover border-2 border-white shadow-xs bg-blue-50 shrink-0"
                    />
                    <div class="min-w-0">
                        <p class="text-lg sm:text-xl font-extrabold text-slate-800 leading-tight">
                            {{ user?.role === 'teacher' ? 'Halo, Bu Guru' : user?.role === 'admin' ? 'Halo, Admin' : 'Halo, Bunda' }}
                        </p>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <!-- Child Selector Pill Button (Displays Active Child Name & Group) -->
                            <button
                                v-if="children.length > 0"
                                type="button"
                                @click.stop="showChildDropdown = !showChildDropdown"
                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white border border-slate-200 shadow-2xs text-xs font-bold text-slate-700 hover:border-[#2E6FBE] hover:text-[#2E6FBE] transition cursor-pointer"
                                :aria-expanded="showChildDropdown"
                            >
                                <span>{{ activeChild?.name }}</span>
                                <span class="text-slate-400 font-medium hidden sm:inline">• {{ activeChild?.group_name }}</span>
                                <svg
                                    :class="['w-3.5 h-3.5 text-slate-500 transition-transform', showChildDropdown ? 'rotate-180' : '']"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2.4"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Quick Add Child Button on Home for Parents -->
                            <button
                                v-if="user?.is_parent || user?.is_admin"
                                type="button"
                                @click="openEnrollModal"
                                class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 transition cursor-pointer"
                            >
                                <span>+ Daftarkan Anak</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notification Bell Button -->
                <Link
                    href="/notifikasi"
                    class="relative w-11 h-11 sm:w-12 sm:h-12 rounded-full bg-white border border-slate-200/80 shadow-2xs flex items-center justify-center text-slate-700 hover:bg-slate-50 transition shrink-0"
                    aria-label="Notifikasi"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span
                        v-if="unreadCount > 0"
                        class="absolute top-2 right-2.5 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-white"
                    ></span>
                </Link>

                <!-- Child Selector Dropdown Popover (Dismisses when clicking anywhere outside) -->
                <div
                    v-if="showChildDropdown"
                    @click.stop
                    class="absolute left-0 top-16 z-30 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-200/90 p-2.5 space-y-1.5"
                >
                    <div class="flex items-center justify-between px-2.5 py-1">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Pilih Profil Anak ({{ children.length }})
                        </p>
                        <button
                            type="button"
                            @click="showChildDropdown = false"
                            class="text-xs font-bold text-slate-400 hover:text-slate-600 px-1.5 py-0.5 rounded-md cursor-pointer"
                        >
                            Tutup ✕
                        </button>
                    </div>

                    <div class="max-h-64 overflow-y-auto space-y-1">
                        <button
                            v-for="child in children"
                            :key="child.id"
                            type="button"
                            @click="selectChild(child.id)"
                            :class="[
                                'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left transition cursor-pointer',
                                activeChild?.id === child.id
                                    ? 'bg-[#EBF3FE] text-[#2E6FBE] ring-1 ring-[#2E6FBE]/30'
                                    : 'hover:bg-slate-50 text-slate-700',
                            ]"
                        >
                            <img :src="child.photo_url" :alt="child.name" class="w-10 h-10 rounded-full object-cover shrink-0 bg-white border border-slate-200" />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <p class="text-sm font-extrabold truncate">{{ child.name }}</p>
                                    <span
                                        v-if="child.enrollment_status === 'pending'"
                                        class="px-2 py-0.2 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold shrink-0"
                                    >
                                        Menunggu Persetujuan
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ child.group_name }}</p>
                            </div>
                            <span v-if="activeChild?.id === child.id" class="text-xs font-extrabold text-[#2E6FBE] bg-white px-2 py-0.5 rounded-full shadow-2xs">
                                Aktif
                            </span>
                        </button>
                    </div>

                    <div class="pt-1.5 mt-1 border-t border-slate-100">
                        <button
                            type="button"
                            @click="openEnrollModal"
                            class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl bg-[#2E6FBE] text-white text-xs font-bold hover:bg-[#245ca3] transition cursor-pointer"
                        >
                            <span>+ Daftarkan Anak Baru</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Active Child Highlight Card (Updates Dynamically with Selected Child's Name & Class) -->
            <div v-if="activeChild" class="space-y-2.5">
                <div class="p-4 sm:p-5 rounded-2xl bg-[#FDECEC] border border-[#F9D5D5] flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
                    <Link
                        href="/profil"
                        class="flex items-center gap-4 min-w-0 flex-1 group"
                    >
                        <img
                            :src="activeChild.photo_url"
                            :alt="activeChild.name"
                            class="w-14 h-14 sm:w-16 sm:h-16 rounded-full object-cover border-2 border-white shadow-xs bg-white shrink-0"
                        />
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h2 class="text-lg sm:text-xl font-extrabold text-slate-800 group-hover:text-[#2E6FBE] transition">
                                    {{ activeChild.name }}
                                </h2>
                                <span
                                    v-if="activeChild.enrollment_status === 'pending'"
                                    class="px-2.5 py-0.5 rounded-full bg-amber-100 border border-amber-300 text-amber-800 text-[11px] font-extrabold"
                                >
                                    Menunggu Persetujuan Admin
                                </span>
                                <span
                                    v-else-if="activeChild.enrollment_status === 'rejected'"
                                    class="px-2.5 py-0.5 rounded-full bg-rose-100 border border-rose-300 text-rose-800 text-[11px] font-extrabold"
                                >
                                    Belum Disetujui
                                </span>
                                <span
                                    v-else
                                    class="px-2.5 py-0.5 rounded-full bg-[#EBF8F0] border border-[#CBEAD7] text-[#1E8E5A] text-[11px] font-extrabold"
                                >
                                    Siswa Aktif
                                </span>
                            </div>
                            <p class="text-xs sm:text-sm font-bold text-slate-700 mt-0.5">
                                {{ activeChild.group_name }}
                            </p>
                        </div>
                    </Link>

                    <div class="flex flex-wrap items-center gap-2 self-end sm:self-center shrink-0">
                        <button
                            type="button"
                            @click="openLeaveModal"
                            class="px-3 py-1.5 rounded-xl bg-[#FEF6E0] hover:brightness-95 border border-[#F9E4B7] text-[#B7791F] text-xs font-extrabold transition cursor-pointer"
                        >
                            + Ajukan Izin / Sakit
                        </button>
                        <button
                            v-if="children.length > 1"
                            type="button"
                            @click.stop="showChildDropdown = !showChildDropdown"
                            class="px-3 py-1.5 rounded-xl bg-white/90 hover:bg-white border border-rose-200 text-slate-700 text-xs font-bold transition cursor-pointer"
                        >
                            Ganti Anak ({{ children.length }})
                        </button>
                        <Link
                            href="/profil"
                            class="px-3 py-1.5 rounded-xl bg-white/90 hover:bg-white border border-rose-200 text-[#C9403C] text-xs font-bold flex items-center gap-1 transition"
                        >
                            <span>Profil</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </Link>
                    </div>
                </div>

                <!-- Pending Approval Info Banner -->
                <div
                    v-if="activeChild.enrollment_status === 'pending'"
                    class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-center justify-between gap-3"
                >
                    <div>
                        <p class="font-bold">Status Pendaftaran: Menunggu Persetujuan Admin</p>
                        <p class="text-xs text-amber-800 mt-0.5">
                            Pemilihan kelas <strong>{{ activeChild.group_name }}</strong> untuk {{ activeChild.name }} sedang diverifikasi kuota oleh Admin Sabila.
                        </p>
                    </div>
                    <Link
                        v-if="user?.is_admin"
                        href="/kelas"
                        class="px-3 py-2 rounded-xl bg-amber-600 text-white font-bold text-xs shrink-0"
                    >
                        Setujui di Kelas
                    </Link>
                </div>
            </div>

            <!-- Teacher & Admin Class Management & Approval Panel -->
            <div
                v-if="user?.can_manage_reports"
                class="p-4 sm:p-5 rounded-2xl bg-white border border-blue-200/90 shadow-2xs space-y-3"
            >
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="text-[11px] font-extrabold uppercase tracking-wider text-[#2E6FBE] bg-[#EBF3FE] px-2.5 py-0.5 rounded-full">
                            {{ user.is_admin ? 'Panel Admin & Guru' : 'Panel Guru Pengajar' }}
                        </span>
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-800 mt-1">
                            Manajemen Kelas, Kuota, Mapping Guru &amp; Daftar Siswa
                        </h3>
                    </div>
                    <Link
                        href="/kelas"
                        class="px-3.5 py-2 rounded-xl bg-[#2E6FBE] text-white text-xs sm:text-sm font-bold hover:bg-[#245ca3] transition"
                    >
                        Buka Menu Kelas →
                    </Link>
                </div>

                <div
                    v-if="pendingEnrollmentsCount > 0 && user.is_admin"
                    class="px-3.5 py-2.5 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between text-xs sm:text-sm"
                >
                    <span class="font-semibold text-amber-900">
                        {{ pendingEnrollmentsCount }} pendaftaran anak menunggu persetujuan masuk kelas
                    </span>
                    <Link href="/kelas" class="font-extrabold text-amber-800 underline">
                        Verifikasi Sekarang
                    </Link>
                </div>

                <!-- Quick Teacher/Admin Actions -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-0.5">
                    <Link
                        href="/laporan-harian"
                        class="px-3 py-2.5 rounded-xl bg-[#EBF8F0] text-[#1E8E5A] text-xs font-bold text-center hover:brightness-95"
                    >
                        + Input Laporan Harian
                    </Link>
                    <Link
                        href="/laporan-bulanan"
                        class="px-3 py-2.5 rounded-xl bg-[#F3EEFD] text-[#6D28D9] text-xs font-bold text-center hover:brightness-95"
                    >
                        + Input Laporan Bulanan
                    </Link>
                    <Link
                        href="/foto-kegiatan"
                        class="px-3 py-2.5 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold text-center hover:brightness-95"
                    >
                        + Unggah Foto Kegiatan
                    </Link>
                    <Link
                        href="/notifikasi"
                        class="px-3 py-2.5 rounded-xl bg-[#FEF6E0] text-[#B7791F] text-xs font-bold text-center hover:brightness-95"
                    >
                        {{ user.is_admin ? '+ Kirim Notifikasi' : 'Lihat Notifikasi' }}
                    </Link>
                </div>
            </div>

            <!-- Responsive Quick Action Grid Cards (2 cols on mobile, 4 cols on tablet/desktop) -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <!-- Laporan Harian -->
                <Link
                    href="/laporan-harian"
                    class="p-4 sm:p-5 rounded-2xl bg-[#EBF3FE] border border-[#D8E7FC] flex flex-col justify-between min-h-[116px] sm:min-h-[130px] hover:brightness-[0.98] transition shadow-2xs"
                >
                    <div class="w-11 h-11 rounded-xl bg-white/90 flex items-center justify-center shadow-2xs">
                        <svg class="w-6 h-6 text-[#22A06B]" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="5" y="3" width="14" height="18" rx="2.5" fill="#22A06B" />
                            <path d="M8 8h8M8 12h8M8 16h5" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div class="mt-3">
                        <span class="text-sm sm:text-[15px] font-extrabold text-slate-800 block">Laporan Harian</span>
                        <span class="text-[11px] text-slate-500 font-medium">Ringkasan belajar harian</span>
                    </div>
                </Link>

                <!-- Laporan Bulanan -->
                <Link
                    href="/laporan-bulanan"
                    class="p-4 sm:p-5 rounded-2xl bg-[#F3EEFD] border border-[#E4D9FA] flex flex-col justify-between min-h-[116px] sm:min-h-[130px] hover:brightness-[0.98] transition shadow-2xs"
                >
                    <div class="w-11 h-11 rounded-xl bg-white/90 flex items-center justify-center shadow-2xs">
                        <svg class="w-6 h-6 text-[#7C3AED]" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="4" y="13" width="4" height="7" rx="1" />
                            <rect x="10" y="9" width="4" height="11" rx="1" />
                            <rect x="16" y="5" width="4" height="15" rx="1" />
                        </svg>
                    </div>
                    <div class="mt-3">
                        <span class="text-sm sm:text-[15px] font-extrabold text-slate-800 block">Laporan Bulanan</span>
                        <span class="text-[11px] text-slate-500 font-medium">Capaian perkembangan</span>
                    </div>
                </Link>

                <!-- Foto Kegiatan -->
                <Link
                    href="/foto-kegiatan"
                    class="p-4 sm:p-5 rounded-2xl bg-[#EBF3FE] border border-[#D8E7FC] flex flex-col justify-between min-h-[116px] sm:min-h-[130px] hover:brightness-[0.98] transition shadow-2xs"
                >
                    <div class="w-11 h-11 rounded-xl bg-white/90 flex items-center justify-center shadow-2xs">
                        <svg class="w-6 h-6 text-[#2E6FBE]" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="3" y="4" width="18" height="16" rx="3" fill="#2E6FBE" />
                            <circle cx="8.5" cy="9.5" r="1.8" fill="#FEF08A" />
                            <path d="M5 18l5-5.5 3.5 3.5 3.5-4.5L19 18H5z" fill="#93C5FD" />
                        </svg>
                    </div>
                    <div class="mt-3">
                        <span class="text-sm sm:text-[15px] font-extrabold text-slate-800 block">Foto Kegiatan</span>
                        <span class="text-[11px] text-slate-500 font-medium">Galeri aktivitas kelas</span>
                    </div>
                </Link>

                <!-- Pembayaran -->
                <Link
                    href="/pembayaran"
                    class="p-4 sm:p-5 rounded-2xl bg-[#FEF6E0] border border-[#F9E6B3] flex flex-col justify-between min-h-[116px] sm:min-h-[130px] hover:brightness-[0.98] transition shadow-2xs"
                >
                    <div class="w-11 h-11 rounded-xl bg-white/90 flex items-center justify-center shadow-2xs">
                        <svg class="w-6 h-6 text-[#D97706]" viewBox="0 0 24 24" fill="currentColor">
                            <rect x="3" y="5" width="18" height="14" rx="3" fill="#F59E0B" />
                            <rect x="14" y="10" width="6" height="4" rx="1" fill="#FEF3C7" />
                            <circle cx="16" cy="12" r="1" fill="#B45309" />
                        </svg>
                    </div>
                    <div class="mt-3">
                        <span class="text-sm sm:text-[15px] font-extrabold text-slate-800 block">Pembayaran</span>
                        <span class="text-[11px] text-slate-500 font-medium">Tagihan &amp; riwayat SPP</span>
                    </div>
                </Link>
            </div>

            <!-- Kegiatan Terupdate Section -->
            <section class="pt-1">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-800">
                        Kegiatan Terupdate ({{ activeChild?.name }})
                    </h3>
                    <Link
                        href="/foto-kegiatan"
                        class="text-xs sm:text-sm font-bold text-[#2E6FBE] hover:underline py-1"
                    >
                        Lihat semua
                    </Link>
                </div>

                <Link
                    v-if="latestActivity"
                    :href="latestReportId ? `/laporan-harian/${latestReportId}` : '/foto-kegiatan'"
                    class="block bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden hover:border-slate-300 transition sm:grid sm:grid-cols-12"
                >
                    <div class="aspect-16/10 sm:aspect-auto sm:col-span-5 bg-slate-100 overflow-hidden sm:min-h-[190px]">
                        <img
                            :src="latestActivity.photo_url"
                            :alt="latestActivity.caption"
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="p-4 sm:p-6 sm:col-span-7 flex flex-col justify-center">
                        <span class="inline-block w-fit px-2.5 py-0.5 rounded-full bg-[#EBF3FE] text-[#2E6FBE] text-[11px] font-bold mb-2">
                            {{ latestActivity.category }} • {{ activeChild?.group_name }}
                        </span>
                        <h4 class="text-base sm:text-lg font-extrabold text-slate-800">
                            {{ latestActivity.caption }}
                        </h4>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            {{ latestActivity.timestamp_label }}
                        </p>
                        <span class="mt-3 inline-flex items-center gap-1 text-xs font-bold text-[#2E6FBE]">
                            Buka Detail Laporan &amp; Foto →
                        </span>
                    </div>
                </Link>

                <!-- Empty State (R-27) -->
                <div
                    v-else
                    class="bg-white rounded-2xl border border-slate-200/80 p-6 text-center"
                >
                    <p class="text-sm font-bold text-slate-700">Belum ada foto kegiatan untuk {{ activeChild?.name }}</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Foto kegiatan kelas {{ activeChild?.group_name }} akan muncul di sini setelah guru mengunggah laporan.
                    </p>
                    <Link
                        href="/laporan-harian"
                        class="inline-block mt-3 px-4 py-2 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold"
                    >
                        Buka Laporan Harian
                    </Link>
                </div>
            </section>
        </div>

        <!-- Modal Pendaftaran Anak Baru oleh Orang Tua / Admin -->
        <div
            v-if="showEnrollChildModal"
            @click.self="showEnrollChildModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="enroll-modal-title"
        >
            <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-xl my-8 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="enroll-modal-title" class="text-base font-bold text-slate-800">
                        Daftarkan Anak Baru
                    </h2>
                    <button
                        type="button"
                        @click="showEnrollChildModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer"
                        aria-label="Tutup"
                    >
                        ✕
                    </button>
                </div>

                <div class="p-3 rounded-xl bg-[#EBF3FE] border border-[#CFE2FC] text-slate-700 leading-relaxed">
                    <p class="font-bold text-[#2E6FBE]">Informasi Pendaftaran Kelas</p>
                    <p class="mt-0.5 text-[11px]">
                        Setiap pendaftaran kelas akan melalui <strong>persetujuan Admin</strong> sesuai ketersediaan kuota masing-masing kelompok belajar.
                    </p>
                </div>

                <form @submit.prevent="submitEnrollChild" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Lengkap / Panggilan Anak</label>
                        <input
                            v-model="enrollForm.name"
                            type="text"
                            required
                            placeholder="Contoh: Nayla Putri"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm"
                        />
                        <p v-if="enrollForm.errors.name" class="text-rose-600 mt-1">{{ enrollForm.errors.name }}</p>
                    </div>

                    <!-- Jenis Kelamin Anak -->
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Jenis Kelamin Anak</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label
                                :class="[
                                    'flex items-center justify-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer transition text-xs font-bold',
                                    enrollForm.gender === 'laki-laki'
                                        ? 'border-[#0077C2] bg-[#F0F7FF] text-[#005E9C]'
                                        : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                                ]"
                            >
                                <input
                                    type="radio"
                                    value="laki-laki"
                                    v-model="enrollForm.gender"
                                    class="sr-only"
                                />
                                <span>👦 Laki-laki</span>
                            </label>
                            <label
                                :class="[
                                    'flex items-center justify-center gap-2 px-3 py-2 rounded-xl border-2 cursor-pointer transition text-xs font-bold',
                                    enrollForm.gender === 'perempuan'
                                        ? 'border-[#EC4899] bg-[#FDF2F8] text-[#BE185D]'
                                        : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                                ]"
                            >
                                <input
                                    type="radio"
                                    value="perempuan"
                                    v-model="enrollForm.gender"
                                    class="sr-only"
                                />
                                <span>👧 Perempuan</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Tanggal Lahir Anak</label>
                        <input
                            v-model="enrollForm.birth_date"
                            type="date"
                            required
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs"
                        />
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nomor HP / WhatsApp (Orang Tua / Wali)</label>
                        <input
                            v-model="enrollForm.phone"
                            type="tel"
                            placeholder="Contoh: 081234567890"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs"
                        />
                        <p v-if="enrollForm.errors.phone" class="text-rose-600 mt-1">{{ enrollForm.errors.phone }}</p>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Alamat Rumah Tempat Tinggal</label>
                        <textarea
                            v-model="enrollForm.address"
                            rows="2"
                            placeholder="Contoh: Jl. Melati Indah No. 12, RT 02 / RW 05"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs resize-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Pilih Kelas yang Diinginkan</label>
                        <select
                            v-model="enrollForm.class_group_id"
                            required
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-medium"
                        >
                            <option v-for="cg in classGroups" :key="cg.id" :value="cg.id">
                                {{ cg.name }} (Sisa kuota: {{ cg.remaining_quota }}/{{ cg.quota }} kursi)
                            </option>
                        </select>
                        <p v-if="enrollForm.errors.class_group_id" class="text-rose-600 mt-1">{{ enrollForm.errors.class_group_id }}</p>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Foto Profil Anak (Opsional)</label>
                        <div class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-slate-50/60">
                            <div class="w-10 h-10 rounded-full overflow-hidden bg-white border border-slate-200 shrink-0 flex items-center justify-center">
                                <img
                                    v-if="enrollPhotoPreview"
                                    :src="enrollPhotoPreview"
                                    alt="Pratinjau foto anak"
                                    class="w-full h-full object-cover"
                                />
                                <span v-else class="text-base">
                                    {{ enrollForm.gender === 'laki-laki' ? '👦' : '👧' }}
                                </span>
                            </div>
                            <input
                                type="file"
                                accept="image/jpeg,image/png,image/webp,image/svg+xml"
                                @change="handleEnrollPhoto"
                                class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-[#EBF3FE] file:text-[#0077C2] file:font-bold file:text-xs cursor-pointer"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="showEnrollChildModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 font-semibold text-slate-600 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="enrollForm.processing"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] text-white font-bold cursor-pointer"
                        >
                            {{ enrollForm.processing ? 'Mengirim...' : 'Ajukan Pendaftaran' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Pengajuan Izin / Sakit oleh Orang Tua -->
        <div
            v-if="showLeaveModal"
            @click.self="showLeaveModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="home-leave-modal-title"
        >
            <div class="bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-xl space-y-4 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 id="home-leave-modal-title" class="text-base font-extrabold text-slate-800">
                            Pengajuan Izin / Sakit ({{ activeChild?.name }})
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Keterangan izin atau sakit otomatis tercatat pada statistik Laporan Harian &amp; Bulanan.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="showLeaveModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer"
                        aria-label="Tutup"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitLeaveRequest" class="space-y-3.5">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Tanggal Tidak Hadir</label>
                        <input
                            v-model="leaveForm.report_date"
                            type="date"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm"
                        />
                        <p v-if="leaveForm.errors.report_date" class="text-rose-600 mt-1">
                            {{ leaveForm.errors.report_date }}
                        </p>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1.5">Jenis Keterangan</label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <button
                                type="button"
                                @click="leaveForm.attendance_status = 'izin'"
                                :class="[
                                    'py-2.5 px-3 rounded-xl border text-xs font-extrabold transition flex items-center justify-center gap-1.5 cursor-pointer',
                                    leaveForm.attendance_status === 'izin'
                                        ? 'bg-[#FEF6E0] border-amber-400 text-[#B7791F] ring-2 ring-amber-200'
                                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                                ]"
                            >
                                <span>Izin</span>
                            </button>
                            <button
                                type="button"
                                @click="leaveForm.attendance_status = 'sakit'"
                                :class="[
                                    'py-2.5 px-3 rounded-xl border text-xs font-extrabold transition flex items-center justify-center gap-1.5 cursor-pointer',
                                    leaveForm.attendance_status === 'sakit'
                                        ? 'bg-[#FDECEC] border-rose-400 text-[#C9403C] ring-2 ring-rose-200'
                                        : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50',
                                ]"
                            >
                                <span>Sakit</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="font-bold text-slate-700 block mb-1">
                            Alasan / Keterangan {{ leaveForm.attendance_status === 'sakit' ? 'Sakit' : 'Izin' }}
                        </label>
                        <textarea
                            v-model="leaveForm.absence_reason"
                            rows="3"
                            required
                            :placeholder="
                                leaveForm.attendance_status === 'sakit'
                                    ? 'Contoh: Demam ringan dan sedang beristirahat di rumah'
                                    : 'Contoh: Mengikuti acara keluarga di luar kota'
                            "
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm"
                        ></textarea>
                        <p v-if="leaveForm.errors.absence_reason" class="text-rose-600 mt-1">
                            {{ leaveForm.errors.absence_reason }}
                        </p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="showLeaveModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 font-semibold text-slate-600 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="leaveForm.processing"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] text-white font-extrabold hover:bg-[#255A9C] disabled:opacity-50 cursor-pointer"
                        >
                            {{ leaveForm.processing ? 'Menyimpan...' : 'Kirim Pengajuan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>
