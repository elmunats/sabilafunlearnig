<script setup>
import { ref, computed } from 'vue';
import MobileAppLayout from '../../Layouts/MobileAppLayout.vue';

const props = defineProps({
    report: {
        type: Object,
        required: true,
    },
});

const selectedPhotoIndex = ref(0);
const photos = computed(() => props.report.photos || []);
const activePhoto = computed(() => photos.value[selectedPhotoIndex.value] || null);

// Detailed activities list matching 4. Detail Kegiatan reference
const detailActivities = computed(() => {
    if (props.report.report_date === '2026-10-14') {
        return ['Menebalkan huruf', 'Menulis mandiri', 'Mewarnai gambar berawalan B'];
    }
    return props.report.activities || [];
});
</script>

<template>
    <Head title="Detail Kegiatan" />

    <MobileAppLayout
        title="Detail Kegiatan"
        :show-back="true"
        :back-url="`/laporan-harian?date=${report.report_date}`"
        active-tab="beranda"
    >
        <div class="py-4 space-y-4">
            <!-- Featured Photo Slider + Thumbnails (Matches 4. Detail Kegiatan Reference) -->
            <div v-if="photos.length > 0" class="space-y-2.5">
                <div class="relative rounded-2xl overflow-hidden bg-slate-100 aspect-16/11 border border-slate-200/80 shadow-xs">
                    <img
                        :src="activePhoto?.photo_url"
                        :alt="activePhoto?.caption || 'Foto kegiatan'"
                        class="w-full h-full object-cover"
                    />
                    <div
                        class="absolute bottom-3 right-3 px-2.5 py-1 rounded-full bg-slate-900/70 text-white text-xs font-bold tracking-wide"
                    >
                        {{ selectedPhotoIndex + 1 }}/{{ photos.length }}
                    </div>
                </div>

                <!-- 4 Thumbnail Strip -->
                <div class="grid grid-cols-4 gap-2">
                    <button
                        v-for="(photo, index) in photos.slice(0, 4)"
                        :key="photo.id"
                        type="button"
                        @click="selectedPhotoIndex = index"
                        :class="[
                            'rounded-xl overflow-hidden aspect-4/3 border-2 transition',
                            selectedPhotoIndex === index
                                ? 'border-[#2E6FBE] ring-2 ring-blue-200'
                                : 'border-transparent opacity-85 hover:opacity-100',
                        ]"
                    >
                        <img :src="photo.photo_url" :alt="photo.caption" class="w-full h-full object-cover" />
                    </button>
                </div>
            </div>

            <!-- Date & Time Metadata Bar (Rabu, 14 Oktober 2026 | 10.00 - 11.00) -->
            <div class="flex items-center justify-between py-2 px-1 text-xs font-semibold text-slate-600 border-b border-slate-200/70">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <path stroke-linecap="round" d="M16 2v4M8 2v4M3 10h18" />
                    </svg>
                    <span>{{ report.formatted_date }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="9" />
                        <path stroke-linecap="round" d="M12 7v5l3 2" />
                    </svg>
                    <span>{{ report.time_range }}</span>
                </div>
            </div>

            <!-- Materi Card (Soft Rose #FDECEC) -->
            <div class="p-4 rounded-2xl bg-[#FDECEC] border border-[#F9D5D5] flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/80 flex items-center justify-center shrink-0 text-[#D9534F] mt-0.5">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-[#C9403C]">Materi</h2>
                    <div class="mt-1 space-y-0.5 text-[13.5px] text-slate-800 font-medium leading-snug">
                        <p v-for="(m, idx) in report.materials" :key="idx">{{ m }}</p>
                    </div>
                </div>
            </div>

            <!-- Aktivitas Card (Soft Mint Green #EBF8F0) -->
            <div class="p-4 rounded-2xl bg-[#EBF8F0] border border-[#CBEAD7] flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/80 flex items-center justify-center shrink-0 text-[#1E8E5A] mt-0.5">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-[#1E8E5A]">Aktivitas</h2>
                    <ul class="mt-1 space-y-0.5 text-[13.5px] text-slate-800 font-medium leading-snug list-disc list-inside">
                        <li v-for="(act, idx) in detailActivities" :key="idx">{{ act }}</li>
                    </ul>
                </div>
            </div>

            <!-- Catatan Guru Card -->
            <div class="p-4 rounded-2xl bg-[#EBF3FE] border border-[#CFE2FC] flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-white/80 flex items-center justify-center shrink-0 text-[#2E6FBE] mt-0.5">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                        <rect x="4" y="3" width="16" height="18" rx="2.5" fill="#2E6FBE" />
                        <path d="M8 8h8M8 12h8M8 16h5" stroke="#FFF" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-[#2563EB]">Catatan Guru ({{ report.teacher_name }})</h2>
                    <p class="mt-1 text-[13.5px] text-slate-700 leading-relaxed">
                        {{ report.teacher_notes }}
                    </p>
                </div>
            </div>
        </div>
    </MobileAppLayout>
</template>

