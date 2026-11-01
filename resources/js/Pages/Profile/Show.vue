<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    childProfile: {
        type: Object,
        default: null,
    },
    parentOptions: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const children = computed(() => page.props.auth?.children || []);
const classGroups = computed(() => page.props.auth?.classGroups || []);
const flashSuccess = computed(() => page.props.flash?.success);

const showAccountModal = ref(false);
const showAccountPassword = ref(false);
const showEditModal = ref(false);
const showEnrollChildModal = ref(false);

const accountForm = useForm({
    name: user.value?.name || '',
    email: user.value?.email || '',
    phone: user.value?.phone || '',
    current_password: '',
    password: '',
    password_confirmation: '',
});

function openAccountModal() {
    accountForm.name = user.value?.name || '';
    accountForm.email = user.value?.email || '';
    accountForm.phone = user.value?.phone || '';
    accountForm.current_password = '';
    accountForm.password = '';
    accountForm.password_confirmation = '';
    accountForm.clearErrors();
    showAccountModal.value = true;
}

function submitAccountUpdate() {
    accountForm.patch('/profil/account', {
        preserveScroll: true,
        onSuccess: () => {
            showAccountModal.value = false;
            accountForm.reset('current_password', 'password', 'password_confirmation');
        },
    });
}

const editForm = useForm({
    name: props.childProfile?.name || '',
    group_name: props.childProfile?.group_name || '',
    birth_date: props.childProfile?.birth_date || '',
    join_date: props.childProfile?.join_date || '',
    parent_name: props.childProfile?.parent?.name || '',
    parent_phone: props.childProfile?.parent?.phone || '',
    parent_email: props.childProfile?.parent?.email || '',
    parent_address: props.childProfile?.parent?.address || '',
    photo: null,
});

const enrollPhotoPreview = ref(null);

const enrollForm = useForm({
    parent_id: props.parentOptions?.[0]?.id || '',
    name: '',
    class_group_id: classGroups.value[0]?.id || '',
    birth_date: '2022-06-15',
    gender: 'laki-laki',
    address: '',
    photo: null,
});

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

function openEditModal() {
    if (!props.childProfile) return;
    editForm.name = props.childProfile.name;
    editForm.group_name = props.childProfile.group_name;
    editForm.birth_date = props.childProfile.birth_date;
    editForm.join_date = props.childProfile.join_date;
    editForm.parent_name = props.childProfile.parent?.name || '';
    editForm.parent_phone = props.childProfile.parent?.phone || '';
    editForm.parent_email = props.childProfile.parent?.email || '';
    editForm.parent_address = props.childProfile.parent?.address || '';
    editForm.photo = null;
    showEditModal.value = true;
}

function handlePhotoChange(e) {
    editForm.photo = e.target.files[0] || null;
}

function submitEdit() {
    if (!props.childProfile) return;
    editForm.post(`/profil/${props.childProfile.id}`, {
        forceFormData: true,
        onSuccess: () => {
            showEditModal.value = false;
        },
    });
}

function submitEnrollChild() {
    enrollForm.post('/children/enroll', {
        forceFormData: true,
        onSuccess: () => {
            showEnrollChildModal.value = false;
            enrollForm.reset('name', 'photo');
        },
    });
}

function approveCurrentChild() {
    if (!props.childProfile) return;
    router.patch(`/children/${props.childProfile.id}/approval`, {
        enrollment_status: 'approved',
        class_group_id: props.childProfile.class_group_id,
    });
}

function switchChild(childId) {
    router.post(`/children/${childId}/select`, {}, { preserveScroll: true });
}

function logout() {
    router.post('/logout');
}

const roleBadge = computed(() => {
    if (user.value?.is_admin) return { label: 'Administrator', bg: 'bg-rose-100 text-rose-700 border-rose-200' };
    if (user.value?.is_teacher) return { label: 'Guru Pengajar', bg: 'bg-blue-100 text-blue-700 border-blue-200' };
    return { label: 'Orang Tua / Wali', bg: 'bg-emerald-100 text-emerald-700 border-emerald-200' };
});
</script>

