<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

const showPassword = ref(false);
const showForgotModal = ref(false);
const showRegisterModal = ref(false);

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
    password: '',
    child_name: '',
    child_birth_date: '2022-05-10',
    child_group: 'Kelompok Calistung A',
});

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
                        Portal Laporan Harian, Perkembangan Bulanan, Dokumentasi Kelas, dan SPP Bimbel Calistung &amp; Pracalis.
                    </p>
                </div>

                <!-- Right / Form Column -->
                <div class="md:col-span-7 p-6 sm:p-8 flex flex-col justify-center">
                    <div>
                        <div class="flex items-center justify-center md:justify-start mb-4">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight">
                                Masuk ke Akun
                            </h1>
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
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl my-8">
                <div class="flex items-center justify-between mb-3">
                    <h2 id="register-modal-title" class="text-base font-bold text-slate-800">Daftar Akun Orang Tua</h2>
                    <button
                        type="button"
                        @click="showRegisterModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"
                        aria-label="Tutup dialog"
                    >
                        ✕
                    </button>
                </div>
                <form @submit.prevent="submitRegister" class="space-y-2.5 text-xs">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Orang Tua (Bunda/Ayah)</label>
                        <input
                            v-model="registerForm.name"
                            type="text"
                            required
                            placeholder="Nama lengkap orang tua"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"
                        />
                        <p v-if="registerForm.errors.name" class="text-rose-600 mt-0.5">{{ registerForm.errors.name }}</p>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Email</label>
                        <input
                            v-model="registerForm.email"
                            type="email"
                            required
                            placeholder="email@example.com"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"
                        />
                        <p v-if="registerForm.errors.email" class="text-rose-600 mt-0.5">{{ registerForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nomor HP / WhatsApp</label>
                        <input
                            v-model="registerForm.phone"
                            type="text"
                            required
                            placeholder="0812 3456 7890"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"
                        />
                        <p v-if="registerForm.errors.phone" class="text-rose-600 mt-0.5">{{ registerForm.errors.phone }}</p>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Kata Sandi</label>
                        <input
                            v-model="registerForm.password"
                            type="password"
                            required
                            minlength="6"
                            placeholder="Minimal 6 karakter"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"
                        />
                    </div>
                    <hr class="my-2 border-slate-100" />
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Nama Anak</label>
                        <input
                            v-model="registerForm.child_name"
                            type="text"
                            required
                            placeholder="Nama panggilan / lengkap anak"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Tanggal Lahir</label>
                            <input
                                v-model="registerForm.child_birth_date"
                                type="date"
                                required
                                class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs"
                            />
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Kelompok</label>
                            <select
                                v-model="registerForm.child_group"
                                class="w-full px-2.5 py-2 rounded-xl border border-slate-200 text-xs bg-white"
                            >
                                <option value="Kelompok Calistung A">Calistung A</option>
                                <option value="Kelompok Calistung B">Calistung B</option>
                                <option value="Kelompok Pracalis A">Pracalis A</option>
                                <option value="Kelompok Pracalis B">Pracalis B</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button
                            type="button"
                            @click="showRegisterModal = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 font-semibold text-slate-600"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="registerForm.processing"
                            class="px-4 py-2.5 rounded-xl bg-[#2E6FBE] text-white font-bold"
                        >
                            Daftar Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
