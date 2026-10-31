<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    filters: {
        type: Array,
        default: () => ['Semua', 'Laporan', 'Pembayaran', 'Info'],
    },
    selectedFilter: {
        type: String,
        default: 'Semua',
    },
    notifications: {
        type: Array,
        default: () => [],
    },
    recipientUsers: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const isAdmin = computed(() => Boolean(user.value?.is_admin));

const selectedNotification = ref(null);
const showSendModal = ref(false);

const sendForm = useForm({
    target_audience: 'all',
    target_user_id: '',
    type: 'info',
    title: '',
    message: '',
});

function selectFilter(filter) {
    router.get('/notifikasi', { filter }, { preserveState: true, preserveScroll: true });
}

function openNotificationDetail(item) {
    selectedNotification.value = {
        ...item,
        is_read: true,
    };

    if (!item.is_read) {
        router.post(
            `/notifikasi/${item.id}/read`,
            {},
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    }
}

function closeNotificationDetail() {
    selectedNotification.value = null;
}

function goToActionUrl(item) {
    if (!item?.action_url) return;
    selectedNotification.value = null;
    router.visit(item.action_url);
}

function markAllRead() {
    router.post('/notifikasi/read-all', {}, { preserveScroll: true });
}

function submitSendNotification() {
    sendForm.post('/notifikasi', {
        preserveScroll: true,
        onSuccess: () => {
            showSendModal.value = false;
            sendForm.reset('title', 'message', 'target_user_id');
        },
    });
}

function handleKeydown(event) {
    if (event.key === 'Escape') {
        selectedNotification.value = null;
        showSendModal.value = false;
    }
}

onMounted(() => window.addEventListener('keydown', handleKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', handleKeydown));

function iconStyle(item) {
    const title = (item?.title || '').toLowerCase();
    if (title.includes('foto')) {
        return { bg: 'bg-[#EBF3FE]', text: 'text-[#2E6FBE]', badgeBg: 'bg-[#EBF3FE] text-[#2E6FBE]', kind: 'photo' };
    }
    if (title.includes('bulanan')) {
        return { bg: 'bg-[#FDECEC]', text: 'text-[#D9534F]', badgeBg: 'bg-[#FDECEC] text-[#D9534F]', kind: 'chart' };
    }
    if (item?.type === 'pembayaran') {
        return { bg: 'bg-[#EBF8F0]', text: 'text-[#1E8E5A]', badgeBg: 'bg-[#EBF8F0] text-[#1E8E5A]', kind: 'payment' };
    }
    if (item?.type === 'info') {
        return { bg: 'bg-[#FEF6E0]', text: 'text-[#D97706]', badgeBg: 'bg-[#FEF6E0] text-[#B45309]', kind: 'megaphone' };
    }
    return { bg: 'bg-[#FDECEC]', text: 'text-[#D9534F]', badgeBg: 'bg-[#FDECEC] text-[#D9534F]', kind: 'report' };
}
</script>

<template>
    <Head title="Notifikasi" />

    <MobileAppLayout title="Notifikasi" :show-back="true" back-url="/" active-tab="notifikasi">
        <template #headerRight>
            <div class="flex items-center gap-2">
                <button
                    v-if="notifications.some((n) => !n.is_read)"
                    type="button"
                    @click="markAllRead"
                    class="text-[11.5px] font-bold text-[#2E6FBE] hover:underline px-2 py-1 rounded-lg hover:bg-blue-50/70 transition"
                >
                    Baca Semua
                </button>
                <button
                    v-if="isAdmin"
                    type="button"
                    @click="showSendModal = true"
                    class="px-3 py-1.5 rounded-xl bg-[#2E6FBE] text-white text-xs font-extrabold hover:bg-[#255A9C] transition shadow-2xs"
                >
                    + Kirim Notifikasi
                </button>
            </div>
        </template>

        <div class="py-4 space-y-5">
            <!-- Admin Action Banner + Category Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <!-- Category Filters: [Semua], [Laporan], [Pembayaran], [Info] -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                    <button
                        v-for="filter in filters"
                        :key="filter"
                        type="button"
                        @click="selectFilter(filter)"
                        :class="[
                            'px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition shrink-0 min-h-[38px]',
                            selectedFilter === filter
                                ? 'bg-[#3B82F6] text-white shadow-2xs'
                                : 'bg-white border border-slate-200/80 text-slate-600 hover:bg-slate-100',
                        ]"
                    >
                        {{ filter }}
                    </button>
                </div>

                <div v-if="isAdmin" class="flex items-center justify-between sm:justify-end gap-2">
                    <button
                        type="button"
                        @click="showSendModal = true"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] border border-blue-200 text-xs font-extrabold hover:bg-blue-100/70 transition flex items-center justify-center gap-1.5"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat &amp; Kirim Notifikasi Baru
                    </button>
                </div>
            </div>

            <!-- Notifications List (Responsive Grid on Desktop) -->
            <div v-if="notifications.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <button
                    v-for="item in notifications"
                    :key="item.id"
                    type="button"
                    @click="openNotificationDetail(item)"
                    :class="[
                        'w-full p-4 rounded-2xl border text-left flex items-start justify-between gap-3.5 transition group',
                        !item.is_read
                            ? 'bg-white border-blue-300 shadow-xs ring-1 ring-blue-100'
                            : 'bg-white border-slate-200/80 hover:border-slate-300',
                    ]"
                >
                    <div class="flex items-start gap-3.5 min-w-0 flex-1">
                        <div
                            :class="[
                                'w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 mt-0.5',
                                iconStyle(item).bg,
                                iconStyle(item).text,
                            ]"
                        >
                            <!-- Report Icon -->
                            <svg v-if="iconStyle(item).kind === 'report'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="5" y="3" width="14" height="18" rx="2.5" />
                                <path d="M8 8h8M8 12h8M8 16h5" stroke="#FFF" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                            <!-- Photo Icon -->
                            <svg v-else-if="iconStyle(item).kind === 'photo'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="3" y="4" width="18" height="16" rx="3" />
                                <circle cx="8.5" cy="9.5" r="1.5" fill="#FFF" />
                                <path d="M5 18l4.5-5 3.5 3.5 3.5-4.5L19 18H5z" fill="#BFDBFE" />
                            </svg>
                            <!-- Payment Icon -->
                            <svg v-else-if="iconStyle(item).kind === 'payment'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="3" y="5" width="18" height="14" rx="3" />
                                <rect x="14" y="10" width="5" height="4" rx="1" fill="#FFF" />
                            </svg>
                            <!-- Chart Icon -->
                            <svg v-else-if="iconStyle(item).kind === 'chart'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="4" y="13" width="4" height="7" rx="1" />
                                <rect x="10" y="9" width="4" height="11" rx="1" />
                                <rect x="16" y="5" width="4" height="15" rx="1" />
                            </svg>
                            <!-- Megaphone Info Icon -->
                            <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 10v4a1 1 0 001 1h2l4 4V5L6 9H4a1 1 0 00-1 1zm13-3.5v11a5.5 5.5 0 000-11z" />
                            </svg>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wide',
                                        iconStyle(item).badgeBg,
                                    ]"
                                >
                                    {{ item.type_label || item.type }}
                                </span>
                                <span
                                    v-if="item.child_name"
                                    class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600"
                                >
                                    {{ item.child_name }}
                                </span>
                                <span
                                    v-if="!item.is_read"
                                    class="w-2 h-2 rounded-full bg-[#3B82F6] shrink-0"
                                    title="Belum dibaca"
                                />
                            </div>

                            <h2 class="text-sm font-extrabold text-slate-800 leading-snug mt-1.5 group-hover:text-[#2E6FBE] transition">
                                {{ item.title }}
                            </h2>

                            <p v-if="item.message" class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">
                                {{ item.message }}
                            </p>

                            <div class="flex items-center justify-between gap-2 mt-2">
                                <p class="text-[11px] font-medium text-slate-400">
                                    {{ item.timestamp_label }}
                                </p>
                                <span class="text-[11px] font-bold text-[#2E6FBE] group-hover:underline">
                                    Lihat Detail &rarr;
                                </span>
                            </div>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-2xl border border-slate-200/80 p-8 text-center space-y-2">
                <p class="text-sm font-bold text-slate-800">
                    Tidak ada notifikasi pada kategori "{{ selectedFilter }}"
                </p>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Pemberitahuan laporan harian, perkembangan bulanan, pembayaran, dan pengumuman sekolah akan tampil di halaman ini.
                </p>
                <button
                    type="button"
                    @click="selectFilter('Semua')"
                    class="mt-2 px-4 py-2 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold"
                >
                    Lihat Semua Notifikasi
                </button>
            </div>
        </div>

        <!-- MODAL DETAIL NOTIFIKASI (Orang Tua, Guru, & Admin) -->
        <div
            v-if="selectedNotification"
            @click.self="closeNotificationDetail"
            class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4"
        >
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="detail-notifikasi-title"
                class="w-full max-w-lg bg-white rounded-t-3xl sm:rounded-3xl p-5 sm:p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-3.5">
                    <div class="flex items-center gap-3">
                        <div
                            :class="[
                                'w-11 h-11 rounded-2xl flex items-center justify-center shrink-0',
                                iconStyle(selectedNotification).bg,
                                iconStyle(selectedNotification).text,
                            ]"
                        >
                            <svg v-if="iconStyle(selectedNotification).kind === 'report'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="5" y="3" width="14" height="18" rx="2.5" />
                                <path d="M8 8h8M8 12h8M8 16h5" stroke="#FFF" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                            <svg v-else-if="iconStyle(selectedNotification).kind === 'photo'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="3" y="4" width="18" height="16" rx="3" />
                                <circle cx="8.5" cy="9.5" r="1.5" fill="#FFF" />
                                <path d="M5 18l4.5-5 3.5 3.5 3.5-4.5L19 18H5z" fill="#BFDBFE" />
                            </svg>
                            <svg v-else-if="iconStyle(selectedNotification).kind === 'payment'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="3" y="5" width="18" height="14" rx="3" />
                                <rect x="14" y="10" width="5" height="4" rx="1" fill="#FFF" />
                            </svg>
                            <svg v-else-if="iconStyle(selectedNotification).kind === 'chart'" class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="4" y="13" width="4" height="7" rx="1" />
                                <rect x="10" y="9" width="4" height="11" rx="1" />
                                <rect x="16" y="5" width="4" height="15" rx="1" />
                            </svg>
                            <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 10v4a1 1 0 001 1h2l4 4V5L6 9H4a1 1 0 00-1 1zm13-3.5v11a5.5 5.5 0 000-11z" />
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span
                                    :class="[
                                        'px-2.5 py-0.5 rounded-full text-[10.5px] font-extrabold uppercase tracking-wide',
                                        iconStyle(selectedNotification).badgeBg,
                                    ]"
                                >
                                    {{ selectedNotification.type_label || selectedNotification.type }}
                                </span>
                                <span class="text-xs text-slate-400 font-medium">
                                    {{ selectedNotification.timestamp_label }}
                                </span>
                            </div>
                            <p class="text-[11px] font-bold text-slate-500 mt-0.5">
                                Detail Notifikasi
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="closeNotificationDetail"
                        aria-label="Tutup detail notifikasi"
                        class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 shrink-0"
                    >
                        ✕
                    </button>
                </div>

                <div class="space-y-3">
                    <h3 id="detail-notifikasi-title" class="text-base sm:text-lg font-extrabold text-slate-800 leading-snug">
                        {{ selectedNotification.title }}
                    </h3>

                    <div
                        v-if="selectedNotification.child_name"
                        class="flex items-center gap-2 px-3 py-2 rounded-xl bg-[#F4F8FD] border border-blue-100 text-xs"
                    >
                        <span class="font-bold text-slate-600">Siswa Terkait:</span>
                        <span class="font-extrabold text-[#2E6FBE]">{{ selectedNotification.child_name }}</span>
                        <span v-if="selectedNotification.child_group" class="text-slate-500">
                            ({{ selectedNotification.child_group }})
                        </span>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                            Deskripsi Notifikasi
                        </p>
                        <p class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                            {{ selectedNotification.message || 'Tidak ada deskripsi tambahan untuk notifikasi ini.' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-end gap-2.5 pt-2">
                    <button
                        v-if="selectedNotification.action_url"
                        type="button"
                        @click="goToActionUrl(selectedNotification)"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#2E6FBE] text-white text-xs font-extrabold hover:bg-[#255A9C] transition"
                    >
                        Buka Halaman Terkait
                    </button>
                    <button
                        type="button"
                        @click="closeNotificationDetail"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200 transition"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL ADMIN KIRIM NOTIFIKASI BARU -->
        <div
            v-if="showSendModal && isAdmin"
            @click.self="showSendModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-end sm:items-center justify-center p-0 sm:p-4"
        >
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="kirim-notifikasi-title"
                class="w-full max-w-lg bg-white rounded-t-3xl sm:rounded-3xl p-5 sm:p-6 shadow-xl space-y-4 max-h-[90vh] overflow-y-auto"
            >
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 id="kirim-notifikasi-title" class="text-base font-extrabold text-slate-800">
                            Kirim Notifikasi Baru
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Tambahkan judul dan deskripsi notifikasi untuk Orang Tua, Guru, atau seluruh pengguna.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="showSendModal = false"
                        aria-label="Tutup modal kirim notifikasi"
                        class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitSendNotification" class="space-y-3.5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Target Penerima</label>
                            <select
                                v-model="sendForm.target_audience"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE] focus:outline-hidden"
                            >
                                <option value="all">Semua Pengguna (Orang Tua, Guru, Admin)</option>
                                <option value="parents">Semua Orang Tua</option>
                                <option value="teachers">Semua Guru</option>
                                <option value="specific">Pengguna Tertentu</option>
                            </select>
                            <p v-if="sendForm.errors.target_audience" class="text-xs text-red-600 mt-1">
                                {{ sendForm.errors.target_audience }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Notifikasi</label>
                            <select
                                v-model="sendForm.type"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE] focus:outline-hidden"
                            >
                                <option value="info">Info / Pengumuman</option>
                                <option value="laporan">Laporan Belajar</option>
                                <option value="pembayaran">Pembayaran</option>
                            </select>
                            <p v-if="sendForm.errors.type" class="text-xs text-red-600 mt-1">
                                {{ sendForm.errors.type }}
                            </p>
                        </div>
                    </div>

                    <div v-if="sendForm.target_audience === 'specific'">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Pengguna</label>
                        <select
                            v-model="sendForm.target_user_id"
                            required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE] focus:outline-hidden"
                        >
                            <option value="" disabled>-- Pilih Nama Penerima --</option>
                            <option v-for="recipient in recipientUsers" :key="recipient.id" :value="recipient.id">
                                {{ recipient.name }} ({{ recipient.role === 'parent' ? 'Orang Tua' : recipient.role === 'teacher' ? 'Guru' : 'Admin' }})
                            </option>
                        </select>
                        <p v-if="sendForm.errors.target_user_id" class="text-xs text-red-600 mt-1">
                            {{ sendForm.errors.target_user_id }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Judul Notifikasi</label>
                        <input
                            v-model="sendForm.title"
                            type="text"
                            required
                            placeholder="Contoh: Pengumuman Libur Akhir Pekan / Jadwal Evaluasi"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE] focus:outline-hidden"
                        />
                        <p v-if="sendForm.errors.title" class="text-xs text-red-600 mt-1">
                            {{ sendForm.errors.title }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Notifikasi</label>
                        <textarea
                            v-model="sendForm.message"
                            rows="4"
                            required
                            placeholder="Tuliskan isi detail informasi atau pesan lengkap yang akan dibaca penerima..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE] focus:outline-hidden"
                        />
                        <p v-if="sendForm.errors.message" class="text-xs text-red-600 mt-1">
                            {{ sendForm.errors.message }}
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="showSendModal = false"
                            class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="sendForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-[#2E6FBE] text-white text-xs font-extrabold hover:bg-[#255A9C] disabled:opacity-50"
                        >
                            {{ sendForm.processing ? 'Mengirim...' : 'Kirim Notifikasi' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>
