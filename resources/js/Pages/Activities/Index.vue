<script setup>
import { ref, computed } from 'vue';
import { router, useForm, usePage } from '@inertiajs/vue3';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => ['Semua', 'Membaca', 'Menulis', 'Berhitung', 'Mengaji', 'Calistung', 'Pracalis', 'Kegiatan Lain'],
    },
    selectedCategory: {
        type: String,
        default: 'Semua',
    },
    photoGroups: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user);
const activeChild = computed(() => page.props.auth?.activeChild);

const showUploadModal = ref(false);
const activePreviewPhoto = ref(null);

const uploadForm = useForm({
    child_id: activeChild.value?.id || '',
    report_date: '2026-10-14',
    category: 'Calistung',
    caption: '',
    taken_time: '10.15',
    photo: null,
    preset_illust: 'seed/writing_b_main.svg',
});

function selectCategory(cat) {
    router.get('/foto-kegiatan', { category: cat }, { preserveState: true, preserveScroll: true });
}

function handleFileChange(e) {
    uploadForm.photo = e.target.files[0] || null;
}

function submitUpload() {
    uploadForm.child_id = activeChild.value?.id || '';
    uploadForm.post('/foto-kegiatan', {
        forceFormData: true,
        onSuccess: () => {
            showUploadModal.value = false;
            uploadForm.reset('caption', 'photo');
        },
    });
}
</script>

