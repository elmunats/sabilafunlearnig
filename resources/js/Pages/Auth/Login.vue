<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const props = defineProps({
    availableClasses: {
        type: Array,
        default: () => ['Kelompok Calistung A', 'Kelompok Calistung B', 'Kelompok Pracalis A', 'Kelompok Mengaji Iqra'],
    },
    openRegister: {
        type: Boolean,
        default: false,
    },
    preselectedGroup: {
        type: String,
        default: '',
    },
});

const showPassword = ref(false);
const showRegisterPassword = ref(false);
const showForgotModal = ref(false);
const showRegisterModal = ref(props.openRegister);

const page = usePage();
const flashSuccess = computed(() => page.props.flash?.success);

const loginForm = useForm({
    identifier: '',
    password: '',
    remember: true,
});

const forgotForm = useForm({
    identifier: '',
});

const registerForm = useForm({
    name: '',
    email: '',
    phone: '',
    address: '',
    password: '',
    child_name: '',
    child_birth_date: '2022-05-10',
    child_gender: 'laki-laki',
    child_group: props.preselectedGroup || props.availableClasses[0] || 'Kelompok Calistung A',
    child_photo: null,
});

const childPhotoPreview = ref(null);

function handleChildPhoto(e) {
    const file = e.target.files[0];
    if (file) {
        registerForm.child_photo = file;
        const reader = new FileReader();
        reader.onload = (event) => {
            childPhotoPreview.value = event.target.result;
        };
        reader.readAsDataURL(file);
    } else {
        registerForm.child_photo = null;
        childPhotoPreview.value = null;
    }
}

function submitLogin() {
    loginForm.post('/login');
}

function submitForgot() {
    forgotForm.post('/forgot-password', {
        onSuccess: () => {
            showForgotModal.value = false;
            forgotForm.reset();
        },
    });
}

function submitRegister() {
    registerForm.post('/register', {
        forceFormData: true,
        onSuccess: () => {
            showRegisterModal.value = false;
        },
    });
}
</script>

