<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    selectedDate: {
        type: String,
        required: true,
    },
    monthHeader: {
        type: String,
        required: true,
    },
    prevWeekDate: {
        type: String,
        required: true,
    },
    nextWeekDate: {
        type: String,
        required: true,
    },
    weekDays: {
        type: Array,
        default: () => [],
    },
    report: {
        type: Object,
        default: null,
    },
    attendanceStats: {
        type: Object,
        default: () => ({
            month_year: '2026-10',
            month_label: 'Oktober 2026',
            total_hadir: 0,
            total_izin: 0,
            total_sakit: 0,
            total_days: 0,
        }),
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const activeChild = computed(() => page.props.auth?.activeChild);

const showEditorModal = ref(false);
const showLeaveModal = ref(false);

const form = useForm({
    child_id: activeChild.value?.id || '',
    report_date: props.selectedDate,
    attendance_status: props.report?.attendance_status || 'hadir',
    absence_reason: props.report?.absence_reason || '',
    time_range: props.report?.time_range || '10.00 - 11.00',
    materials: props.report?.materials?.length
        ? [...props.report.materials]
        : ['Mengenal huruf B', 'Menulis suku kata ba, bi, bu'],
    activities: props.report?.activities?.length
        ? [...props.report.activities]
        : ['Membaca suku kata', 'Menulis huruf rapi', 'Berhitung angka', 'Mengaji Iqra / Hijaiyah'],
    behavior_fokus: props.report?.behavior?.Fokus || 'Baik',
    behavior_antusias: props.report?.behavior?.Antusias || 'Baik',
    behavior_kemandirian: props.report?.behavior?.Kemandirian || 'Mulai berkembang',
    teacher_notes:
        props.report?.teacher_notes ||
        'Aisyah sudah mulai dapat menulis huruf B dengan bantuan. Masih perlu latihan konsistensi bentuk huruf.',
});

const leaveForm = useForm({
    child_id: activeChild.value?.id || '',
    report_date: props.selectedDate,
    attendance_status:
        props.report?.attendance_status === 'sakit' ? 'sakit' : 'izin',
    absence_reason: props.report?.absence_reason || '',
});

function pickDate(dateStr) {
    router.get('/laporan-harian', { date: dateStr }, { preserveState: false });
}

function openEditor() {
    form.child_id = activeChild.value?.id || '';
    form.report_date = props.selectedDate;
    form.attendance_status = props.report?.attendance_status || 'hadir';
    form.absence_reason = props.report?.absence_reason || '';
    form.time_range = props.report?.time_range || '10.00 - 11.00';
    form.materials = props.report?.materials?.length
        ? [...props.report.materials]
        : ['Mengenal huruf B', 'Menulis suku kata ba, bi, bu'];
    form.activities = props.report?.activities?.length
        ? [...props.report.activities]
        : ['Membaca suku kata', 'Menulis huruf rapi', 'Berhitung angka', 'Mengaji Iqra / Hijaiyah'];
    form.behavior_fokus = props.report?.behavior?.Fokus || 'Baik';
    form.behavior_antusias = props.report?.behavior?.Antusias || 'Baik';
    form.behavior_kemandirian = props.report?.behavior?.Kemandirian || 'Mulai berkembang';
    form.teacher_notes =
        props.report?.teacher_notes ||
        `${activeChild.value?.name || 'Anak'} mengikuti kegiatan belajar dengan baik.`;
    showEditorModal.value = true;
}

function openLeaveModal(defaultStatus = 'izin') {
    leaveForm.child_id = activeChild.value?.id || '';
    leaveForm.report_date = props.selectedDate;
    leaveForm.attendance_status =
        props.report?.attendance_status === 'sakit' || props.report?.attendance_status === 'izin'
            ? props.report.attendance_status
            : defaultStatus;
    leaveForm.absence_reason = props.report?.absence_reason || '';
    showLeaveModal.value = true;
}

function submitReport() {
    form.post('/laporan-harian', {
        onSuccess: () => {
            showEditorModal.value = false;
        },
    });
}

function submitLeaveRequest() {
    leaveForm.child_id = activeChild.value?.id || '';
    leaveForm.post('/laporan-harian/izin', {
        onSuccess: () => {
            showLeaveModal.value = false;
        },
    });
}
</script>

<template>
    <Head title="Laporan Harian" />

    <MobileAppLayout title="Laporan Harian" :show-back="true" back-url="/" active-tab="beranda">
        <template #headerRight>
            <div class="flex items-center gap-1.5">
                <button
                    type="button"
                    @click="openLeaveModal('izin')"
                    class="px-2.5 py-1.5 rounded-lg bg-[#FEF6E0] border border-[#F9E4B7] text-[#B7791F] text-xs font-extrabold hover:brightness-95 transition cursor-pointer"
                >
                    + Izin / Sakit
                </button>
                <button
                    v-if="user?.can_manage_reports"
                    type="button"
                    @click="openEditor"
                    class="px-2.5 py-1.5 rounded-lg bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 transition cursor-pointer"
                >
                    {{ report ? 'Edit' : '+ Input' }}
                </button>
            </div>
        </template>

        <div class="py-4 space-y-4">
            <!-- Statistik Kehadiran Bulan Ini (Hadir, Izin, Sakit) -->
            <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Statistik Kehadiran ({{ attendanceStats.month_label }})
                        </p>
                        <h2 class="text-sm font-extrabold text-slate-800 mt-0.5">
                            Rekap Kehadiran {{ activeChild?.name }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        @click="openLeaveModal('izin')"
                        class="px-3 py-1.5 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-extrabold hover:bg-blue-100 transition cursor-pointer"
                    >
                        + Ajukan Izin / Sakit
                    </button>
                </div>

                <div class="grid grid-cols-3 gap-2.5">
                    <!-- Total Hadir -->
                    <div class="p-3 rounded-2xl bg-[#EBF8F0] border border-[#CBEAD7] text-center">
                        <p class="text-[11px] font-bold text-[#1E8E5A]">Hadir</p>
                        <p class="text-lg font-extrabold text-[#1E8E5A] mt-0.5">
                            {{ attendanceStats.total_hadir }}
                            <span class="text-[11px] font-semibold">Hari</span>
                        </p>
                    </div>
                    <!-- Total Izin -->
                    <div class="p-3 rounded-2xl bg-[#FEF6E0] border border-[#F9E4B7] text-center">
                        <p class="text-[11px] font-bold text-[#B7791F]">Izin</p>
                        <p class="text-lg font-extrabold text-[#B7791F] mt-0.5">
                            {{ attendanceStats.total_izin }}
                            <span class="text-[11px] font-semibold">Hari</span>
                        </p>
                    </div>
                    <!-- Total Sakit -->
                    <div class="p-3 rounded-2xl bg-[#FDECEC] border border-[#F9D5D5] text-center">
                        <p class="text-[11px] font-bold text-[#C9403C]">Sakit</p>
                        <p class="text-lg font-extrabold text-[#C9403C] mt-0.5">
                            {{ attendanceStats.total_sakit }}
                            <span class="text-[11px] font-semibold">Hari</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Date Picker / Carousel Header (Matches 3. Laporan Harian Reference: < Okt 2026 > + Sen 12 .. Jum 16) -->
            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/70 shadow-2xs">
                <div class="flex items-center justify-between mb-3 px-1">
                    <button
                        type="button"
                        @click="pickDate(prevWeekDate)"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-600 hover:bg-slate-100"
                        aria-label="Minggu sebelumnya"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>

                    <span class="text-sm font-extrabold text-slate-800">
                        {{ monthHeader }}
                    </span>

                    <button
                        type="button"
                        @click="pickDate(nextWeekDate)"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-600 hover:bg-slate-100"
                        aria-label="Minggu berikutnya"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <!-- 5-Day Horizontal Selector (Sen 12, Sel 13, Rab 14, Kam 15, Jum 16) -->
                <div class="grid grid-cols-5 gap-1.5">
                    <button
                        v-for="day in weekDays"
                        :key="day.date"
                        type="button"
                        @click="pickDate(day.date)"
                        :class="[
                            'flex flex-col items-center justify-center py-2 rounded-2xl transition min-h-[62px] relative',
                            day.is_selected
                                ? 'bg-[#3B82F6] text-white font-bold shadow-xs'
                                : 'bg-transparent text-slate-700 hover:bg-slate-100',
                        ]"
                    >
                        <span :class="['text-[11px]', day.is_selected ? 'text-blue-50 font-semibold' : 'text-slate-500']">
                            {{ day.day_short }}
                        </span>
                        <span class="text-[15px] font-extrabold mt-0.5">
                            {{ day.day_number }}
                        </span>
                        <span
                            v-if="day.attendance_status"
                            :class="[
                                'mt-1 px-1.5 py-0.2 rounded-full text-[9px] font-extrabold uppercase',
                                day.is_selected
                                    ? 'bg-white/25 text-white'
                                    : day.attendance_status === 'sakit'
                                      ? 'bg-rose-100 text-rose-700'
                                      : day.attendance_status === 'izin'
                                        ? 'bg-amber-100 text-amber-800'
                                        : 'bg-emerald-100 text-emerald-700',
                            ]"
                        >
                            {{ day.attendance_status }}
                        </span>
                    </button>
                </div>
            </div>

            <!-- Ringkasan Hari Ini Section -->
            <div v-if="report" class="space-y-3">
                <div class="flex flex-wrap items-center justify-between gap-2 px-0.5">
                    <div class="flex items-center gap-2">
                        <h2 class="text-[15px] font-extrabold text-slate-800">
                            Ringkasan Hari Ini
                        </h2>
                        <span
                            :class="[
                                'px-2.5 py-0.5 rounded-full text-[11px] font-extrabold',
                                report.attendance_status === 'sakit'
                                    ? 'bg-[#FDECEC] text-[#C9403C] border border-[#F9D5D5]'
                                    : report.attendance_status === 'izin'
                                      ? 'bg-[#FEF6E0] text-[#B7791F] border border-[#F9E4B7]'
                                      : 'bg-[#EBF8F0] text-[#1E8E5A] border border-[#CBEAD7]',
                            ]"
                        >
                            Status: {{ report.attendance_label || 'Hadir' }}
                        </span>
                    </div>
                    <Link
                        :href="`/laporan-harian/${report.id}`"
                        class="text-xs font-bold text-[#2E6FBE] hover:underline"
                    >
                        Detail &amp; Foto ({{ report.photos?.length || 0 }}) →
                    </Link>
                </div>

                <!-- Banner Keterangan Izin / Sakit jika siswa tidak hadir -->
                <div
                    v-if="report.attendance_status === 'izin' || report.attendance_status === 'sakit'"
                    :class="[
                        'p-4 rounded-2xl border flex items-start justify-between gap-3',
                        report.attendance_status === 'sakit'
                            ? 'bg-[#FDECEC] border-[#F9D5D5] text-[#C9403C]'
                            : 'bg-[#FEF6E0] border-[#F9E4B7] text-[#B7791F]',
                    ]"
                >
                    <div class="space-y-1">
                        <p class="text-xs font-extrabold uppercase tracking-wider">
                            Keterangan {{ report.attendance_label }} ({{ report.formatted_date }})
                        </p>
                        <p class="text-sm font-bold text-slate-800">
                            Alasan: {{ report.absence_reason || 'Tidak mengikuti kegiatan kelas' }}
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="openLeaveModal(report.attendance_status)"
                        class="px-3 py-1.5 rounded-xl bg-white/90 text-xs font-bold text-slate-700 hover:bg-white shrink-0"
                    >
                        Ubah
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- 1. Materi Card (Soft Rose Tint #FDECEC) -->
                    <div class="p-4 rounded-2xl bg-[#FDECEC] border border-[#F9D5D5] flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/80 flex items-center justify-center shrink-0 text-[#D9534F] mt-0.5">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-extrabold text-[#C9403C]">Materi</h3>
                            <div class="mt-1 space-y-0.5 text-[13.5px] text-slate-800 font-medium leading-snug">
                                <p v-for="(item, idx) in report.materials" :key="idx">
                                    {{ item }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Aktivitas Card (Soft Mint Green #EBF8F0) -->
                    <div class="p-4 rounded-2xl bg-[#EBF8F0] border border-[#CBEAD7] flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/80 flex items-center justify-center shrink-0 text-[#1E8E5A] mt-0.5">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-extrabold text-[#1E8E5A]">Aktivitas</h3>
                            <ul class="mt-1 space-y-0.5 text-[13.5px] text-slate-800 font-medium leading-snug list-disc list-inside">
                                <li v-for="(act, idx) in report.activities.slice(0, 3)" :key="idx">
                                    {{ act }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 3. Perilaku Card (Soft Warm Amber #FEF6E0) -->
                    <div class="p-4 rounded-2xl bg-[#FEF6E0] border border-[#F9E4B7] flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/80 flex items-center justify-center shrink-0 text-[#D97706] mt-0.5">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="10" fill="#F59E0B" />
                                <circle cx="9" cy="10" r="1.4" fill="#FFF" />
                                <circle cx="15" cy="10" r="1.4" fill="#FFF" />
                                <path d="M8.5 14.5c1 1.5 2.2 2 3.5 2s2.5-.5 3.5-2" stroke="#FFF" stroke-width="1.8" fill="none" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-extrabold text-[#B7791F]">Perilaku</h3>
                            <ul class="mt-1 space-y-0.5 text-[13.5px] text-slate-800 font-medium leading-snug list-disc list-inside">
                                <li v-for="(val, key) in report.behavior" :key="key">
                                    {{ key }}: {{ val }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- 4. Catatan Guru Card (Soft Sky Blue #EBF3FE) -->
                    <div class="p-4 rounded-2xl bg-[#EBF3FE] border border-[#CFE2FC] flex items-start gap-3">
                        <div class="w-9 h-9 rounded-xl bg-white/80 flex items-center justify-center shrink-0 text-[#2E6FBE] mt-0.5">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <rect x="4" y="3" width="16" height="18" rx="2.5" fill="#2E6FBE" />
                                <path d="M8 8h8M8 12h8M8 16h5" stroke="#FFF" stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-extrabold text-[#2563EB]">Catatan Guru</h3>
                            <p class="mt-1 text-[13.5px] text-slate-700 leading-relaxed">
                                {{ report.teacher_notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- CTA Button to View Screen 4 (Detail Kegiatan + Foto) -->
                <Link
                    :href="`/laporan-harian/${report.id}`"
                    class="w-full py-3.5 px-4 rounded-2xl bg-white border border-slate-200 shadow-2xs flex items-center justify-between text-sm font-bold text-[#2E6FBE] hover:bg-blue-50/40 transition"
                >
                    <span>Lihat Detail Kegiatan &amp; Galeri Foto ({{ report.photos?.length || 0 }})</span>
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>

            <!-- Empty State when no report on selected date -->
            <div v-else class="bg-white rounded-2xl border border-slate-200/80 p-6 text-center space-y-2.5">
                <p class="text-sm font-bold text-slate-800">
                    Belum ada laporan pada tanggal {{ selectedDate }}
                </p>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Pilih tanggal lain pada kalender mingguan, ajukan keterangan izin/sakit jika anak berhalangan hadir, atau tambahkan laporan baru jika Anda Guru/Admin.
                </p>
                <div class="pt-2 flex flex-wrap items-center justify-center gap-2">
                    <button
                        type="button"
                        @click="pickDate('2026-10-14')"
                        class="px-4 py-2 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold"
                    >
                        Lihat Rabu, 14 Okt 2026
                    </button>
                    <button
                        type="button"
                        @click="openLeaveModal('izin')"
                        class="px-4 py-2 rounded-xl bg-[#FEF6E0] border border-[#F9E4B7] text-[#B7791F] text-xs font-extrabold"
                    >
                        + Ajukan Izin / Sakit Tanggal Ini
                    </button>
                    <button
                        v-if="user?.can_manage_reports"
                        type="button"
                        @click="openEditor"
                        class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white text-xs font-bold"
                    >
                        + Buat Laporan Tanggal Ini
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Pengajuan Izin / Sakit (Orang Tua, Guru, Admin) -->
        <div
            v-if="showLeaveModal"
            @click.self="showLeaveModal = false"
            @keydown.esc="showLeaveModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="leave-modal-title"
        >
            <div class="bg-white rounded-3xl max-w-md w-full p-5 sm:p-6 shadow-xl space-y-4 text-xs">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h2 id="leave-modal-title" class="text-base font-extrabold text-slate-800">
                            Pengajuan Izin / Sakit ({{ activeChild?.name }})
                        </h2>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Keterangan izin atau sakit otomatis tercatat pada statistik Laporan Harian &amp; Bulanan.
                        </p>
                    </div>
                    <button
                        type="button"
                        @click="showLeaveModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"
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
                            class="px-4 py-2.5 rounded-xl border border-slate-200 font-semibold text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="leaveForm.processing"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] text-white font-extrabold hover:bg-[#255A9C] disabled:opacity-50"
                        >
                            {{ leaveForm.processing ? 'Menyimpan...' : 'Kirim Pengajuan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Teacher / Admin Create & Edit Daily Report Modal -->
        <div
            v-if="showEditorModal"
            @click.self="showEditorModal = false"
            @keydown.esc="showEditorModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="daily-editor-title"
        >
            <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-xl my-8 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="daily-editor-title" class="text-base font-bold text-slate-800">
                        Input Laporan Harian ({{ activeChild?.name }})
                    </h2>
                    <button
                        type="button"
                        @click="showEditorModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"
                        aria-label="Tutup"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitReport" class="space-y-3">
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Tanggal</label>
                            <input v-model="form.report_date" type="date" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Status Hadir</label>
                            <select v-model="form.attendance_status" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 bg-white font-bold">
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Jam Sesi</label>
                            <input v-model="form.time_range" type="text" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200" />
                        </div>
                    </div>

                    <div v-if="form.attendance_status !== 'hadir'">
                        <label class="font-semibold text-slate-700 block mb-1">Alasan Izin / Sakit</label>
                        <input
                            v-model="form.absence_reason"
                            type="text"
                            placeholder="Tuliskan alasan izin atau sakit..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-200"
                        />
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Materi (Baris 1 &amp; 2)</label>
                        <input v-model="form.materials[0]" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200 mb-1.5" placeholder="Materi 1" />
                        <input v-model="form.materials[1]" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-200" placeholder="Materi 2 (opsional)" />
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Aktivitas Kelas (Membaca, Menulis, Berhitung, Mengaji)</label>
                        <input v-model="form.activities[0]" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200 mb-1.5" placeholder="Aktivitas Membaca" />
                        <input v-model="form.activities[1]" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-200 mb-1.5" placeholder="Aktivitas Menulis" />
                        <input v-model="form.activities[2]" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-200 mb-1.5" placeholder="Aktivitas Berhitung" />
                        <input v-model="form.activities[3]" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-200" placeholder="Aktivitas Mengaji" />
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Fokus</label>
                            <select v-model="form.behavior_fokus" class="w-full px-2 py-2 rounded-xl border border-slate-200 bg-white">
                                <option>Sangat baik</option>
                                <option>Baik</option>
                                <option>Mulai berkembang</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Antusias</label>
                            <select v-model="form.behavior_antusias" class="w-full px-2 py-2 rounded-xl border border-slate-200 bg-white">
                                <option>Sangat baik</option>
                                <option>Baik</option>
                                <option>Mulai berkembang</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Kemandirian</label>
                            <select v-model="form.behavior_kemandirian" class="w-full px-2 py-2 rounded-xl border border-slate-200 bg-white">
                                <option>Sangat baik</option>
                                <option>Baik</option>
                                <option>Mulai berkembang</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Guru</label>
                        <textarea
                            v-model="form.teacher_notes"
                            rows="3"
                            required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="showEditorModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Simpan Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>
