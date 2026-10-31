<script setup>
import { ref, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    currentInvoice: {
        type: Object,
        default: null,
    },
    invoices: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const activeChild = computed(() => page.props.auth?.activeChild);

const showAllHistory = ref(false);
const showCreateModal = ref(false);

const displayedInvoices = computed(() => {
    if (showAllHistory.value) return props.invoices;
    return props.invoices.slice(0, 3);
});

const createForm = useForm({
    child_id: activeChild.value?.id || '',
    month_year: '2026-11',
    tuition_amount: 300000,
    extra_amount: 50000,
    status: 'unpaid',
});

function submitCreateInvoice() {
    createForm.child_id = activeChild.value?.id || '';
    createForm.post('/pembayaran', {
        onSuccess: () => {
            showCreateModal.value = false;
        },
    });
}
</script>

<template>
    <Head title="Pembayaran" />

    <MobileAppLayout title="Pembayaran" :show-back="true" back-url="/" active-tab="beranda">
        <template #headerRight>
            <button
                v-if="user?.is_admin"
                type="button"
                @click="showCreateModal = true"
                class="px-2.5 py-1.5 rounded-lg bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 transition"
            >
                + Tagihan
            </button>
        </template>

        <div class="py-4 space-y-5">
            <!-- Current Active Billing Card (Matches 7. Pembayaran Reference: "Tagihan Bulan Ini", "Oktober 2026", "Rp 350.000", "Sudah dibayar") -->
            <Link
                v-if="currentInvoice"
                :href="`/pembayaran/${currentInvoice.id}`"
                class="block p-4 rounded-2xl bg-[#FDECEC] border border-[#F9D5D5] hover:brightness-[0.99] transition"
            >
                <div class="flex items-start justify-between gap-2">
                    <div>
                        <p class="text-[13px] font-extrabold text-slate-800">Tagihan Bulan Ini</p>
                        <p class="text-xs text-slate-600 mt-0.5">{{ currentInvoice.month_label }}</p>
                    </div>
                    <span
                        :class="[
                            'px-3 py-1 rounded-full text-xs font-bold shadow-2xs',
                            currentInvoice.status === 'paid'
                                ? 'bg-[#34A853] text-white'
                                : currentInvoice.status === 'pending'
                                  ? 'bg-amber-500 text-white'
                                  : 'bg-rose-600 text-white',
                        ]"
                    >
                        {{ currentInvoice.status_badge }}
                    </span>
                </div>

                <div class="mt-3 flex items-center justify-between">
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight">
                        {{ currentInvoice.formatted_amount }}
                    </span>
                    <svg class="w-5 h-5 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </Link>

            <!-- Riwayat Pembayaran List -->
            <section>
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-[15px] font-extrabold text-slate-800">
                        Riwayat Pembayaran
                    </h2>
                    <button
                        v-if="invoices.length > 0"
                        type="button"
                        @click="showAllHistory = !showAllHistory"
                        class="text-xs font-bold text-[#2E6FBE] hover:underline"
                    >
                        {{ showAllHistory ? 'Ringkas' : 'Lihat semua' }}
                    </button>
                </div>

                <div v-if="displayedInvoices.length > 0" class="space-y-2.5">
                    <Link
                        v-for="inv in displayedInvoices"
                        :key="inv.id"
                        :href="`/pembayaran/${inv.id}`"
                        class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-2xs flex items-center justify-between gap-3 hover:border-slate-300 transition"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                :class="[
                                    'w-8 h-8 rounded-full flex items-center justify-center shrink-0',
                                    inv.status === 'paid' ? 'bg-[#34A853] text-white' : 'bg-amber-100 text-amber-700',
                                ]"
                            >
                                <svg v-if="inv.status === 'paid'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span v-else class="text-xs font-bold">!</span>
                            </div>

                            <div>
                                <h3 class="text-[13.5px] font-extrabold text-slate-800">
                                    {{ inv.month_label }}
                                </h3>
                                <p class="text-[11.5px] text-slate-500 mt-0.5">
                                    {{ inv.paid_short_label }}
                                </p>
                            </div>
                        </div>

                        <div class="text-right">
                            <p class="text-[13.5px] font-extrabold text-slate-800">
                                {{ inv.formatted_amount }}
                            </p>
                            <span
                                :class="[
                                    'inline-block mt-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold',
                                    inv.status === 'paid'
                                        ? 'bg-[#EBF8F0] text-[#1E8E5A]'
                                        : 'bg-rose-50 text-rose-700',
                                ]"
                            >
                                {{ inv.history_badge }}
                            </span>
                        </div>
                    </Link>
                </div>

                <!-- Empty State (R-27) -->
                <div v-else class="bg-white rounded-2xl border border-slate-200/80 p-6 text-center">
                    <p class="text-sm font-bold text-slate-800">Belum ada tagihan tercatat</p>
                    <p class="text-xs text-slate-500 mt-1">
                        Tagihan SPP bulanan untuk {{ activeChild?.name }} akan tampil di halaman ini.
                    </p>
                </div>
            </section>
        </div>

        <!-- Admin Create Monthly Invoice Modal -->
        <div
            v-if="showCreateModal"
            @click.self="showCreateModal = false"
            @keydown.esc="showCreateModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="create-invoice-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="create-invoice-title" class="text-base font-bold text-slate-800">
                        Buat Tagihan ({{ activeChild?.name }})
                    </h2>
                    <button
                        type="button"
                        @click="showCreateModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"
                        aria-label="Tutup"
                    >
                        ✕
                    </button>
                </div>
                <form @submit.prevent="submitCreateInvoice" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Periode Bulan (YYYY-MM)</label>
                        <input v-model="createForm.month_year" type="month" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Bimbel Calistung (Rp)</label>
                        <input v-model.number="createForm.tuition_amount" type="number" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Kegiatan Tambahan (Rp)</label>
                        <input v-model.number="createForm.extra_amount" type="number" required class="w-full px-3 py-2 rounded-xl border border-slate-200" />
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Status Pembayaran</label>
                        <select v-model="createForm.status" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                            <option value="unpaid">Belum dibayar</option>
                            <option value="paid">Sudah dibayar (Lunas)</option>
                            <option value="pending">Menunggu verifikasi</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showCreateModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="createForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Simpan Tagihan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>