<template>
    <Head title="Masuk ke Akun" />

    <div class="w-full min-h-dvh bg-gradient-to-b from-[#DCEBFE] via-[#F4F7FB] to-white flex flex-col justify-between">
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-6">
            <div class="w-full max-w-4xl bg-white rounded-3xl shadow-[0_12px_40px_rgba(15,23,42,0.08)] border border-slate-100 overflow-hidden grid grid-cols-1 md:grid-cols-12">
                <!-- Left / Top Brand Logo & Illustration Column -->
                <div class="md:col-span-5 bg-gradient-to-b from-[#DCEBFE] via-[#EBF3FE] to-[#F8FAFC] p-6 sm:p-8 flex flex-col items-center justify-center text-center">
                    <img
                        src="/logo-sabiladunlearning.png"
                        alt="Logo Sabila Fun Learning"
                        class="h-16 sm:h-20 w-auto object-contain mb-3 drop-shadow-2xs"
                    />

                    <div class="w-52 h-36 sm:w-60 sm:h-44 flex items-center justify-center">
                        <svg viewBox="0 0 280 200" class="w-full h-full drop-shadow-sm" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="140" cy="105" r="78" fill="#D3E7FE" opacity="0.65" />
                            <!-- Boy (Left) -->
                            <path d="M58 164 C62 128 118 128 122 164 Z" fill="#60A5FA" />
                            <circle cx="90" cy="100" r="28" fill="#FCD5B5" />
                            <path d="M62 94 C65 65 115 65 118 94 C104 80 76 80 62 94 Z" fill="#4A2C18" />
                            <circle cx="81" cy="100" r="3.2" fill="#1E293B" />
                            <circle cx="99" cy="100" r="3.2" fill="#1E293B" />
                            <circle cx="75" cy="106" r="4.5" fill="#FB7185" opacity="0.4" />
                            <circle cx="105" cy="106" r="4.5" fill="#FB7185" opacity="0.4" />
                            <path d="M84 110 Q90 116 96 110" stroke="#7C2D12" stroke-width="2.5" fill="none" stroke-linecap="round" />

                            <!-- Girl (Right) -->
                            <circle cx="190" cy="104" r="34" fill="#3B2314" />
                            <path d="M158 164 C162 128 218 128 222 164 Z" fill="#F472B6" />
                            <circle cx="190" cy="100" r="28" fill="#FCD5B5" />
                            <path d="M162 92 C166 64 214 64 218 92 C204 78 176 78 162 92 Z" fill="#3B2314" />
                            <circle cx="215" cy="78" r="6" fill="#FB7185" />
                            <circle cx="181" cy="100" r="3.2" fill="#1E293B" />
                            <circle cx="199" cy="100" r="3.2" fill="#1E293B" />
                            <circle cx="175" cy="106" r="4.5" fill="#FB7185" opacity="0.45" />
                            <circle cx="205" cy="106" r="4.5" fill="#FB7185" opacity="0.45" />
                            <path d="M184 110 Q190 116 196 110" stroke="#9F1239" stroke-width="2.5" fill="none" stroke-linecap="round" />

                            <!-- Open Yellow Book -->
                            <path d="M85 162 L140 150 L195 162 L195 178 L140 168 L85 178 Z" fill="#FBBF24" />
                            <path d="M90 156 L140 146 L190 156 L190 172 L140 162 L90 172 Z" fill="#FEF3C7" stroke="#F59E0B" stroke-width="2" />
                            <line x1="140" y1="146" x2="140" y2="164" stroke="#D97706" stroke-width="2" />
                            <circle cx="105" cy="160" r="7" fill="#FCD5B5" />
                            <circle cx="175" cy="160" r="7" fill="#FCD5B5" />
                        </svg>
                    </div>

                    <p class="text-xs text-slate-600 mt-2 max-w-xs leading-relaxed">
                        Portal Laporan Harian, Perkembangan Bulanan, Dokumentasi Kelas, dan SPP Bimbel Membaca, Menulis, Berhitung &amp; Mengaji.
                    </p>
                </div>

                <!-- Right / Form Column -->
                <div class="md:col-span-7 p-6 sm:p-8 flex flex-col justify-center">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">
                                Masuk ke Akun
                            </h1>
                            <Link
                                href="/"
                                class="inline-flex items-center gap-1 text-xs font-bold text-[#2E6FBE] hover:underline py-1.5 px-2.5 rounded-xl bg-[#EBF3FE]/70"
                            >
                                <span>&larr; Halaman Utama</span>
                            </Link>
                        </div>

                        <div
                            v-if="flashSuccess"
                            class="mb-4 px-4 py-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium"
                        >
                            {{ flashSuccess }}
                        </div>

                        <form @submit.prevent="submitLogin" class="space-y-4">
                            <div>
                                <label for="identifier" class="sr-only">Email atau Nomor HP</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-4 text-slate-400 pointer-events-none">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <circle cx="12" cy="8" r="4" />
                                            <path stroke-linecap="round" d="M5 20c1.5-3.5 4.2-5 7-5s5.5 1.5 7 5" />
                                        </svg>
                                    </span>
                                    <input
                                        id="identifier"
                                        v-model="loginForm.identifier"
                                        type="text"
                                        autocomplete="username"
                                        placeholder="Email / Nomor HP"
                                        class="w-full pl-12 pr-4 py-3.5 bg-[#F8FAFC] border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#2E6FBE] transition"
                                        required
                                    />
                                </div>
                                <p v-if="loginForm.errors.identifier" class="mt-1.5 text-xs text-rose-600 font-medium">
                                    {{ loginForm.errors.identifier }}
                                </p>
                            </div>

                            <div>
                                <label for="password" class="sr-only">Kata sandi</label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-4 text-slate-400 pointer-events-none">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <rect x="5" y="11" width="14" height="10" rx="2" />
                                            <path stroke-linecap="round" d="M8 11V7a4 4 0 018 0v4" />
                                        </svg>
                                    </span>
                                    <input
                                        id="password"
                                        v-model="loginForm.password"
                                        :type="showPassword ? 'text' : 'password'"
                                        autocomplete="current-password"
                                        placeholder="Kata sandi"
                                        class="w-full pl-12 pr-12 py-3.5 bg-[#F8FAFC] border border-slate-200 rounded-2xl text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-[#2E6FBE] transition"
                                        required
                                    />
                                    <button
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        class="absolute right-3.5 p-1.5 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer"
                                        :aria-label="showPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                    >
                                        <svg v-if="!showPassword" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.5 10.677a2 2 0 002.823 2.823M7.362 7.561C5.68 8.74 4.279 10.42 3 12c1.889 2.991 5.282 6 9 6 1.55 0 3.043-.523 4.395-1.353m1.929-1.976C19.521 13.588 20.43 12.29 21 12c-1.889-2.991-5.282-6-9-6-.89 0-1.765.146-2.6.412" />
                                        </svg>
                                        <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-1">
                                <button
                                    type="submit"
                                    :disabled="loginForm.processing"
                                    class="w-full py-3.5 rounded-full bg-[#2E6FBE] hover:bg-[#245ca3] active:scale-[0.99] text-white font-bold text-[15px] shadow-sm transition disabled:opacity-60 cursor-pointer"
                                >
                                    {{ loginForm.processing ? 'Memproses...' : 'Masuk' }}
                                </button>
                            </div>

                            <div class="text-center">
                                <button
                                    type="button"
                                    @click="showForgotModal = true"
                                    class="text-xs font-semibold text-[#2E6FBE] hover:underline py-1 px-2 cursor-pointer"
                                >
                                    Lupa kata sandi?
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="pt-5 mt-6 text-center border-t border-slate-100">
                        <p class="text-xs text-slate-600">
                            Belum punya akun?
                            <button
                                type="button"
                                @click="showRegisterModal = true"
                                class="font-bold text-[#2E6FBE] hover:underline ml-0.5 cursor-pointer"
                            >
                                Daftar
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Forgot Password Modal -->
        <div
            v-if="showForgotModal"
            @keydown.esc="showForgotModal = false"
            @click.self="showForgotModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="forgot-modal-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl">
                <div class="flex items-center justify-between mb-3">
                    <h2 id="forgot-modal-title" class="text-base font-bold text-slate-800">Pemulihan Kata Sandi</h2>
                    <button
                        type="button"
                        @click="showForgotModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"
                        aria-label="Tutup dialog"
                    >
                        ✕
                    </button>
                </div>
                <p class="text-xs text-slate-600 mb-3">
                    Masukkan Email atau Nomor HP yang terdaftar untuk menerima instruksi pengaturan ulang kata sandi.
                </p>
                <form @submit.prevent="submitForgot" class="space-y-3">
                    <input
                        v-model="forgotForm.identifier"
                        type="text"
                        required
                        placeholder="Contoh: siti@example.com / 0812..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm"
                    />
                    <div class="flex justify-end gap-2">
                        <button
                            type="button"
                            @click="showForgotModal = false"
                            class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="forgotForm.processing"
                            class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white text-xs font-bold"
                        >
                            Kirim Tautan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Register Parent + Child Modal -->
        <div
            v-if="showRegisterModal"
            @keydown.esc="showRegisterModal = false"
            @click.self="showRegisterModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4 overflow-y-auto"
            role="dialog"
            aria-modal="true"
            aria-labelledby="register-modal-title"
        >
            <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-xl my-6 max-h-[92vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div>
                        <h2 id="register-modal-title" class="text-base sm:text-lg font-bold text-slate-800">
                            Pendaftaran Murid Baru
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">Lengkapi data orang tua dan calon murid</p>
                    </div>
                    <button
                        type="button"
                        @click="showRegisterModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 cursor-pointer"
                        aria-label="Tutup dialog"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitRegister" class="space-y-4 text-xs">
                    <!-- SECTION 1: DATA ORANG TUA -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                            <span class="w-5 h-5 rounded-full bg-[#EBF3FE] text-[#2E6FBE] font-bold text-[11px] flex items-center justify-center">1</span>
                            <span class="font-bold text-slate-800 text-xs">Data Orang Tua / Wali</span>
                        </div>

                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Nama Orang Tua (Bunda / Ayah) <span class="text-rose-500">*</span></label>
                            <input
                                v-model="registerForm.name"
                                type="text"
                                required
                                placeholder="Nama lengkap orang tua"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE]"
                            />
                            <p v-if="registerForm.errors.name" class="text-rose-600 mt-0.5">{{ registerForm.errors.name }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Email <span class="text-rose-500">*</span></label>
                                <input
                                    v-model="registerForm.email"
                                    type="email"
                                    required
                                    placeholder="email@example.com"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE]"
                                />
                                <p v-if="registerForm.errors.email" class="text-rose-600 mt-0.5">{{ registerForm.errors.email }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Nomor WhatsApp <span class="text-rose-500">*</span></label>
                                <input
                                    v-model="registerForm.phone"
                                    type="text"
                                    required
                                    placeholder="0812 3456 7890"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE]"
                                />
                                <p v-if="registerForm.errors.phone" class="text-rose-600 mt-0.5">{{ registerForm.errors.phone }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Alamat Rumah Tempat Tinggal <span class="text-rose-500">*</span></label>
                            <textarea
                                v-model="registerForm.address"
                                required
                                rows="2"
                                placeholder="Contoh: Jl. Melati Indah No. 12, RT 02 / RW 05, Kel. Sukamaju"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE] resize-none"
                            ></textarea>
                            <p v-if="registerForm.errors.address" class="text-rose-600 mt-0.5">{{ registerForm.errors.address }}</p>
                        </div>

                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Kata Sandi Akun <span class="text-rose-500">*</span></label>
                            <div class="relative flex items-center">
                                <input
                                    v-model="registerForm.password"
                                    :type="showRegisterPassword ? 'text' : 'password'"
                                    required
                                    minlength="6"
                                    placeholder="Minimal 6 karakter"
                                    class="w-full pl-3 pr-10 py-2 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE]"
                                />
                                <button
                                    type="button"
                                    @click="showRegisterPassword = !showRegisterPassword"
                                    class="absolute right-2.5 p-1 text-slate-400 hover:text-slate-600 rounded-lg cursor-pointer"
                                    :aria-label="showRegisterPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
                                >
                                    <svg v-if="!showRegisterPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3l18 18M10.5 10.677a2 2 0 002.823 2.823M7.362 7.561C5.68 8.74 4.279 10.42 3 12c1.889 2.991 5.282 6 9 6 1.55 0 3.043-.523 4.395-1.353m1.929-1.976C19.521 13.588 20.43 12.29 21 12c-1.889-2.991-5.282-6-9-6-.89 0-1.765.146-2.6.412" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="registerForm.errors.password" class="text-rose-600 mt-0.5">{{ registerForm.errors.password }}</p>
                        </div>
                    </div>

                    <!-- SECTION 2: DATA CALON MURID -->
                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-2 pb-1 border-b border-slate-100">
                            <span class="w-5 h-5 rounded-full bg-[#FFF3E0] text-[#D97706] font-bold text-[11px] flex items-center justify-center">2</span>
                            <span class="font-bold text-slate-800 text-xs">Data Calon Murid</span>
                        </div>

                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Nama Calon Murid <span class="text-rose-500">*</span></label>
                            <input
                                v-model="registerForm.child_name"
                                type="text"
                                required
                                placeholder="Nama lengkap / panggilan anak"
                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm focus:border-[#2E6FBE]"
                            />
                            <p v-if="registerForm.errors.child_name" class="text-rose-600 mt-0.5">{{ registerForm.errors.child_name }}</p>
                        </div>

                        <!-- Jenis Kelamin Anak (Laki-laki / Perempuan) -->
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1.5">Jenis Kelamin Anak <span class="text-rose-500">*</span></label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    :class="[
                                        'flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 cursor-pointer transition text-xs font-bold',
                                        registerForm.child_gender === 'laki-laki'
                                            ? 'border-[#0077C2] bg-[#F0F7FF] text-[#005E9C]'
                                            : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        value="laki-laki"
                                        v-model="registerForm.child_gender"
                                        class="sr-only"
                                    />
                                    <span>👦 Laki-laki</span>
                                </label>
                                <label
                                    :class="[
                                        'flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl border-2 cursor-pointer transition text-xs font-bold',
                                        registerForm.child_gender === 'perempuan'
                                            ? 'border-[#EC4899] bg-[#FDF2F8] text-[#BE185D]'
                                            : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300'
                                    ]"
                                >
                                    <input
                                        type="radio"
                                        value="perempuan"
                                        v-model="registerForm.child_gender"
                                        class="sr-only"
                                    />
                                    <span>👧 Perempuan</span>
                                </label>
                            </div>
                            <p v-if="registerForm.errors.child_gender" class="text-rose-600 mt-0.5">{{ registerForm.errors.child_gender }}</p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
                                <input
                                    v-model="registerForm.child_birth_date"
                                    type="date"
                                    required
                                    class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs focus:border-[#2E6FBE]"
                                />
                                <p v-if="registerForm.errors.child_birth_date" class="text-rose-600 mt-0.5">{{ registerForm.errors.child_birth_date }}</p>
                            </div>
                            <div>
                                <label class="font-semibold text-slate-700 block mb-1">Pilihan Kelompok <span class="text-rose-500">*</span></label>
                                <select
                                    v-model="registerForm.child_group"
                                    class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs bg-white focus:border-[#2E6FBE]"
                                >
                                    <option v-for="cls in availableClasses" :key="cls" :value="cls">
                                        {{ cls }}
                                    </option>
                                </select>
                                <p v-if="registerForm.errors.child_group" class="text-rose-600 mt-0.5">{{ registerForm.errors.child_group }}</p>
                            </div>
                        </div>

                        <!-- Upload Foto Anak -->
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Foto Calon Murid (Opsional)</label>
                            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/60">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-white border border-slate-200 shrink-0 flex items-center justify-center">
                                    <img
                                        v-if="childPhotoPreview"
                                        :src="childPhotoPreview"
                                        alt="Pratinjau foto anak"
                                        class="w-full h-full object-cover"
                                    />
                                    <span v-else class="text-lg">
                                        {{ registerForm.child_gender === 'laki-laki' ? '👦' : '👧' }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <input
                                        type="file"
                                        accept="image/jpeg,image/png,image/webp,image/svg+xml"
                                        @change="handleChildPhoto"
                                        class="w-full text-xs text-slate-600 file:mr-2.5 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:bg-[#EBF3FE] file:text-[#0077C2] file:font-bold file:text-xs cursor-pointer"
                                    />
                                    <p class="text-[10px] text-slate-500 mt-0.5">Format: JPG, PNG, atau WebP (maks. 4MB)</p>
                                </div>
                            </div>
                            <p v-if="registerForm.errors.child_photo" class="text-rose-600 mt-0.5">{{ registerForm.errors.child_photo }}</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showRegisterModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 font-semibold text-slate-600 hover:bg-slate-50 cursor-pointer"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="registerForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-[#F28C18] hover:bg-[#D97706] text-slate-950 font-extrabold cursor-pointer transition"
                        >
                            {{ registerForm.processing ? 'Memproses...' : 'Daftar Sekarang' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
