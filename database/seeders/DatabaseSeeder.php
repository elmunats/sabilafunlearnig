<?php

namespace Database\Seeders;

use App\Models\ActivityPhoto;
use App\Models\AppNotification;
use App\Models\Child;
use App\Models\ClassGroup;
use App\Models\DailyReport;
use App\Models\Invoice;
use App\Models\MonthlyReport;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPrivateMediaAssets();

        // 1. Spatie Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);
        $parentRole = Role::firstOrCreate(['name' => 'parent']);

        // 2. Only Admin User (Clean Database for Production)
        $admin = User::updateOrCreate(
            ['email' => 'admin@sabilafun.id'],
            [
                'name' => 'Admin Sabila',
                'phone' => '0811 0000 1111',
                'role' => 'admin',
                'address' => 'Kantor Pusat Sabila Fun Learning, Jl. Pendidikan No. 8, Jakarta',
                'password' => 'password123',
            ]
        );
        $admin->syncRoles([$adminRole]);

        // 3. Default Class Groups
        $defaultClasses = [
            ['name' => 'Kelompok Calistung A', 'category' => 'Calistung', 'quota' => 8, 'schedule' => 'Senin - Jumat, 10.00 - 11.00'],
            ['name' => 'Kelompok Calistung B', 'category' => 'Calistung', 'quota' => 8, 'schedule' => 'Senin - Jumat, 11.15 - 12.15'],
            ['name' => 'Kelompok Pracalis', 'category' => 'Pracalis', 'quota' => 8, 'schedule' => 'Senin - Jumat, 08.30 - 09.30'],
            ['name' => 'Kelompok Mengaji', 'category' => 'Mengaji', 'quota' => 10, 'schedule' => 'Senin - Jumat, 15.30 - 16.30'],
        ];
        foreach ($defaultClasses as $cls) {
            ClassGroup::firstOrCreate(['name' => $cls['name']], $cls);
        }
    }

    private function seedPrivateMediaAssets(): void
    {
        $disk = Storage::disk('local');

        // Child Avatars
        $disk->put('avatars/aisyah.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
  <defs>
    <linearGradient id="bgA" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#FDECEC"/>
      <stop offset="100%" stop-color="#FCE7F3"/>
    </linearGradient>
  </defs>
  <rect width="200" height="200" rx="100" fill="url(#bgA)"/>
  <circle cx="100" cy="110" r="52" fill="#3B2314"/>
  <path d="M52 165 C55 132 145 132 148 165 Z" fill="#F472B6"/>
  <circle cx="100" cy="96" r="38" fill="#FCD5B5"/>
  <path d="M60 88 C65 55 135 55 140 88 C125 72 75 72 60 88 Z" fill="#2D1B10"/>
  <circle cx="64" cy="76" r="8" fill="#F43F5E"/>
  <circle cx="136" cy="76" r="8" fill="#F43F5E"/>
  <circle cx="86" cy="96" r="4.5" fill="#1E293B"/>
  <circle cx="114" cy="96" r="4.5" fill="#1E293B"/>
  <circle cx="78" cy="105" r="6" fill="#FB7185" opacity="0.45"/>
  <circle cx="122" cy="105" r="6" fill="#FB7185" opacity="0.45"/>
  <path d="M90 110 Q100 119 110 110" stroke="#9F1239" stroke-width="3" fill="none" stroke-linecap="round"/>
</svg>
SVG);

        $disk->put('avatars/syifa.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
  <rect width="200" height="200" rx="100" fill="#EBF3FE"/>
  <circle cx="100" cy="105" r="50" fill="#2D1B10"/>
  <path d="M54 168 C58 135 142 135 146 168 Z" fill="#38BDF8"/>
  <circle cx="100" cy="96" r="36" fill="#FCD5B5"/>
  <path d="M63 86 C68 56 132 56 137 86 C120 72 80 72 63 86 Z" fill="#2D1B10"/>
  <circle cx="87" cy="95" r="4.2" fill="#1E293B"/>
  <circle cx="113" cy="95" r="4.2" fill="#1E293B"/>
  <path d="M91 109 Q100 117 109 109" stroke="#9F1239" stroke-width="3" fill="none" stroke-linecap="round"/>
</svg>
SVG);

        $disk->put('avatars/rafa.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">
  <rect width="200" height="200" rx="100" fill="#EBF8F0"/>
  <path d="M54 168 C58 135 142 135 146 168 Z" fill="#34D399"/>
  <circle cx="100" cy="95" r="37" fill="#FCD5B5"/>
  <path d="M64 82 C68 50 132 50 136 82 Z" fill="#1E293B"/>
  <circle cx="87" cy="95" r="4.2" fill="#1E293B"/>
  <circle cx="113" cy="95" r="4.2" fill="#1E293B"/>
  <path d="M90 109 Q100 116 110 109" stroke="#1E293B" stroke-width="3" fill="none" stroke-linecap="round"/>
</svg>
SVG);

        // Classroom Activity Illustrations (Matching Reference Photos)
        $disk->put('seed/writing_b_main.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 420">
  <rect width="640" height="420" fill="#FDF6EC"/>
  <rect x="0" y="0" width="640" height="170" fill="#F3E8D8"/>
  <rect x="40" y="20" width="160" height="95" rx="10" fill="#EBF8F0" stroke="#CBEAD7" stroke-width="4"/>
  <text x="72" y="78" font-family="sans-serif" font-size="44" font-weight="bold" fill="#2E6FBE">B b</text>
  <text x="145" y="74" font-family="sans-serif" font-size="22" font-weight="bold" fill="#D9534F">ba bi bu</text>
  <!-- Desk -->
  <rect x="0" y="255" width="640" height="165" fill="#E6B980"/>
  <rect x="0" y="255" width="640" height="12" fill="#D4A366"/>
  <!-- Child learning & writing -->
  <path d="M205 265 C215 190 425 190 435 265 Z" fill="#F472B6"/>
  <circle cx="320" cy="145" r="64" fill="#3B2314"/>
  <circle cx="320" cy="155" r="52" fill="#FCD5B5"/>
  <path d="M266 138 C275 95 365 95 374 138 C350 120 290 120 266 138 Z" fill="#2D1B10"/>
  <!-- Eyes looking down at worksheet -->
  <path d="M294 156 Q303 163 312 156" stroke="#1E293B" stroke-width="3.5" fill="none" stroke-linecap="round"/>
  <path d="M328 156 Q337 163 346 156" stroke="#1E293B" stroke-width="3.5" fill="none" stroke-linecap="round"/>
  <path d="M310 176 Q320 183 330 176" stroke="#9F1239" stroke-width="3" fill="none" stroke-linecap="round"/>
  <!-- Worksheet on desk -->
  <polygon points="150,275 490,275 530,395 110,395" fill="#FFFFFF" stroke="#CBD5E1" stroke-width="2"/>
  <line x1="165" y1="305" x2="475" y2="305" stroke="#E2E8F0" stroke-width="2"/>
  <line x1="155" y1="335" x2="485" y2="335" stroke="#E2E8F0" stroke-width="2"/>
  <line x1="145" y1="365" x2="495" y2="365" stroke="#E2E8F0" stroke-width="2"/>
  <text x="190" y="330" font-family="sans-serif" font-size="28" font-weight="bold" fill="#2E6FBE">B   B   ba   bi   bu</text>
  <text x="185" y="362" font-family="sans-serif" font-size="26" font-weight="bold" fill="#94A3B8">B   B   ba   bi   bu</text>
  <!-- Hand & Pencil -->
  <line x1="365" y1="210" x2="325" y2="315" stroke="#1E293B" stroke-width="8" stroke-linecap="round"/>
  <circle cx="342" cy="272" r="18" fill="#FCD5B5"/>
</svg>
SVG);

        $disk->put('seed/blocks_play.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 420">
  <rect width="640" height="420" fill="#F0F7FF"/>
  <rect x="0" y="270" width="640" height="150" fill="#E9C496"/>
  <circle cx="320" cy="145" r="52" fill="#FCD5B5"/>
  <path d="M266 130 C275 85 365 85 374 130 Z" fill="#2D1B10"/>
  <path d="M225 270 C235 200 405 200 415 270 Z" fill="#60A5FA"/>
  <!-- Colorful blocks -->
  <rect x="170" y="285" width="56" height="56" rx="8" fill="#EF4444"/>
  <rect x="234" y="285" width="56" height="56" rx="8" fill="#F59E0B"/>
  <rect x="298" y="285" width="56" height="56" rx="8" fill="#10B981"/>
  <rect x="362" y="285" width="56" height="56" rx="8" fill="#3B82F6"/>
  <rect x="202" y="229" width="56" height="56" rx="8" fill="#8B5CF6"/>
  <rect x="266" y="229" width="56" height="56" rx="8" fill="#EC4899"/>
  <text x="188" y="322" font-family="sans-serif" font-size="26" font-weight="bold" fill="#FFF">A</text>
  <text x="252" y="322" font-family="sans-serif" font-size="26" font-weight="bold" fill="#FFF">B</text>
  <text x="316" y="322" font-family="sans-serif" font-size="26" font-weight="bold" fill="#FFF">C</text>
  <text x="380" y="322" font-family="sans-serif" font-size="26" font-weight="bold" fill="#FFF">1</text>
</svg>
SVG);

        $disk->put('seed/group_counting.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 420">
  <rect width="640" height="420" fill="#FEF9EC"/>
  <rect x="0" y="275" width="640" height="145" fill="#E4B882"/>
  <!-- Three kids learning together -->
  <circle cx="180" cy="165" r="42" fill="#FCD5B5"/>
  <path d="M125 275 C135 215 225 215 235 275 Z" fill="#34D399"/>
  <circle cx="320" cy="155" r="45" fill="#FCD5B5"/>
  <path d="M260 275 C270 210 370 210 380 275 Z" fill="#F472B6"/>
  <circle cx="460" cy="165" r="42" fill="#FCD5B5"/>
  <path d="M405 275 C415 215 505 215 515 275 Z" fill="#60A5FA"/>
  <rect x="160" y="295" width="320" height="80" rx="12" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="2"/>
  <text x="220" y="346" font-family="sans-serif" font-size="34" font-weight="bold" fill="#D97706">1  2  3  4  5</text>
</svg>
SVG);

        $disk->put('seed/letter_b_card.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 420">
  <rect width="640" height="420" fill="#FFF5F5"/>
  <circle cx="320" cy="150" r="54" fill="#FCD5B5"/>
  <path d="M264 132 C275 88 365 88 376 132 Z" fill="#2D1B10"/>
  <path d="M220 380 C235 245 405 245 420 380 Z" fill="#FB7185"/>
  <!-- Holding Big Letter B Card -->
  <rect x="240" y="205" width="160" height="175" rx="18" fill="#FFFFFF" stroke="#FDA4AF" stroke-width="4"/>
  <text x="278" y="330" font-family="sans-serif" font-size="115" font-weight="bold" fill="#E11D48">B</text>
</svg>
SVG);

        $disk->put('seed/coloring_book.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 420">
  <rect width="640" height="420" fill="#F0FDF4"/>
  <rect x="0" y="250" width="640" height="170" fill="#E6B980"/>
  <rect x="150" y="70" width="340" height="230" rx="12" fill="#FFFFFF" stroke="#BBF7D0" stroke-width="4"/>
  <circle cx="260" cy="165" r="45" fill="#FDE047" stroke="#CA8A04" stroke-width="3"/>
  <path d="M340 220 L390 120 L440 220 Z" fill="#4ADE80" stroke="#16A34A" stroke-width="3"/>
  <text x="210" y="270" font-family="sans-serif" font-size="24" font-weight="bold" fill="#15803D">Bola &amp; Balon</text>
</svg>
SVG);

        $disk->put('seed/reading_book.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 420">
  <rect width="640" height="420" fill="#EFF6FF"/>
  <rect x="0" y="270" width="640" height="150" fill="#D8B48A"/>
  <circle cx="320" cy="140" r="50" fill="#FCD5B5"/>
  <path d="M235 280 C245 205 395 205 405 280 Z" fill="#3B82F6"/>
  <rect x="190" y="220" width="260" height="120" rx="10" fill="#FFFFFF" stroke="#93C5FD" stroke-width="4"/>
  <line x1="320" y1="220" x2="320" y2="340" stroke="#93C5FD" stroke-width="3"/>
  <text x="218" y="275" font-family="sans-serif" font-size="22" font-weight="bold" fill="#1D4ED8">ba - ca</text>
  <text x="348" y="275" font-family="sans-serif" font-size="22" font-weight="bold" fill="#1D4ED8">bu - ku</text>
</svg>
SVG);

        $disk->put('seed/clay_craft.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 420">
  <rect width="640" height="420" fill="#FAF5FF"/>
  <rect x="0" y="265" width="640" height="155" fill="#E6B980"/>
  <circle cx="240" cy="160" r="44" fill="#FCD5B5"/>
  <circle cx="400" cy="160" r="44" fill="#FCD5B5"/>
  <circle cx="220" cy="315" r="26" fill="#F43F5E"/>
  <circle cx="320" cy="315" r="26" fill="#F59E0B"/>
  <circle cx="420" cy="315" r="26" fill="#8B5CF6"/>
</svg>
SVG);

        // Payment Proof Receipt SVG
        $disk->put('seed/payment_proof_okt2026.svg', <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 680">
  <rect width="500" height="680" rx="24" fill="#F8FAFC"/>
  <rect x="28" y="28" width="444" height="624" rx="20" fill="#FFFFFF" stroke="#E2E8F0" stroke-width="2"/>
  <circle cx="250" cy="96" r="34" fill="#DCFCE7"/>
  <path d="M234 96 L246 108 L268 84" stroke="#16A34A" stroke-width="5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
  <text x="250" y="158" text-anchor="middle" font-family="sans-serif" font-size="20" font-weight="bold" fill="#0F172A">Bukti Transfer Berhasil</text>
  <text x="250" y="184" text-anchor="middle" font-family="sans-serif" font-size="14" fill="#64748B">05 Oktober 2026 • 09:15 WIB</text>
  <text x="250" y="236" text-anchor="middle" font-family="sans-serif" font-size="32" font-weight="bold" fill="#2E6FBE">Rp 350.000</text>
  <line x1="60" y1="270" x2="440" y2="270" stroke="#E2E8F0" stroke-width="1.5" stroke-dasharray="6,6"/>
  <text x="64" y="315" font-family="sans-serif" font-size="14" fill="#64748B">Pengirim</text>
  <text x="436" y="315" text-anchor="end" font-family="sans-serif" font-size="14" font-weight="bold" fill="#1E293B">Ibu Siti</text>
  <text x="64" y="355" font-family="sans-serif" font-size="14" fill="#64748B">Penerima</text>
  <text x="436" y="355" text-anchor="end" font-family="sans-serif" font-size="14" font-weight="bold" fill="#1E293B">Yayasan Sabila Fun Learning</text>
  <text x="64" y="395" font-family="sans-serif" font-size="14" fill="#64748B">Metode Pembayaran</text>
  <text x="436" y="395" text-anchor="end" font-family="sans-serif" font-size="14" font-weight="bold" fill="#1E293B">Transfer Bank</text>
  <text x="64" y="435" font-family="sans-serif" font-size="14" fill="#64748B">Siswa / Kelompok</text>
  <text x="436" y="435" text-anchor="end" font-family="sans-serif" font-size="14" font-weight="bold" fill="#1E293B">Aisyah • Calistung A</text>
  <text x="64" y="475" font-family="sans-serif" font-size="14" fill="#64748B">Periode Tagihan</text>
  <text x="436" y="475" text-anchor="end" font-family="sans-serif" font-size="14" font-weight="bold" fill="#1E293B">Oktober 2026</text>
  <text x="64" y="515" font-family="sans-serif" font-size="14" fill="#64748B">No. Referensi</text>
  <text x="436" y="515" text-anchor="end" font-family="sans-serif" font-size="14" font-weight="bold" fill="#1E293B">SFL-20261005-0084</text>
  <rect x="64" y="555" width="372" height="54" rx="12" fill="#F0FDF4" stroke="#BBF7D0"/>
  <text x="250" y="587" text-anchor="middle" font-family="sans-serif" font-size="14" font-weight="bold" fill="#15803D">STATUS: LUNAS TERVERIFIKASI</text>
</svg>
SVG);
    }
}
