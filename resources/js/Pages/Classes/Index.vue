<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    myClasses: {
        type: Array,
        default: () => [],
    },
    allClasses: {
        type: Array,
        default: () => [],
    },
    activeClass: {
        type: Object,
        default: null,
    },
    pendingChildren: {
        type: Array,
        default: () => [],
    },
    allTeachers: {
        type: Array,
        default: () => [],
    },
    parentOptions: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const showCreateClassModal = ref(false);
const showEditQuotaModal = ref(false);
const showMappingModal = ref(false);
const showAddTeacherModal = ref(false);
const showAttachClassModal = ref(false);

// Track target class selection per pending child for Admin approval
const approvalClassSelection = ref(
    Object.fromEntries(props.pendingChildren.map((c) => [c.id, c.class_group_id || props.allClasses[0]?.id]))
);

const allWeekDays = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

function formatScheduleLabel(days, startTime, endTime) {
    const ordered = allWeekDays.filter((d) => days.includes(d));
    if (ordered.length === 0) return 'Senin - Jumat, 10.00 - 11.00';
    const indices = ordered.map((d) => allWeekDays.indexOf(d));
    const isConsecutive = indices.length > 2 && indices[indices.length - 1] - indices[0] === indices.length - 1;
    const dayStr = isConsecutive ? `${ordered[0]} - ${ordered[ordered.length - 1]}` : ordered.join(', ');
    const st = (startTime || '10:00').replace(':', '.');
    const et = (endTime || '11:00').replace(':', '.');
    return `${dayStr}, ${st} - ${et}`;
}

function parseScheduleString(scheduleStr) {
    const defaultResult = {
        days: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
        start_time: '10:00',
        end_time: '11:00',
    };
    if (!scheduleStr) return defaultResult;

    const timeMatch = scheduleStr.match(/(\d{1,2})[:.](\d{2})\s*-\s*(\d{1,2})[:.](\d{2})/);
    const start_time = timeMatch ? `${timeMatch[1].padStart(2, '0')}:${timeMatch[2]}` : '10:00';
    const end_time = timeMatch ? `${timeMatch[3].padStart(2, '0')}:${timeMatch[4]}` : '11:00';

    const rangeMatch = scheduleStr.match(/(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu|Minggu)\s*-\s*(Senin|Selasa|Rabu|Kamis|Jumat|Sabtu|Minggu)/i);
    if (rangeMatch) {
        const startIdx = allWeekDays.findIndex((d) => d.toLowerCase() === rangeMatch[1].toLowerCase());
        const endIdx = allWeekDays.findIndex((d) => d.toLowerCase() === rangeMatch[2].toLowerCase());
        if (startIdx !== -1 && endIdx !== -1 && endIdx >= startIdx) {
            return {
                days: allWeekDays.slice(startIdx, endIdx + 1),
                start_time,
                end_time,
            };
        }
    }

    const foundDays = allWeekDays.filter((d) => new RegExp(d, 'i').test(scheduleStr));
    return {
        days: foundDays.length ? foundDays : defaultResult.days,
        start_time,
        end_time,
    };
}

const createClassForm = useForm({
    name: '',
    category: 'Calistung',
    quota: 10,
    days: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'],
    start_time: '10:00',
    end_time: '11:00',
    schedule: 'Senin - Jumat, 10.00 - 11.00',
    teacher_ids: user.value?.is_teacher ? [user.value.id] : [],
});

const initialQuotaParsed = parseScheduleString(props.activeClass?.schedule);
const quotaForm = useForm({
    name: props.activeClass?.name || '',
    category: props.activeClass?.category || 'Calistung',
    quota: props.activeClass?.quota || 10,
    days: initialQuotaParsed.days,
    start_time: initialQuotaParsed.start_time,
    end_time: initialQuotaParsed.end_time,
    schedule: props.activeClass?.schedule || 'Senin - Jumat, 10.00 - 11.00',
});

