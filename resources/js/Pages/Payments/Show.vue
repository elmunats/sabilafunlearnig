<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    invoice: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);

const showProofModal = ref(false);
const showUploadProofModal = ref(false);

const proofForm = useForm({
    payment_method: props.invoice.payment_method || 'Transfer Bank',
    proof: null,
});

function handleProofFile(e) {
    proofForm.proof = e.target.files[0] || null;
}

function submitProof() {
    proofForm.post(`/pembayaran/${props.invoice.id}/proof`, {
        forceFormData: true,
        onSuccess: () => {
            showUploadProofModal.value = false;
        },
    });
}

function updateInvoiceStatus(status) {
    router.patch(`/pembayaran/${props.invoice.id}/status`, {
        status,
        payment_method: props.invoice.payment_method || 'Transfer Bank',
    });
}
</script>

<template>
    <Head title="Detail Tagihan" />

    <MobileAppLayout title="Detail Tagihan" :show-back="true" back-url="/pembayaran" active-tab="beranda">
        <div class="py-4 space-y-4">
            <!-- Top Summary Card (Matches 8. Detail Tagihan Reference: "Oktober 2026", "Rp 350.000", "Sudah dibayar") -->
            <div class="p-4 rounded-2xl bg-[#FDECEC] border border-[#F9D5D5] flex items-center justify-between gap-3">
                <div>
                    <p class="text-[13.5px] font-extrabold text-slate-800">
                        {{ invoice.month_label }}
                    </p>
                    <p class="text-xl font-extrabold text-slate-900 mt-1">
                        {{ invoice.formatted_amount }}
                    </p>
                </div>
                <span
                    :class="[
                        'px-3 py-1 rounded-full text-xs font-bold shadow-2xs',
                        invoice.status === 'paid'
                            ? 'bg-[#34A853] text-white'
                            : invoice.status === 'pending'
                              ? 'bg-amber-500 text-white'
                              : 'bg-rose-600 text-white',
                    ]"
                >
                    {{ invoice.status_badge }}
                </span>
            </div>

            <!-- Rincian Breakout Section -->
            <section>
                <h2 class="text-sm font-extrabold text-slate-800 mb-2">Rincian</h2>

                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 space-y-3 shadow-2xs">
                    <div
                        v-for="(item, idx) in invoice.breakdown"
                        :key="idx"
                        class="flex items-center justify-between text-xs font-medium text-slate-700 pb-2.5 border-b border-slate-100"
                    >
                        <span>{{ item.label }}</span>
                        <span class="font-semibold text-slate-800">{{ item.formatted_amount }}</span>
                    </div>

                    <div class="flex items-center justify-between text-sm font-extrabold text-slate-900 pt-0.5">
                        <span>Total</span>
                        <span>{{ invoice.formatted_amount }}</span>
                    </div>
                </div>
            </section>

            <!-- Status Pembayaran Section -->
            <section>
                <h2 class="text-sm font-extrabold text-slate-800 mb-2">Status Pembayaran</h2>

                <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs flex items-start gap-3">
                    <div
                        :class="[
                            'w-8 h-8 rounded-full flex items-center justify-center shrink-0 mt-0.5',
                            invoice.status === 'paid' ? 'bg-[#34A853] text-white' : 'bg-amber-100 text-amber-700',
                        ]"
                    >
                        <svg v-if="invoice.status === 'paid'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span v-else class="text-xs font-bold">!</span>
                    </div>
                    <div>
                        <p
                            :class="[
                                'text-sm font-extrabold',
                                invoice.status === 'paid' ? 'text-[#1E8E5A]' : 'text-amber-700',
                            ]"
                        >
                            {{ invoice.history_badge }}
                        </p>
                        <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">
                            {{ invoice.paid_full_label }}
                        </p>
                    </div>
                </div>
            </section>

            <!-- Lihat Bukti Pembayaran Button (Matches 8. Detail Tagihan Reference) -->
            <div class="pt-1 space-y-2.5">
                <button
                    v-if="invoice.payment_proof_url"
                    type="button"
                    @click="showProofModal = true"
                    class="w-full py-3.5 px-4 rounded-2xl bg-[#EBF3FE] hover:bg-blue-100/80 text-[#2E6FBE] font-extrabold text-[13.5px] flex items-center justify-center gap-2 transition"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Lihat Bukti Pembayaran</span>
                </button>

                <button
                    v-if="invoice.status !== 'paid'"
                    type="button"
                    @click="showUploadProofModal = true"
                    class="w-full py-3.5 px-4 rounded-2xl bg-[#2E6FBE] text-white font-bold text-xs flex items-center justify-center gap-2"
                >
                    <span>Unggah Bukti Transfer Pembayaran</span>
                </button>

                <!-- Admin Quick Status Confirmation Controls -->
                <div
                    v-if="user?.is_admin"
                    class="p-3.5 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-between gap-2"
                >
                    <span class="text-xs font-bold text-slate-700">Kontrol Admin:</span>
                    <div class="flex items-center gap-1.5">
                        <button
                            type="button"
                            @click="updateInvoiceStatus('paid')"
                            class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white text-xs font-bold"
                        >
                            Set Lunas
                        </button>
                        <button
                            type="button"
                            @click="updateInvoiceStatus('unpaid')"
                            class="px-3 py-1.5 rounded-xl bg-rose-600 text-white text-xs font-bold"
                        >
                            Set Belum Lunas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Signed-URL Payment Proof Viewer Modal -->
        <div
            v-if="showProofModal"
            @click.self="showProofModal = false"
            @keydown.esc="showProofModal = false"
            class="fixed inset-0 z-50 bg-slate-900/75 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="proof-modal-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl">
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <h3 id="proof-modal-title" class="text-sm font-extrabold text-slate-800">
                        Bukti Pembayaran ({{ invoice.month_label }})
                    </h3>
                    <button
                        type="button"
                        @click="showProofModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-100"
                        aria-label="Tutup"
                    >
                        ✕
                    </button>
                </div>
                <div class="p-4 bg-slate-50 flex items-center justify-center">
                    <img
                        :src="invoice.payment_proof_url"
                        alt="Bukti Pembayaran"
                        class="max-h-[440px] w-auto rounded-xl shadow-xs border border-slate-200"
                    />
                </div>
                <div class="px-4 py-3 bg-white border-t border-slate-100 flex justify-between items-center">
                    <span class="text-[11px] text-slate-500">Dilindungi Temporary Signed URL</span>
                    <button
                        type="button"
                        @click="showProofModal = false"
                        class="px-4 py-1.5 rounded-xl bg-[#2E6FBE] text-white text-xs font-bold"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- Upload Payment Proof Modal -->
        <div
            v-if="showUploadProofModal"
            @click.self="showUploadProofModal = false"
            @keydown.esc="showUploadProofModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="upload-proof-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h3 id="upload-proof-title" class="text-base font-bold text-slate-800">Konfirmasi Pembayaran</h3>
                    <button type="button" @click="showUploadProofModal = false" class="w-8 h-8 rounded-full text-slate-400">✕</button>
                </div>
                <form @submit.prevent="submitProof" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Metode Pembayaran</label>
                        <select v-model="proofForm.payment_method" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white">
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="QRIS Sabila">QRIS Sabila</option>
                            <option value="Tunai di Kelas">Tunai di Kelas</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Berkas Bukti Transfer</label>
                        <input
                            type="file"
                            accept="image/*,.pdf"
                            @change="handleProofFile"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:bg-[#EBF3FE] file:text-[#2E6FBE] file:font-bold"
                        />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showUploadProofModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="proofForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Kirim Bukti
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>