<template>
    <Head title="Foto Kegiatan" />

    <MobileAppLayout title="Foto Kegiatan" :show-back="true" back-url="/" active-tab="beranda">
        <template #headerRight>
            <button
                v-if="user?.can_manage_reports"
                type="button"
                @click="showUploadModal = true"
                class="px-2.5 py-1.5 rounded-lg bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold hover:bg-blue-100 transition"
            >
                + Unggah
            </button>
        </template>

        <div class="py-4 space-y-4">
            <!-- Horizontally Scrollable Filter Tags: [Semua], [Calistung], [Pracalis], [Kegiatan Lain] -->
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1">
                <button
                    v-for="cat in categories"
                    :key="cat"
                    type="button"
                    @click="selectCategory(cat)"
                    :class="[
                        'px-4 py-2 rounded-full text-xs font-bold whitespace-nowrap transition shrink-0 min-h-[36px]',
                        selectedCategory === cat
                            ? 'bg-[#3B82F6] text-white shadow-2xs'
                            : 'bg-[#F1F5FB] text-slate-600 hover:bg-slate-200/70',
                    ]"
                >
                    {{ cat }}
                </button>
            </div>

            <!-- Grouped Activity Photos by Month (Matches 6. Foto Kegiatan Reference) -->
            <div v-if="photoGroups.length > 0" class="space-y-5">
                <section v-for="group in photoGroups" :key="group.month_title">
                    <h2 class="text-sm font-extrabold text-slate-800 mb-2.5">
                        {{ group.month_title }}
                    </h2>

                    <!-- First 4 photos in 2-col grid, remaining in 3-col grid just like Screen 6 Reference -->
                    <div class="grid grid-cols-2 gap-2.5">
                        <button
                            v-for="photo in group.items.slice(0, 4)"
                            :key="photo.id"
                            type="button"
                            @click="activePreviewPhoto = photo"
                            class="group relative rounded-2xl overflow-hidden bg-slate-100 aspect-4/3 border border-slate-200/80 text-left focus:ring-2 focus:ring-[#2E6FBE]"
                        >
                            <img
                                :src="photo.photo_url"
                                :alt="photo.caption"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                            />
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-900/75 via-slate-900/30 to-transparent p-2.5">
                                <p class="text-[11px] font-bold text-white truncate">{{ photo.caption }}</p>
                            </div>
                        </button>
                    </div>

                    <div v-if="group.items.length > 4" class="grid grid-cols-3 gap-2 mt-2.5">
                        <button
                            v-for="photo in group.items.slice(4)"
                            :key="photo.id"
                            type="button"
                            @click="activePreviewPhoto = photo"
                            class="group relative rounded-xl overflow-hidden bg-slate-100 aspect-square border border-slate-200/80 text-left"
                        >
                            <img
                                :src="photo.photo_url"
                                :alt="photo.caption"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-200"
                            />
                        </button>
                    </div>
                </section>
            </div>

            <!-- Empty State (R-27) -->
            <div v-else class="bg-white rounded-2xl border border-slate-200/80 p-6 text-center space-y-2">
                <p class="text-sm font-bold text-slate-800">
                    Belum ada foto untuk kategori "{{ selectedCategory }}"
                </p>
                <p class="text-xs text-slate-500">
                    Pilih kategori "Semua" untuk melihat seluruh dokumentasi kegiatan belajar {{ activeChild?.name }}.
                </p>
                <button
                    type="button"
                    @click="selectCategory('Semua')"
                    class="mt-2 px-4 py-2 rounded-xl bg-[#EBF3FE] text-[#2E6FBE] text-xs font-bold"
                >
                    Tampilkan Semua Foto
                </button>
            </div>
        </div>

        <!-- Photo Lightbox Modal -->
        <div
            v-if="activePreviewPhoto"
            @click.self="activePreviewPhoto = null"
            @keydown.esc="activePreviewPhoto = null"
            class="fixed inset-0 z-50 bg-slate-900/75 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-label="Detail Foto Kegiatan"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full overflow-hidden shadow-2xl">
                <div class="aspect-16/11 bg-slate-100 relative">
                    <img :src="activePreviewPhoto.photo_url" :alt="activePreviewPhoto.caption" class="w-full h-full object-cover" />
                    <button
                        type="button"
                        @click="activePreviewPhoto = null"
                        class="absolute top-3 right-3 w-8 h-8 rounded-full bg-slate-900/60 text-white flex items-center justify-center text-sm"
                        aria-label="Tutup pratinjau"
                    >
                        ✕
                    </button>
                </div>
                <div class="p-4 space-y-2">
                    <span class="inline-block px-2.5 py-0.5 rounded-full bg-[#EBF3FE] text-[#2E6FBE] text-[11px] font-bold">
                        {{ activePreviewPhoto.category }}
                    </span>
                    <h3 class="text-sm font-extrabold text-slate-800">{{ activePreviewPhoto.caption }}</h3>
                    <p class="text-xs text-slate-500">
                        {{ activePreviewPhoto.report_date }} • Pukul {{ activePreviewPhoto.taken_time }}
                    </p>
                    <div class="pt-2 flex justify-between items-center">
                        <Link
                            :href="`/laporan-harian/${activePreviewPhoto.daily_report_id}`"
                            class="text-xs font-bold text-[#2E6FBE] hover:underline"
                        >
                            Buka Laporan Harian Terkait →
                        </Link>
                        <button
                            type="button"
                            @click="activePreviewPhoto = null"
                            class="px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teacher / Admin Upload Activity Photo Modal -->
        <div
            v-if="showUploadModal"
            @click.self="showUploadModal = false"
            @keydown.esc="showUploadModal = false"
            class="fixed inset-0 z-50 bg-slate-900/50 flex items-center justify-center p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="upload-photo-title"
        >
            <div class="bg-white rounded-2xl max-w-sm w-full p-5 shadow-xl space-y-3 text-xs">
                <div class="flex items-center justify-between">
                    <h2 id="upload-photo-title" class="text-base font-bold text-slate-800">
                        Unggah Foto Kegiatan ({{ activeChild?.name }})
                    </h2>
                    <button
                        type="button"
                        @click="showUploadModal = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100"
                        aria-label="Tutup"
                    >
                        ✕
                    </button>
                </div>

                <form @submit.prevent="submitUpload" class="space-y-3">
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Judul / Keterangan Kegiatan</label>
                        <input
                            v-model="uploadForm.caption"
                            type="text"
                            required
                            placeholder="Contoh: Latihan menulis suku kata"
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-sm"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Kategori</label>
                            <select v-model="uploadForm.category" class="w-full px-2.5 py-2 rounded-xl border border-slate-200 bg-white">
                                <option value="Membaca">Membaca</option>
                                <option value="Menulis">Menulis</option>
                                <option value="Berhitung">Berhitung</option>
                                <option value="Mengaji">Mengaji</option>
                                <option value="Calistung">Calistung</option>
                                <option value="Pracalis">Pracalis</option>
                                <option value="Kegiatan Lain">Kegiatan Lain</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-semibold text-slate-700 block mb-1">Tanggal</label>
                            <input v-model="uploadForm.report_date" type="date" required class="w-full px-2.5 py-2 rounded-xl border border-slate-200" />
                        </div>
                    </div>
                    <div>
                        <label class="font-semibold text-slate-700 block mb-1">Pilih Berkas Foto (Bucket Privat / Signed URL)</label>
                        <input
                            type="file"
                            accept="image/*"
                            @change="handleFileChange"
                            class="w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:bg-[#EBF3FE] file:text-[#2E6FBE] file:font-bold"
                        />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showUploadModal = false" class="px-4 py-2 rounded-xl border border-slate-200 font-semibold text-slate-600">
                            Batal
                        </button>
                        <button type="submit" :disabled="uploadForm.processing" class="px-4 py-2 rounded-xl bg-[#2E6FBE] text-white font-bold">
                            Unggah Foto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </MobileAppLayout>
</template>