function toggleDay(formObj, day) {
    const idx = formObj.days.indexOf(day);
    if (idx > -1) {
        if (formObj.days.length > 1) {
            formObj.days.splice(idx, 1);
        }
    } else {
        formObj.days.push(day);
    }
    formObj.days = allWeekDays.filter((d) => formObj.days.includes(d));
    formObj.schedule = formatScheduleLabel(formObj.days, formObj.start_time, formObj.end_time);
}

function setPresetDays(formObj, presetDays) {
    formObj.days = [...presetDays];
    formObj.schedule = formatScheduleLabel(formObj.days, formObj.start_time, formObj.end_time);
}

function syncScheduleText(formObj) {
    formObj.schedule = formatScheduleLabel(formObj.days, formObj.start_time, formObj.end_time);
}

const mappingForm = useForm({
    teacher_ids: props.activeClass?.teacher_ids ? [...props.activeClass.teacher_ids] : [],
});

const attachClassForm = useForm({
    teacher_id: props.allTeachers[0]?.id || '',
    class_group_id: props.allClasses[0]?.id || '',
});

const addTeacherForm = useForm({
    name: '',
    email: '',
    phone: '',
    class_ids: props.activeClass ? [props.activeClass.id] : [],
});

function selectClass(classId) {
    router.get('/kelas', { class_id: classId }, { preserveState: false });
}

function openEditQuota() {
    if (!props.activeClass) return;
    const parsed = parseScheduleString(props.activeClass.schedule);
    quotaForm.name = props.activeClass.name;
    quotaForm.category = props.activeClass.category;
    quotaForm.quota = props.activeClass.quota;
    quotaForm.days = parsed.days;
    quotaForm.start_time = parsed.start_time;
    quotaForm.end_time = parsed.end_time;
    quotaForm.schedule = props.activeClass.schedule;
    showEditQuotaModal.value = true;
}

function openMappingModal() {
    if (!props.activeClass) return;
    mappingForm.teacher_ids = [...(props.activeClass.teacher_ids || [])];
    showMappingModal.value = true;
}

function submitCreateClass() {
    syncScheduleText(createClassForm);
    createClassForm.post('/kelas', {
        onSuccess: () => {
            showCreateClassModal.value = false;
            createClassForm.reset('name');
        },
    });
}

function submitEditQuota() {
    if (!props.activeClass) return;
    syncScheduleText(quotaForm);
    quotaForm.patch(`/kelas/${props.activeClass.id}/quota`, {
        onSuccess: () => {
            showEditQuotaModal.value = false;
        },
    });
}

function submitMapping() {
    if (!props.activeClass) return;
    mappingForm.post(`/kelas/${props.activeClass.id}/teachers`, {
        onSuccess: () => {
            showMappingModal.value = false;
        },
    });
}

function submitAttachClass() {
    attachClassForm.post('/kelas/attach-teacher', {
        onSuccess: () => {
            showAttachClassModal.value = false;
        },
    });
}

function submitAddTeacher() {
    addTeacherForm.post('/teachers', {
        onSuccess: () => {
            showAddTeacherModal.value = false;
            addTeacherForm.reset('name', 'email', 'phone');
        },
    });
}

function deleteActiveClass() {
    if (!props.activeClass) return;
    if (confirm(`Apakah Anda yakin ingin menghapus kelas "${props.activeClass.name}"? Data penempatan siswa pada kelas ini akan dilepas.`)) {
        router.delete(`/kelas/${props.activeClass.id}`);
    }
}

function handleApproval(childId, status) {
    const targetClassId = approvalClassSelection.value[childId];
    router.patch(
        `/children/${childId}/approval`,
        {
            enrollment_status: status,
            class_group_id: targetClassId,
        },
        { preserveScroll: true }
    );
}

function openChildModule(childId, destinationUrl) {
    router.post(
        `/children/${childId}/select`,
        {},
        {
            onSuccess: () => {
                router.visit(destinationUrl);
            },
        }
    );
}
</script>

