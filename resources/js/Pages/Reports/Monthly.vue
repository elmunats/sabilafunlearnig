<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    selectedMonth: {
        type: String,
        required: true,
    },
    monthLabel: {
        type: String,
        required: true,
    },
    prevMonth: {
        type: String,
        required: true,
    },
    nextMonth: {
        type: String,
        required: true,
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
            attendance_rate: 0,
            leave_records: [],
        }),
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const activeChild = computed(() => page.props.auth?.activeChild);

const showEditorModal = ref(false);

const form = useForm({
    child_id: activeChild.value?.id || '',
    month_year: props.selectedMonth,
    reading_score: props.report?.reading_score || 78,
    writing_score: props.report?.writing_score || 74,
    counting_score: props.report?.counting_score || 82,
    mengaji_score: props.report?.mengaji_score || 84,
    attitude_score: props.report?.attitude_score || 88,
    note_reading: props.report?.notes?.reading || 'Mulai mengenal huruf vokal dan konsonan.',
    note_writing: props.report?.notes?.writing || 'Menulis huruf dengan lebih rapi dan konsisten.',
    note_counting: props.report?.notes?.counting || 'Mengenal angka 1-10 dan menghitung benda.',
    note_mengaji: props.report?.notes?.mengaji || 'Mengenal dan melafalkan huruf hijaiyah dengan baik.',
    note_attitude: props.report?.notes?.attitude || 'Lebih fokus dan aktif dalam kegiatan.',
});

function changeMonth(ym) {
    router.get('/laporan-bulanan', { month: ym }, { preserveState: false });
}

function openEditor() {
    form.child_id = activeChild.value?.id || '';
    form.month_year = props.selectedMonth;
    form.reading_score = props.report?.reading_score || 78;
    form.writing_score = props.report?.writing_score || 74;
    form.counting_score = props.report?.counting_score || 82;
    form.mengaji_score = props.report?.mengaji_score || 84;
    form.attitude_score = props.report?.attitude_score || 88;
    form.note_reading = props.report?.notes?.reading || 'Mulai mengenal huruf vokal dan konsonan.';
    form.note_writing = props.report?.notes?.writing || 'Menulis huruf dengan lebih rapi dan konsisten.';
    form.note_counting = props.report?.notes?.counting || 'Mengenal angka 1-10 dan menghitung benda.';
    form.note_mengaji = props.report?.notes?.mengaji || 'Mengenal dan melafalkan huruf hijaiyah dengan baik.';
    form.note_attitude = props.report?.notes?.attitude || 'Lebih fokus dan aktif dalam kegiatan.';
    showEditorModal.value = true;
}

function submitMonthly() {
    form.post('/laporan-bulanan', {
        onSuccess: () => {
            showEditorModal.value = false;
        },
    });
}
</script>