<template>
    <Head title="Profil & Akun" />

    <MobileAppLayout title="Profil & Akun" :show-back="true" back-url="/" active-tab="profil">
        <template #headerRight>
            <button
                v-if="childProfile"
                type="button"
                @click="openEditModal"
                class="inline-flex items-center gap-1 text-xs font-bold text-[#2E6FBE] hover:underline py-1 px-1.5 cursor-pointer"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                <span>Edit Anak</span>
            </button>
        </template>

        <div class="py-4 space-y-5">
            <!-- Flash notification if any -->
            <div v-if="flashSuccess" class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ flashSuccess }}</span>
            </div>

            <!-- Kartu Profil Akun Pengguna (Berlaku untuk Admin, Guru, & Orang Tua) -->
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-[#2E6FBE] to-[#5C9CE6] flex items-center justify-center text-white font-extrabold text-lg shadow-xs">
                            {{ user?.name ? user.name.charAt(0).toUpperCase() : 'U' }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-sm font-extrabold text-slate-800">{{ user?.name }}</h2>
                                <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold border', roleBadge.bg]">
                                    {{ roleBadge.label }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">{{ user?.email }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-2 text-slate-600">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="font-medium">No. HP:</span>
                        <span class="font-bold text-slate-800">{{ user?.phone || '-' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-slate-600">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="font-medium">Keamanan:</span>
                        <span class="font-semibold text-slate-700">Kata Sandi Terproteksi</span>
                    </div>
                </div>

                <div class="pt-1">
                    <button
                        type="button"
                        @click="openAccountModal"
                        class="w-full py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 text-[#2E6FBE] text-xs font-bold transition flex items-center justify-center gap-1.5 cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>Ubah Profil Akun, Email & Kata Sandi</span>
                    </button>
                </div>
            </div>

            <!-- Jika Ada Profil Anak -->
            <div v-if="childProfile" class="space-y-5">
                <!-- Child Avatar & Centered Name Header -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-24 h-24 rounded-full p-1 bg-gradient-to-b from-[#FDECEC] to-[#EBF3FE] shadow-xs">
                        <img
                            :src="childProfile.photo_url"
                            :alt="childProfile.name"
                            class="w-full h-full rounded-full object-cover bg-white"
                        />
                    </div>
                    <h2 class="text-lg font-extrabold text-slate-800 mt-3">
                        {{ childProfile.name }}
                    </h2>
                    <p class="text-xs font-medium text-slate-500 mt-0.5">
                        {{ childProfile.group_name }}
                    </p>

                    <!-- Enrollment Approval Status Badge -->
                    <div class="mt-2">
                        <span
                            :class="[
                                'inline-flex items-center px-3 py-0.5 rounded-full text-[11px] font-extrabold',
                                childProfile.enrollment_status === 'pending'
                                    ? 'bg-amber-100 text-amber-800 border border-amber-300'
                                    : childProfile.enrollment_status === 'rejected'
                                      ? 'bg-rose-100 text-rose-800 border border-rose-300'
                                      : 'bg-[#EBF8F0] text-[#1E8E5A] border border-[#CBEAD7]',
                            ]"
                        >
                            {{ childProfile.status_label }}
                        </span>
                    </div>

                    <!-- Child Switcher Pills if parent has multiple children -->
                    <div v-if="children.length > 1" class="flex flex-wrap items-center justify-center gap-1.5 mt-3">
                        <button
                            v-for="c in children"
                            :key="c.id"
                            type="button"
                            @click="switchChild(c.id)"
                            :class="[
                                'px-3 py-1 rounded-full text-xs font-bold transition cursor-pointer',
                                c.id === childProfile.id
                                    ? 'bg-[#2E6FBE] text-white'
                                    : 'bg-slate-100 text-slate-600 hover:bg-slate-200',
                            ]"
                        >
                            {{ c.name }}
                        </button>
                    </div>
                </div>

                <!-- Admin Quick Approval Action if viewing a Pending Child -->
                <div
                    v-if="childProfile.enrollment_status === 'pending' && user?.is_admin"
                    class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 flex items-center justify-between gap-2"
                >
                    <div class="text-xs text-amber-900">
                        <p class="font-bold">Menunggu Persetujuan Kelas</p>
                        <p class="text-[11px]">Setujui pendaftaran {{ childProfile.name }} ke {{ childProfile.group_name }}?</p>
                    </div>
                    <button
                        type="button"
                        @click="approveCurrentChild"
                        class="px-3 py-2 rounded-xl bg-[#34A853] text-white text-xs font-bold shrink-0 cursor-pointer"
                    >
                        ✓ Setujui
                    </button>
                </div>

                <!-- Child Details Table Card -->
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs divide-y divide-slate-100">
                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="7" r="4" />
                                <path stroke-linecap="round" d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                            </svg>
                            <span>Jenis Kelamin</span>
                        </div>
                        <span class="font-bold text-slate-800">
                            {{ childProfile.gender ? (childProfile.gender.toLowerCase() === 'laki-laki' ? '👦 Laki-laki' : '👧 Perempuan') : '-' }}
                        </span>
                    </div>

                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                            </svg>
                            <span>Tanggal Lahir</span>
                        </div>
                        <span class="font-bold text-slate-800">{{ childProfile.birth_date_formatted }}</span>
                    </div>

                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <circle cx="9" cy="7" r="3" />
                                <path stroke-linecap="round" d="M3 19c1-3 3.2-4.5 6-4.5s5 1.5 6 4.5M16 11a2.5 2.5 0 100-5M21 19c-.6-2-2-3.3-4-3.8" />
                            </svg>
                            <span>Kelompok</span>
                        </div>
                        <span class="font-bold text-slate-800">{{ childProfile.short_group }}</span>
                    </div>

                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Alamat Rumah</span>
                        </div>
                        <span class="font-bold text-slate-800 text-right max-w-[200px] truncate" :title="childProfile.address || childProfile.parent?.address || '-'">
                            {{ childProfile.address || childProfile.parent?.address || '-' }}
                        </span>
                    </div>

                    <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="4" y="4" width="16" height="16" rx="2" />
                                <path stroke-linecap="round" d="M8 9h8M8 13h5" />
                            </svg>
                            <span>Mulai Bergabung</span>
                        </div>
                        <span class="font-bold text-slate-800">{{ childProfile.join_date_formatted }}</span>
                    </div>
                </div>

                <!-- Data Orang Tua Section -->
                <section>
                    <h3 class="text-sm font-extrabold text-slate-800 mb-2.5">
                        Data Orang Tua
                    </h3>

                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs divide-y divide-slate-100">
                        <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="3.5" />
                                    <path stroke-linecap="round" d="M5 19c1.5-3 4-4.5 7-4.5s5.5 1.5 7 4.5" />
                                </svg>
                                <span>Nama</span>
                            </div>
                            <span class="font-bold text-slate-800">{{ childProfile.parent?.name }}</span>
                        </div>

                        <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>Nomor HP</span>
                            </div>
                            <span class="font-bold text-slate-800">{{ childProfile.parent?.phone }}</span>
                        </div>

                        <div class="p-3.5 flex items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="5" width="18" height="14" rx="2" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7l9 6 9-6" />
                                </svg>
                                <span>Email</span>
                            </div>
                            <span class="font-bold text-slate-800">{{ childProfile.parent?.email }}</span>
                        </div>

                        <div v-if="childProfile.parent?.address" class="p-3.5 flex items-start justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2.5 text-slate-500 font-medium shrink-0">
                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <circle cx="12" cy="11" r="3" />
                                </svg>
                                <span>Alamat</span>
                            </div>
                            <span class="font-semibold text-slate-700 text-right">{{ childProfile.parent?.address }}</span>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Jika Tidak Ada Profil Anak (Admin / Guru tanpa anak terhubung) -->
            <div v-else class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 text-center space-y-2">
                <div class="w-10 h-10 rounded-full bg-blue-100 text-[#2E6FBE] mx-auto flex items-center justify-center font-bold text-base">
                    ℹ️
                </div>
                <h3 class="text-xs font-bold text-slate-800">Tidak Ada Murid Tertaut</h3>
                <p class="text-[11px] text-slate-500 max-w-xs mx-auto">
                    {{ user?.is_admin ? 'Sebagai Admin, Anda dapat mendaftarkan murid baru, mengelola kelompok kelas, dan guru pengajar.' : 'Akun pengajar aktif. Anda dapat memantau presensi dan aktivitas kelas melalui menu Kelas dan Presensi.' }}
                </p>
            </div>

            <!-- Action: Enroll Another Child (Parent / Admin) + Logout Button -->
            <div class="pt-1 space-y-2.5">
                <button
                    v-if="user?.is_parent || user?.is_admin"
                    type="button"
                    @click="showEnrollChildModal = true"
                    class="w-full py-3 rounded-2xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 transition cursor-pointer"
                >
                    + Daftarkan Anak Baru
                </button>

                <button
                    type="button"
                    @click="logout"
                    class="w-full py-3 rounded-2xl border border-rose-200 bg-rose-50/60 text-rose-700 text-xs font-bold hover:bg-rose-100/70 transition cursor-pointer"
                >
                    Keluar dari Akun ({{ user?.name }})
                </button>
            </div>
        </div>

        <!-- Modal Ubah Akun & Kata Sandi (Semua Role: Admin, Guru, Orang Tua) -->
        <div
            v-if="showAccountModal"
            @click.self="showAccountModal = false"
            @keydown.esc="showAccountModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="account-modal-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl my-8 space-y-3.5 text-xs">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <div>
                        <h2 id="account-modal-title" class="text-sm font-extrabold text-slate-800">Edit Akun &amp; Kata Sandi</h2>
                        <p class="text-[11px] text-slate-500">Perbarui email login dan kata sandi Anda</p>
                    </div>
                    <button type="button" @click="showAccountModal = false" class="w-7 h-7 rounded-full text-slate-400 hover:text-slate-600 flex items-center justify-center">✕</button>
                </div>

                <form @submit.prevent="submitAccountUpdate" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Lengkap</label>
                        <input
                            v-model="accountForm.name"
                            type="text"
                            required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE]"
                        />
                        <p v-if="accountForm.errors.name" class="text-rose-500 text-[11px] mt-1">{{ accountForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Email Login</label>
                        <input
                            v-model="accountForm.email"
                            type="email"
                            required
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE]"
                        />
                        <p v-if="accountForm.errors.email" class="text-rose-500 text-[11px] mt-1">{{ accountForm.errors.email }}</p>
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nomor HP / WhatsApp</label>
                        <input
                            v-model="accountForm.phone"
                            type="text"
                            placeholder="Contoh: 081234567890"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE]"
                        />
                        <p v-if="accountForm.errors.phone" class="text-rose-500 text-[11px] mt-1">{{ accountForm.errors.phone }}</p>
                    </div>

                    <div class="pt-2 border-t border-slate-100">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80 mb-2.5">
                            <p class="text-[11px] text-slate-600 font-medium">
                                Kosongkan kolom kata sandi di bawah jika Anda hanya ingin memperbarui nama, email, atau nomor HP.
                            </p>
                        </div>

                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-600">Keamanan Kata Sandi</span>
                                <button
                                    type="button"
                                    @click="showAccountPassword = !showAccountPassword"
                                    class="text-[11px] font-bold text-[#2E6FBE] hover:underline cursor-pointer"
                                >
                                    {{ showAccountPassword ? 'Sembunyikan Sandi' : 'Lihat Sandi' }}
                                </button>
                            </div>
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Kata Sandi Saat Ini</label>
                                <input
                                    v-model="accountForm.current_password"
                                    :type="showAccountPassword ? 'text' : 'password'"
                                    placeholder="Wajib diisi jika mengubah sandi"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE]"
                                />
                                <p v-if="accountForm.errors.current_password" class="text-rose-500 text-[11px] mt-1">{{ accountForm.errors.current_password }}</p>
                            </div>

                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Kata Sandi Baru</label>
                                <input
                                    v-model="accountForm.password"
                                    :type="showAccountPassword ? 'text' : 'password'"
                                    placeholder="Minimal 6 karakter"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE]"
                                />
                                <p v-if="accountForm.errors.password" class="text-rose-500 text-[11px] mt-1">{{ accountForm.errors.password }}</p>
                            </div>

                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Konfirmasi Kata Sandi Baru</label>
                                <input
                                    v-model="accountForm.password_confirmation"
                                    :type="showAccountPassword ? 'text' : 'password'"
                                    placeholder="Ulangi kata sandi baru"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-[#2E6FBE]"
                                />
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="showAccountModal = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="accountForm.processing"
                            class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold cursor-pointer disabled:opacity-50"
                        >
                            {{ accountForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Child & Parent Profile Modal -->
        <div
            v-if="showEditModal"
            @click.self="showEditModal = false"
            @keydown.esc="showEditModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="edit-profile-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl my-8 space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="edit-profile-title" class="text-base font-bold text-slate-800">Edit Profil Anak &amp; Orang Tua</h2>
                    <button type="button" @click="showEditModal = false" class="w-8 h-8 rounded-full text-slate-400 cursor-pointer">✕</button>
                </div>

                <form @submit.prevent="submitEdit" class="space-y-2.5">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Anak</label>
                        <input v-model="editForm.name" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Kelompok</label>
                        <select v-model="editForm.group_name" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                            <option v-for="cg in classGroups" :key="cg.id" :value="cg.name">
                                {{ cg.name }}
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Tanggal Lahir</label>
                            <input v-model="editForm.birth_date" type="date" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200" />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Mulai Bergabung</label>
                            <input v-model="editForm.join_date" type="date" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200" />
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Foto Profil Anak (Opsional)</label>
                        <input type="file" accept="image/*" @change="handlePhotoChange" class="w-full text-xs text-slate-600" />
                    </div>
                    <hr class="border-slate-100" />
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Orang Tua</label>
                        <input v-model="editForm.parent_name" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nomor HP</label>
                        <input v-model="editForm.parent_phone" type="text" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Email</label>
                        <input v-model="editForm.parent_email" type="email" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Alamat</label>
                        <input v-model="editForm.parent_address" type="text" class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="editForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Enroll New Child Modal (Parent / Admin) -->
        <div
            v-if="showEnrollChildModal"
            @click.self="showEnrollChildModal = false"
            @keydown.esc="showEnrollChildModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="enroll-child-title"
        >
            <div class="bg-white rounded-2xl max-w-md w-full p-5 shadow-xl space-y-3 text-xs max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h2 id="enroll-child-title" class="text-base font-bold text-slate-800">Daftarkan Anak Baru</h2>
                    <button type="button" @click="showEnrollChildModal = false" class="w-8 h-8 rounded-full text-slate-400 cursor-pointer">✕</button>
                </div>
                <form @submit.prevent="submitEnrollChild" class="space-y-3">
                    <div v-if="user?.is_admin && parentOptions.length">
                        <label class="font-semibold text-slate-700 block mb-1">Orang Tua</label>
                        <select v-model="enrollForm.parent_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                            <option v-for="p in parentOptions" :key="p.id" :value="p.id">
                                {{ p.name }} ({{ p.email }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Anak</label>
                        <input v-model="enrollForm.name" type="text" required placeholder="Nama lengkap / panggilan anak" class="w-full px-3 py-2 rounded-xl border border-slate-200" />
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
                        <label class="font-semibold text-slate-700 block mb-1">Pilih Kelas</label>
                        <select v-model="enrollForm.class_group_id" required class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                            <option v-for="cg in classGroups" :key="cg.id" :value="cg.id">
                                {{ cg.name }} (Sisa kuota: {{ cg.remaining_quota }}/{{ cg.quota }})
                            </option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Tanggal Lahir</label>
                        <input v-model="enrollForm.birth_date" type="date" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200" />
                    </div>

                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Alamat Rumah Tempat Tinggal</label>
                        <textarea
                            v-model="enrollForm.address"
                            rows="2"
                            placeholder="Contoh: Jl. Melati Indah No. 12"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs resize-none"
                        ></textarea>
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
                        <button type="button" @click="showEnrollChildModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600 cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="enrollForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold cursor-pointer">
                            {{ user?.is_admin ? 'Simpan & Setujui' : 'Ajukan Pendaftaran' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>