<template>
    <Head title="Kelas & Daftar Siswa" />

    <MobileAppLayout title="Kelas & Siswa" :show-back="true" back-url="/" active-tab="kelas">
        <template #headerRight>
            <button
                v-if="user?.is_admin"
                type="button"
                @click="showCreateClassModal = true"
                class="px-2.5 py-1.5 rounded-lg bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 transition cursor-pointer"
            >
                + Kelas
            </button>
        </template>

        <div class="py-4 space-y-5">
            <!-- 1. Admin Pending Enrollment Approval Queue (Feature #2 & #3) -->
            <section
                v-if="pendingChildren.length > 0"
                class="p-4 rounded-2xl bg-[#FEF6E0] border border-[#F9E4B7] space-y-3"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <span class="px-2 py-0.5 rounded-full bg-amber-600 text-white text-[10px] font-extrabold uppercase tracking-wider">
                            Menunggu Persetujuan ({{ pendingChildren.length }})
                        </span>
                        <h2 class="text-sm font-extrabold text-slate-800 mt-1">
                            Pendaftaran Siswa Baru
                        </h2>
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div
                        v-for="child in pendingChildren"
                        :key="child.id"
                        class="p-3 rounded-xl bg-white border border-amber-200 shadow-2xs space-y-2.5"
                    >
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <img :src="child.photo_url" :alt="child.name" class="w-10 h-10 rounded-full object-cover bg-slate-100 shrink-0" />
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-slate-800 truncate">
                                        {{ child.name }}
                                        <span v-if="child.gender" class="ml-1 text-[10px] font-semibold text-slate-500">
                                            ({{ child.gender.toLowerCase() === 'laki-laki' ? '👦 Laki-laki' : '👧 Perempuan' }})
                                        </span>
                                    </p>
                                    <p class="text-[11px] text-slate-500 truncate">
                                        Orang Tua: {{ child.parent_name }} ({{ child.parent_phone }})
                                    </p>
                                    <p v-if="child.address" class="text-[10px] text-slate-400 truncate">
                                        📍 {{ child.address }}
                                    </p>
                                    <p class="text-[11px] font-semibold text-[#2E6FBE]">
                                        Pilihan: {{ child.group_name }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Admin Approval Controls with Class & Quota Selection -->
                        <div v-if="user?.is_admin" class="pt-1 border-t border-slate-100 space-y-2">
                            <div>
                                <label class="text-[10px] font-bold text-slate-500 block mb-1">
                                    Tempatkan di Kelas (Cek Sisa Kuota):
                                </label>
                                <select
                                    v-model="approvalClassSelection[child.id]"
                                    class="w-full px-2.5 py-1.5 rounded-lg border border-slate-200 text-xs bg-slate-50 font-semibold text-slate-700"
                                >
                                    <option v-for="cg in allClasses" :key="cg.id" :value="cg.id">
                                        {{ cg.name }} (Terisi {{ cg.approved_count }}/{{ cg.quota }} • Sisa {{ cg.remaining_quota }})
                                    </option>
                                </select>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="handleApproval(child.id, 'approved')"
                                    class="flex-1 py-2 rounded-xl bg-[#34A853] hover:bg-emerald-700 text-white text-xs font-bold transition cursor-pointer"
                                >
                                    ✓ Setujui Masuk Kelas
                                </button>
                                <button
                                    type="button"
                                    @click="handleApproval(child.id, 'rejected')"
                                    class="px-3 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition cursor-pointer"
                                >
                                    Tolak
                                </button>
                            </div>
                        </div>
                        <p v-else class="text-[11px] text-amber-800 font-medium">
                            Menunggu verifikasi &amp; persetujuan Admin Sabila.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 2. Multi-Class Switcher Pills for Teacher & Admin (Feature #5) -->
            <section class="space-y-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-xs font-extrabold uppercase tracking-wider text-slate-500">
                        {{ user?.is_admin ? 'Daftar Semua Kelas' : 'Kelas yang Anda Ajar' }} ({{ myClasses.length }})
                    </h2>
                    <button
                        v-if="user?.is_admin"
                        type="button"
                        @click="showCreateClassModal = true"
                        class="text-xs font-bold text-[#2E6FBE] hover:underline cursor-pointer"
                    >
                        + Tambah Kelas Baru
                    </button>
                </div>

                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                    <button
                        v-for="cg in myClasses"
                        :key="cg.id"
                        type="button"
                        @click="selectClass(cg.id)"
                        :class="[
                            'px-3.5 py-2 rounded-2xl text-xs font-bold whitespace-nowrap transition shrink-0 flex items-center gap-1.5 cursor-pointer',
                            activeClass?.id === cg.id
                                ? 'bg-[#2E6FBE] text-white shadow-xs'
                                : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50',
                        ]"
                    >
                        <span>{{ cg.name }}</span>
                        <span
                            :class="[
                                'px-1.5 py-0.2 rounded-full text-[10px] font-extrabold',
                                activeClass?.id === cg.id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600',
                            ]"
                        >
                            {{ cg.approved_count }}/{{ cg.quota }}
                        </span>
                    </button>
                </div>
            </section>

            <!-- 3. Active Class Card: Quota Bar + Teacher Mapping (Feature #3 & #4) -->
            <section v-if="activeClass" class="space-y-4">
                <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-2xs space-y-3">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <span class="px-2.5 py-0.5 rounded-full bg-[#EBF3FE] text-[#2E6FBE] text-[10px] font-extrabold">
                                {{ activeClass.category }}
                            </span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-1">
                                {{ activeClass.name }}
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Jadwal: {{ activeClass.schedule }}
                            </p>
                        </div>

                        <div v-if="user?.is_admin" class="flex items-center gap-2 shrink-0">
                            <button
                                type="button"
                                @click="openEditQuota"
                                class="px-2.5 py-1.5 rounded-xl bg-[#FEF6E0] border border-[#F9E4B7] text-[#B7791F] text-xs font-bold hover:brightness-95 cursor-pointer"
                            >
                                Atur Kuota
                            </button>
                            <button
                                type="button"
                                @click="deleteActiveClass"
                                class="px-2.5 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold hover:bg-rose-100 transition cursor-pointer"
                                title="Hapus Kelas"
                            >
                                Hapus Kelas
                            </button>
                        </div>
                    </div>

                    <!-- Quota Visual Progress Indicator -->
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-700">
                                Kuota Kelas: {{ activeClass.approved_count }} / {{ activeClass.quota }} Siswa
                            </span>
                            <span
                                :class="[
                                    'font-extrabold',
                                    activeClass.is_full ? 'text-rose-600' : 'text-[#1E8E5A]',
                                ]"
                            >
                                {{ activeClass.is_full ? 'Kuota Penuh' : `Sisa ${activeClass.remaining_quota} Kursi` }}
                            </span>
                        </div>
                        <div class="w-full h-2.5 rounded-full bg-slate-200 overflow-hidden">
                            <div
                                :class="[
                                    'h-full rounded-full transition-all',
                                    activeClass.is_full ? 'bg-rose-500' : 'bg-[#2E6FBE]',
                                ]"
                                :style="{ width: `${Math.min(100, Math.round((activeClass.approved_count / activeClass.quota) * 100))}%` }"
                            ></div>
                        </div>
                    </div>

                    <!-- Mapped Teachers Section -->
                    <div class="pt-1 border-t border-slate-100 flex items-center justify-between gap-2">
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                Guru Pengajar Kelas Ini
                            </p>
                            <div v-if="activeClass.teachers?.length" class="flex flex-wrap gap-1.5 mt-1">
                                <span
                                    v-for="t in activeClass.teachers"
                                    :key="t.id"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#EBF8F0] text-[#1E8E5A] text-xs font-bold"
                                >
                                    {{ t.name }}
                                </span>
                            </div>
                            <p v-else class="text-xs text-rose-600 font-semibold mt-1">
                                Belum ada guru yang di-mapping ke kelas ini
                            </p>
                        </div>

                        <div v-if="user?.is_admin" class="flex flex-col gap-1 shrink-0">
                            <button
                                type="button"
                                @click="openMappingModal"
                                class="px-2.5 py-1.5 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 cursor-pointer"
                            >
                                Mapping Guru
                            </button>
                            <button
                                type="button"
                                @click="showAddTeacherModal = true"
                                class="text-[11px] font-bold text-slate-500 hover:text-slate-800 text-right cursor-pointer"
                            >
                                + Guru Baru
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 4. Enrolled Students List in Active Class + Teacher/Admin Actions (Feature #5 & #6) -->
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between">
                        <h3 class="text-[15px] font-extrabold text-slate-800">
                            Daftar Anak di {{ activeClass.name }} ({{ activeClass.approved_children?.length || 0 }})
                        </h3>
                    </div>

                    <div v-if="activeClass.approved_children?.length > 0" class="space-y-2.5">
                        <div
                            v-for="student in activeClass.approved_children"
                            :key="student.id"
                            class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs space-y-3"
                        >
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img
                                        :src="student.photo_url"
                                        :alt="student.name"
                                        class="w-11 h-11 rounded-full object-cover border border-slate-200 shrink-0 bg-rose-50"
                                    />
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <h4 class="text-sm font-extrabold text-slate-800 truncate">
                                                {{ student.name }}
                                            </h4>
                                            <span class="px-2 py-0.2 rounded-full bg-[#EBF8F0] text-[#1E8E5A] text-[10px] font-bold">
                                                Aktif
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                            Orang Tua: {{ student.parent_name }} • Lahir: {{ student.birth_date }}
                                            <span v-if="student.gender"> • {{ student.gender.toLowerCase() === 'laki-laki' ? '👦 Laki-laki' : '👧 Perempuan' }}</span>
                                        </p>
                                        <p v-if="student.address" class="text-[10px] text-slate-400 truncate">
                                            📍 {{ student.address }}
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    @click="openChildModule(student.id, '/profil')"
                                    class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold shrink-0 cursor-pointer"
                                >
                                    Profil
                                </button>
                            </div>

                            <!-- Teacher & Admin Quick Actions per Student (Feature #5 & #6: Admin has same features as Teacher) -->
                            <div class="grid grid-cols-3 gap-1.5 pt-2 border-t border-slate-100">
                                <button
                                    type="button"
                                    @click="openChildModule(student.id, '/laporan-harian')"
                                    class="py-1.5 px-2 rounded-xl bg-[#FDECEC] text-[#C9403C] text-[11px] font-bold hover:brightness-95 transition cursor-pointer"
                                >
                                    Laporan Harian
                                </button>
                                <button
                                    type="button"
                                    @click="openChildModule(student.id, '/laporan-bulanan')"
                                    class="py-1.5 px-2 rounded-xl bg-[#F3EEFD] text-[#6D28D9] text-[11px] font-bold hover:brightness-95 transition cursor-pointer"
                                >
                                    Laporan Bulanan
                                </button>
                                <button
                                    type="button"
                                    @click="openChildModule(student.id, '/foto-kegiatan')"
                                    class="py-1.5 px-2 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-[11px] font-bold hover:brightness-95 transition cursor-pointer"
                                >
                                    Foto Kegiatan
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-else class="bg-white rounded-2xl border border-slate-200/80 p-6 text-center space-y-2">
                        <p class="text-sm font-bold text-slate-800">Belum ada siswa aktif di kelas ini</p>
                        <p class="text-xs text-slate-500">
                            Setujui pendaftaran siswa dari antrean di atas atau daftarkan siswa baru ke {{ activeClass.name }}.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 5. Overview Mapping Seluruh Guru (Visible for Admin) -->
            <section v-if="user?.is_admin" class="pt-2 space-y-2.5">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-extrabold text-slate-800">
                        Ringkasan Mapping Guru Pengajar
                    </h3>
                    <button
                        type="button"
                        @click="showAddTeacherModal = true"
                        class="text-xs font-bold text-[#2E6FBE] hover:underline cursor-pointer"
                    >
                        + Tambah Guru
                    </button>
                </div>

                <div class="space-y-2">
                    <div
                        v-for="teacher in allTeachers"
                        :key="teacher.id"
                        class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center justify-between gap-3"
                    >
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-slate-800">{{ teacher.name }}</p>
                            <p class="text-[11px] text-slate-500">{{ teacher.email }} • {{ teacher.phone }}</p>
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                <span
                                    v-for="(cname, idx) in teacher.class_names"
                                    :key="idx"
                                    class="px-2 py-0.5 rounded-md bg-[#EBF3FE] text-[#2E6FBE] text-[10px] font-bold"
                                >
                                    {{ cname }}
                                </span>
                                <span v-if="!teacher.class_names.length" class="text-[11px] text-slate-400">
                                    Belum ada kelas
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 shrink-0">
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[11px] font-bold">
                                {{ teacher.class_names.length }} Kelas
                            </span>
                            <button
                                type="button"
                                @click="
                                    attachClassForm.teacher_id = teacher.id;
                                    showAttachClassModal = true;
                                "
                                class="text-[11px] font-bold text-[#2E6FBE] hover:underline cursor-pointer"
                            >
                                + Tambah Kelas Mengajar
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal 1: Admin Edit Quota & Class Info -->
        <div
            v-if="showEditQuotaModal"
            @click.self="showEditQuotaModal = false"
            @keydown.esc="showEditQuotaModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="quota-modal-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="quota-modal-title" class="text-base font-bold text-slate-800">
                        Atur Kuota &amp; Jadwal Kelas
                    </h2>
                    <button type="button" @click="showEditQuotaModal = false" class="w-8 h-8 rounded-full text-slate-400">✕</button>
                </div>
                <form @submit.prevent="submitEditQuota" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Kelas</label>
                        <input v-model="quotaForm.name" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Program</label>
                            <select v-model="quotaForm.category" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                                <option value="Calistung">Calistung</option>
                                <option value="Pracalis">Pracalis</option>
                                <option value="Mengaji">Mengaji</option>
                                <option value="Membaca">Membaca</option>
                                <option value="Menulis">Menulis</option>
                                <option value="Berhitung">Berhitung</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Kuota Maksimal Siswa</label>
                            <input v-model.number="quotaForm.quota" type="number" min="1" max="100" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm font-bold" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="font-semibold text-slate-700">Hari Belajar (Senin - Minggu)</label>
                            <div class="flex gap-1">
                                <button
                                    type="button"
                                    @click="setPresetDays(quotaForm, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])"
                                    class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 cursor-pointer"
                                >
                                    Sen-Jum
                                </button>
                                <button
                                    type="button"
                                    @click="setPresetDays(quotaForm, ['Sabtu', 'Minggu'])"
                                    class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 cursor-pointer"
                                >
                                    Sab-Min
                                </button>
                                <button
                                    type="button"
                                    @click="setPresetDays(quotaForm, allWeekDays)"
                                    class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 cursor-pointer"
                                >
                                    Semua
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-1.5">
                            <button
                                v-for="day in allWeekDays"
                                :key="day"
                                type="button"
                                @click="toggleDay(quotaForm, day)"
                                :class="[
                                    'py-1.5 px-1 rounded-xl text-[11px] font-bold border transition cursor-pointer text-center',
                                    quotaForm.days.includes(day)
                                        ? 'bg-[#2E6FBE] text-white border-[#2E6FBE]'
                                        : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100',
                                ]"
                            >
                                {{ day.slice(0, 3) }}
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Jam Mulai</label>
                                <input
                                    v-model="quotaForm.start_time"
                                    @change="syncScheduleText(quotaForm)"
                                    type="time"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800"
                                />
                            </div>
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Jam Selesai</label>
                                <input
                                    v-model="quotaForm.end_time"
                                    @change="syncScheduleText(quotaForm)"
                                    type="time"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800"
                                />
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200/70">
                            Format jadwal: <strong class="text-slate-800">{{ quotaForm.schedule }}</strong>
                        </p>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showEditQuotaModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="quotaForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Simpan Kuota
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 2: Admin Mapping Guru ke Kelas -->
        <div
            v-if="showMappingModal"
            @click.self="showMappingModal = false"
            @keydown.esc="showMappingModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="mapping-modal-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="mapping-modal-title" class="text-base font-bold text-slate-800">
                        Mapping Guru: {{ activeClass?.name }}
                    </h2>
                    <button type="button" @click="showMappingModal = false" class="w-8 h-8 rounded-full text-slate-400">✕</button>
                </div>
                <p class="text-slate-500">
                    Pilih satu atau lebih guru pengajar untuk kelas ini. Satu guru juga dapat mengajar lebih dari satu kelas.
                </p>
                <form @submit.prevent="submitMapping" class="space-y-3">
                    <div class="space-y-2 max-h-56 overflow-y-auto p-1">
                        <label
                            v-for="teacher in allTeachers"
                            :key="teacher.id"
                            class="flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer"
                        >
                            <input
                                v-model="mappingForm.teacher_ids"
                                type="checkbox"
                                :value="teacher.id"
                                class="w-4 h-4 rounded text-[#2E6FBE]"
                            />
                            <div>
                                <p class="font-bold text-slate-800">{{ teacher.name }}</p>
                                <p class="text-[11px] text-slate-500">
                                    Mengajar: {{ teacher.class_names.join(', ') || 'Belum ada kelas' }}
                                </p>
                            </div>
                        </label>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showMappingModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="mappingForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Simpan Mapping Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 3: Create New Class (Admin Only) -->
        <div
            v-if="showCreateClassModal && user?.is_admin"
            @click.self="showCreateClassModal = false"
            @keydown.esc="showCreateClassModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="create-class-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="create-class-title" class="text-base font-bold text-slate-800">
                        Tambah Kelas Baru
                    </h2>
                    <button type="button" @click="showCreateClassModal = false" class="w-8 h-8 rounded-full text-slate-400">✕</button>
                </div>
                <form @submit.prevent="submitCreateClass" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Kelas</label>
                        <input
                            v-model="createClassForm.name"
                            type="text"
                            required
                            placeholder="Contoh: Kelompok Calistung C"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"
                        />
                        <p v-if="createClassForm.errors.name" class="text-rose-600 mt-1">{{ createClassForm.errors.name }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Program</label>
                            <select v-model="createClassForm.category" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                                <option value="Calistung">Calistung</option>
                                <option value="Pracalis">Pracalis</option>
                                <option value="Mengaji">Mengaji</option>
                                <option value="Membaca">Membaca</option>
                                <option value="Menulis">Menulis</option>
                                <option value="Berhitung">Berhitung</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Kuota Siswa</label>
                            <input v-model.number="createClassForm.quota" type="number" min="1" max="50" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <label class="font-semibold text-slate-700">Hari Belajar (Senin - Minggu)</label>
                            <div class="flex gap-1">
                                <button
                                    type="button"
                                    @click="setPresetDays(createClassForm, ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])"
                                    class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 cursor-pointer"
                                >
                                    Sen-Jum
                                </button>
                                <button
                                    type="button"
                                    @click="setPresetDays(createClassForm, ['Sabtu', 'Minggu'])"
                                    class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 cursor-pointer"
                                >
                                    Sab-Min
                                </button>
                                <button
                                    type="button"
                                    @click="setPresetDays(createClassForm, allWeekDays)"
                                    class="px-2 py-0.5 rounded-md bg-slate-100 hover:bg-slate-200 text-[10px] font-bold text-slate-600 cursor-pointer"
                                >
                                    Semua
                                </button>
                            </div>
                        </div>
                        <div class="grid grid-cols-4 sm:grid-cols-7 gap-1.5">
                            <button
                                v-for="day in allWeekDays"
                                :key="day"
                                type="button"
                                @click="toggleDay(createClassForm, day)"
                                :class="[
                                    'py-1.5 px-1 rounded-xl text-[11px] font-bold border transition cursor-pointer text-center',
                                    createClassForm.days.includes(day)
                                        ? 'bg-[#2E6FBE] text-white border-[#2E6FBE]'
                                        : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100',
                                ]"
                            >
                                {{ day.slice(0, 3) }}
                            </button>
                        </div>
                        <div class="grid grid-cols-2 gap-2 pt-1">
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Jam Mulai</label>
                                <input
                                    v-model="createClassForm.start_time"
                                    @change="syncScheduleText(createClassForm)"
                                    type="time"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800"
                                />
                            </div>
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Jam Selesai</label>
                                <input
                                    v-model="createClassForm.end_time"
                                    @change="syncScheduleText(createClassForm)"
                                    type="time"
                                    required
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white font-bold text-slate-800"
                                />
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200/70">
                            Format jadwal: <strong class="text-slate-800">{{ createClassForm.schedule }}</strong>
                        </p>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Mapping Guru Pengajar</label>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto border border-slate-200 rounded-xl p-2">
                            <label v-for="t in allTeachers" :key="t.id" class="flex items-center gap-2 text-xs">
                                <input v-model="createClassForm.teacher_ids" type="checkbox" :value="t.id" class="rounded text-[#2E6FBE]" />
                                <span>{{ t.name }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showCreateClassModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="createClassForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Buat Kelas
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 4: Admin Assigns Additional Class to a Teacher -->
        <div
            v-if="showAttachClassModal && user?.is_admin"
            @click.self="showAttachClassModal = false"
            @keydown.esc="showAttachClassModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="attach-class-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="attach-class-title" class="text-base font-bold text-slate-800">
                        Tambah Kelas Mengajar Guru
                    </h2>
                    <button type="button" @click="showAttachClassModal = false" class="w-8 h-8 rounded-full text-slate-400">✕</button>
                </div>
                <p class="text-slate-500">
                    Pilih guru dan kelas tambahan yang akan diajar oleh guru tersebut.
                </p>
                <form @submit.prevent="submitAttachClass" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Pilih Guru</label>
                        <select v-model="attachClassForm.teacher_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white">
                            <option v-for="t in allTeachers" :key="t.id" :value="t.id">
                                {{ t.name }} ({{ t.class_names.length }} Kelas)
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Pilih Kelas</label>
                        <select v-model="attachClassForm.class_group_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white">
                            <option v-for="cg in allClasses" :key="cg.id" :value="cg.id">
                                {{ cg.name }} ({{ cg.approved_count }}/{{ cg.quota }} Siswa)
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-between items-center pt-2">
                        <button
                            type="button"
                            @click="
                                showAttachClassModal = false;
                                showCreateClassModal = true;
                            "
                            class="text-xs font-bold text-[#2E6FBE] hover:underline"
                        >
                            + Buat Kelas Baru
                        </button>
                        <div class="flex gap-2">
                            <button type="button" @click="showAttachClassModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                                Batal
                            </button>
                            <button type="submit" :disabled="attachClassForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                                Tambahkan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal 5: Admin Create New Teacher & Map to Classes -->
        <div
            v-if="showAddTeacherModal"
            @click.self="showAddTeacherModal = false"
            @keydown.esc="showAddTeacherModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="add-teacher-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="add-teacher-title" class="text-base font-bold text-slate-800">
                        Tambah Guru &amp; Mapping Kelas
                    </h2>
                    <button type="button" @click="showAddTeacherModal = false" class="w-8 h-8 rounded-full text-slate-400">✕</button>
                </div>
                <form @submit.prevent="submitAddTeacher" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Guru</label>
                        <input v-model="addTeacherForm.name" type="text" required placeholder="Contoh: Bu Guru Rini" class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Email</label>
                        <input v-model="addTeacherForm.email" type="email" required placeholder="rini@sabilafun.id" class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nomor HP</label>
                        <input v-model="addTeacherForm.phone" type="text" required placeholder="0812 9988 7766" class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Mapping ke Kelas (Bisa pilih &gt; 1 kelas)</label>
                        <div class="space-y-1.5 max-h-36 overflow-y-auto border border-slate-200 rounded-xl p-2">
                            <label v-for="cg in allClasses" :key="cg.id" class="flex items-center gap-2 text-xs">
                                <input v-model="addTeacherForm.class_ids" type="checkbox" :value="cg.id" class="rounded text-[#2E6FBE]" />
                                <span>{{ cg.name }}</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showAddTeacherModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="addTeacherForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Simpan Guru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>