<template>
    <Head title="Laporan Bulanan" />

    <MobileAppLayout title="Laporan Bulanan" :show-back="true" back-url="/" active-tab="beranda">
        <template #headerRight>
            <button
                v-if="user?.can_manage_reports"
                type="button"
                @click="openEditor"
                class="px-2.5 py-1.5 rounded-lg bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 transition cursor-pointer"
            >
                {{ report ? 'Edit' : '+ Input' }}
            </button>
        </template>

        <div class="py-4 space-y-5">
            <!-- Month-Year Selector Pill Bar -->
            <div class="bg-[#F1F5FB] rounded-2xl px-3 py-2.5 flex items-center justify-between border border-slate-200/70">
                <button
                    type="button"
                    @click="changeMonth(prevMonth)"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-slate-600 hover:bg-white transition cursor-pointer"
                    aria-label="Bulan sebelumnya"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <span class="text-sm font-extrabold text-slate-800">
                    {{ monthLabel }}
                </span>

                <button
                    type="button"
                    @click="changeMonth(nextMonth)"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-slate-600 hover:bg-white transition cursor-pointer"
                    aria-label="Bulan berikutnya"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Rekap Kehadiran & Statistik Izin/Sakit -->
            <section class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-2xs space-y-3.5">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Rujukan dari Laporan Harian Siswa
                        </p>
                        <h2 class="text-[15px] font-extrabold text-slate-800 mt-0.5">
                            Rekap Kehadiran Bulanan ({{ monthLabel }})
                        </h2>
                    </div>
                    <Link
                        :href="`/laporan-harian?date=${selectedMonth}-14`"
                        class="text-xs font-bold text-[#2E6FBE] hover:underline"
                    >
                        Lihat Laporan Harian &rarr;
                    </Link>
                </div>

                <!-- 3-Column Attendance Cards: Total Hadir, Total Izin, Total Sakit -->
                <div class="grid grid-cols-3 gap-2.5">
                    <div class="p-3.5 rounded-2xl bg-[#EBF8F0] border border-[#CBEAD7] text-center">
                        <p class="text-xs font-extrabold text-[#1E8E5A]">Total Hadir</p>
                        <p class="text-xl font-extrabold text-[#1E8E5A] mt-1">
                            {{ attendanceStats.total_hadir }}
                            <span class="text-xs font-semibold">Hari</span>
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#FEF6E0] border border-[#F9E4B7] text-center">
                        <p class="text-xs font-extrabold text-[#B7791F]">Total Izin</p>
                        <p class="text-xl font-extrabold text-[#B7791F] mt-1">
                            {{ attendanceStats.total_izin }}
                            <span class="text-xs font-semibold">Hari</span>
                        </p>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-[#FDECEC] border border-[#F9D5D5] text-center">
                        <p class="text-xs font-extrabold text-[#C9403C]">Total Sakit</p>
                        <p class="text-xl font-extrabold text-[#C9403C] mt-1">
                            {{ attendanceStats.total_sakit }}
                            <span class="text-xs font-semibold">Hari</span>
                        </p>
                    </div>
                </div>

                <!-- Riwayat Tanggal Izin & Sakit pada Bulan Ini -->
                <div v-if="attendanceStats.leave_records?.length > 0" class="pt-2 border-t border-slate-100 space-y-2">
                    <p class="text-xs font-bold text-slate-600">
                        Rincian Hari Izin &amp; Sakit Bulan {{ monthLabel }}:
                    </p>
                    <div class="space-y-1.5">
                        <Link
                            v-for="item in attendanceStats.leave_records"
                            :key="item.id"
                            :href="`/laporan-harian?date=${item.report_date}`"
                            class="p-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/70 flex items-center justify-between gap-2 transition text-xs"
                        >
                            <div class="flex items-center gap-2 min-w-0">
                                <span
                                    :class="[
                                        'px-2 py-0.5 rounded-full text-[10px] font-extrabold uppercase shrink-0',
                                        item.attendance_status === 'sakit'
                                            ? 'bg-[#FDECEC] text-[#C9403C]'
                                            : 'bg-[#FEF6E0] text-[#B7791F]',
                                    ]"
                                >
                                    {{ item.attendance_label }}
                                </span>
                                <span class="font-bold text-slate-800 shrink-0">{{ item.formatted_date }}:</span>
                                <span class="text-slate-600 truncate">{{ item.absence_reason || '-' }}</span>
                            </div>
                            <span class="text-[11px] font-bold text-[#2E6FBE] shrink-0">Detail &rarr;</span>
                        </Link>
                    </div>
                </div>
            </section>

            <div v-if="report" class="space-y-5">
                <!-- Ringkasan Perkembangan (Membaca, Menulis, Berhitung, Mengaji, Sikap Belajar) -->
                <section>
                    <h2 class="text-[15px] font-extrabold text-slate-800 mb-2.5">
                        Ringkasan Perkembangan (Membaca, Menulis, Berhitung &amp; Mengaji)
                    </h2>

                    <div class="grid grid-cols-2 lg:grid-cols-5 gap-2.5">
                        <!-- Membaca -->
                        <div class="p-3.5 rounded-2xl bg-[#EBF8F0]/80 border border-[#CBEAD7] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shrink-0 text-[#1E8E5A] shadow-2xs">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-extrabold text-slate-800">Membaca</p>
                                    <span class="text-[10px] font-bold text-[#1E8E5A]">{{ report.reading_score }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200/90 mt-1.5 overflow-hidden">
                                    <div class="h-full rounded-full bg-[#22A06B]" :style="{ width: `${report.reading_score}%` }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Menulis -->
                        <div class="p-3.5 rounded-2xl bg-[#EBF3FE]/70 border border-[#D8E7FC] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shrink-0 text-[#2E6FBE] shadow-2xs">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-extrabold text-slate-800">Menulis</p>
                                    <span class="text-[10px] font-bold text-[#2E6FBE]">{{ report.writing_score }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200/90 mt-1.5 overflow-hidden">
                                    <div class="h-full rounded-full bg-[#2E6FBE]" :style="{ width: `${report.writing_score}%` }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Berhitung -->
                        <div class="p-3.5 rounded-2xl bg-[#FEF6E0]/80 border border-[#F9E4B7] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shrink-0 text-[#D97706] font-extrabold text-xs shadow-2xs">
                                123
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-extrabold text-slate-800">Berhitung</p>
                                    <span class="text-[10px] font-bold text-[#D97706]">{{ report.counting_score }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200/90 mt-1.5 overflow-hidden">
                                    <div class="h-full rounded-full bg-[#F59E0B]" :style="{ width: `${report.counting_score}%` }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Mengaji -->
                        <div class="p-3.5 rounded-2xl bg-[#F3EEFD]/80 border border-[#E2D4F8] flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shrink-0 text-[#7C3AED] font-extrabold text-sm shadow-2xs">
                                أ ب
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-extrabold text-slate-800">Mengaji</p>
                                    <span class="text-[10px] font-bold text-[#7C3AED]">{{ report.mengaji_score ?? 80 }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200/90 mt-1.5 overflow-hidden">
                                    <div class="h-full rounded-full bg-[#7C3AED]" :style="{ width: `${report.mengaji_score ?? 80}%` }"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Sikap Belajar -->
                        <div class="p-3.5 rounded-2xl bg-rose-50/80 border border-rose-200 flex items-center gap-3 col-span-2 lg:col-span-1">
                            <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shrink-0 text-rose-600 shadow-2xs">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="10" fill="#E11D48" />
                                    <circle cx="9" cy="10" r="1.4" fill="#FFF" />
                                    <circle cx="15" cy="10" r="1.4" fill="#FFF" />
                                    <path d="M8.5 14.5c1 1.5 2.2 2 3.5 2s2.5-.5 3.5-2" stroke="#FFF" stroke-width="1.8" fill="none" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-extrabold text-slate-800">Sikap Belajar</p>
                                    <span class="text-[10px] font-bold text-rose-600">{{ report.attitude_score }}%</span>
                                </div>
                                <div class="w-full h-2 rounded-full bg-slate-200/90 mt-1.5 overflow-hidden">
                                    <div class="h-full rounded-full bg-rose-500" :style="{ width: `${report.attitude_score}%` }"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Detail Perkembangan Itemized List -->
                <section>
                    <h2 class="text-[15px] font-extrabold text-slate-800 mb-2.5">
                        Detail Perkembangan
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                        <!-- Membaca row -->
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#EBF8F0] text-[#1E8E5A] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-12 gap-2 flex-1 items-baseline">
                                <span class="col-span-4 text-xs font-extrabold text-slate-800">Membaca</span>
                                <p class="col-span-8 text-xs text-slate-600 leading-relaxed">
                                    {{ report.notes?.reading }}
                                </p>
                            </div>
                        </div>

                        <!-- Menulis row -->
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-12 gap-2 flex-1 items-baseline">
                                <span class="col-span-4 text-xs font-extrabold text-slate-800">Menulis</span>
                                <p class="col-span-8 text-xs text-slate-600 leading-relaxed">
                                    {{ report.notes?.writing }}
                                </p>
                            </div>
                        </div>

                        <!-- Berhitung row -->
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#FEF6E0] text-[#D97706] font-extrabold text-[11px] flex items-center justify-center shrink-0 mt-0.5">
                                123
                            </div>
                            <div class="grid grid-cols-12 gap-2 flex-1 items-baseline">
                                <span class="col-span-4 text-xs font-extrabold text-slate-800">Berhitung</span>
                                <p class="col-span-8 text-xs text-slate-600 leading-relaxed">
                                    {{ report.notes?.counting }}
                                </p>
                            </div>
                        </div>

                        <!-- Mengaji row -->
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#F3EEFD] text-[#7C3AED] font-extrabold text-xs flex items-center justify-center shrink-0 mt-0.5">
                                أ ب
                            </div>
                            <div class="grid grid-cols-12 gap-2 flex-1 items-baseline">
                                <span class="col-span-4 text-xs font-extrabold text-slate-800">Mengaji</span>
                                <p class="col-span-8 text-xs text-slate-600 leading-relaxed">
                                    {{ report.notes?.mengaji || 'Mengenal dan melafalkan huruf hijaiyah dengan baik.' }}
                                </p>
                            </div>
                        </div>

                        <!-- Sikap Belajar row -->
                        <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-start gap-3 md:col-span-2">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <circle cx="12" cy="12" r="10" fill="#E11D48" />
                                    <circle cx="9" cy="10" r="1.4" fill="#FFF" />
                                    <circle cx="15" cy="10" r="1.4" fill="#FFF" />
                                    <path d="M8.5 14.5c1 1.5 2.2 2 3.5 2s2.5-.5 3.5-2" stroke="#FFF" stroke-width="1.8" fill="none" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div class="grid grid-cols-12 gap-2 flex-1 items-baseline">
                                <span class="col-span-4 sm:col-span-2 text-xs font-extrabold text-slate-800">Sikap Belajar</span>
                                <p class="col-span-8 sm:col-span-10 text-xs text-slate-600 leading-relaxed">
                                    {{ report.notes?.attitude }}
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Empty State when month has no report yet -->
            <div v-else class="bg-white rounded-2xl border border-slate-200/80 p-6 text-center space-y-2">
                <p class="text-sm font-bold text-slate-800">
                    Belum ada laporan bulanan untuk {{ monthLabel }}
                </p>
                <p class="text-xs text-slate-500">
                    Evaluasi perkembangan bulanan diterbitkan setiap akhir bulan oleh guru pembimbing.
                </p>
                <div class="pt-2 flex items-center justify-center gap-2">
                    <button
                        type="button"
                        @click="changeMonth('2026-10')"
                        class="px-4 py-2 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold cursor-pointer"
                    >
                        Lihat Oktober 2026
                    </button>
                    <button
                        v-if="user?.can_manage_reports"
                        type="button"
                        @click="openEditor"
                        class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white text-xs font-bold cursor-pointer"
                    >
                        + Input Evaluasi Bulan Ini
                    </button>
                </div>
            </div>
        </div>

        <!-- Teacher / Admin Monthly Report Modal -->
        <div
            v-if="showEditorModal"
            @click.self="showEditorModal = false"
            @keydown.esc="showEditorModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="monthly-editor-title"
        >
            <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-xl my-8 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="monthly-editor-title" class="text-base font-bold text-slate-800">
                        Evaluasi Bulanan ({{ activeChild?.name }} - {{ monthLabel }})
                    </h2>
                    <button
                        type="button"
                        @click="showEditorModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer"
                        aria-label="Tutup"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitMonthly" class="space-y-3">
                    <div class="grid grid-cols-2 gap-2.5">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Skor Membaca ({{ form.reading_score }}%)</label>
                            <input v-model.number="form.reading_score" type="range" min="20" max="100" class="w-full" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Skor Menulis ({{ form.writing_score }}%)</label>
                            <input v-model.number="form.writing_score" type="range" min="20" max="100" class="w-full" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Skor Berhitung ({{ form.counting_score }}%)</label>
                            <input v-model.number="form.counting_score" type="range" min="20" max="100" class="w-full" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Skor Mengaji ({{ form.mengaji_score }}%)</label>
                            <input v-model.number="form.mengaji_score" type="range" min="20" max="100" class="w-full" />
                        </div>
                        <div class="col-span-2">
                            <label class="font-semibold text-slate-700 block mb-1">Sikap Belajar ({{ form.attitude_score }}%)</label>
                            <input v-model.number="form.attitude_score" type="range" min="20" max="100" class="w-full" />
                        </div>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Membaca</label>
                        <input v-model="form.note_reading" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Menulis</label>
                        <input v-model="form.note_writing" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Berhitung</label>
                        <input v-model="form.note_counting" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Mengaji</label>
                        <input v-model="form.note_mengaji" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Catatan Sikap Belajar</label>
                        <input v-model="form.note_attitude" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showEditorModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="form.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold cursor-pointer">
                            Simpan Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>
