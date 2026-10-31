# DESIGN.md: Sabila Fun Learning Brand & UI Direction

## Design Read
Reading this as: Mobile-first Early Childhood Education (Calistung & Pracalis) Parent/Teacher Portal & PWA for Indonesian parents ("Bunda/Ayah") and teachers ("Guru"), in a warm tactile classroom-journal style, dial `ENERGY 2 / RHYTHM 2 / MOTION 1`.

## 1. Identity & Mood
- **Product**: Sabila Fun Learning (Bimbel Calistung & Pracalis)
- **Audience**: Parents monitoring daily/monthly child progress, class activities, and tuition billing; Teachers recording daily/monthly learning reports and uploading class photos; Admins managing children, teachers, and invoices.
- **Personality**: Warm, reassuring, trustworthy, playful yet structured, accessible on mobile screens (`max-w-md` centered mobile app shell on desktop with optional wider admin/teacher workspace, and native-feeling bottom navigation bar).

## 2. Color Palette (R-01, R-25, R-29)
Derived directly from the provided UI specification and visual reference:
- **Neutral Canvas**: `#F8FAFC` / `#F4F6FB` soft warm-cool background with `#FFFFFF` elevated cards.
- **Primary Brand Blue**: `#2E6FBE` / `#3B82F6` (Active navigation states, primary CTAs, selected date/filter pills, progress bars). Meets WCAG AA contrast with white text (`#2563EB` / `#1D4ED8` for text on light backgrounds).
- **Semantic Pastel Surface Tokens (Hierarchy & Section Differentiation)**:
  - **Soft Rose / Coral Tint (`#FDECEC` / border `#F9D5D5`, icon `#D9534F`)**: Used for *Materi* (Learning Materials), *Tagihan Bulan Ini* highlight card, and *Aisyah* child selector card.
  - **Soft Mint Green (`#EBF8F0` / border `#CBEAD7`, icon `#1E8E5A`, badge `#34A853`)**: Used for *Aktivitas* (Daily Activities), *Membaca* progress indicator, and `Sudah dibayar` / `Lunas` payment badges.
  - **Soft Warm Amber / Yellow (`#FEF6E0` / border `#F9E4B7`, icon `#B7791F`)**: Used for *Perilaku* (Behavior observation), *Berhitung* (`123`) indicator, and *Pembayaran* quick action card.
  - **Soft Sky / Periwinkle Blue (`#EBF3FE` / border `#CFE2FC`, icon `#2563EB`)**: Used for *Catatan Guru* (Teacher Notes), *Laporan Harian* & *Foto Kegiatan* quick actions, and *Menulis* indicator.
  - **Soft Lavender (`#F3EEFD` / border `#E2D4F8`, icon `#6D28D9`)**: Used for *Laporan Bulanan* quick action and *Sikap Belajar* indicator.

## 3. Typography (R-06)
- **Typeface**: `Plus Jakarta Sans` / `Nunito` (rounded, warm humanist sans-serif suited for early childhood education in Indonesia, high legibility at small mobile sizes).
- **Hierarchy**:
  - App Bar Title: `17px` (`text-base`/`text-lg`) bold `#1E293B` centered with back arrow on subpages.
  - Section Headers: `15px` (`text-[15px]`) bold `#1E293B`.
  - Body / Report Lists: `13.5px`–`14px` regular/medium `#334155` with comfortable `1.5` line height.
  - Timestamps & Metadata: `12px` `#475569` (ensuring >= 4.5:1 contrast ratio against card backgrounds).

## 4. Layout & Component Architecture (R-03, R-05, R-11, R-12)
- **Mobile-First PWA Shell**: Centered `max-w-[430px]` mobile container on desktop (with role switcher / quick role toolbar at the top for demo & Teacher/Admin management drawers) and sticky 5-item Bottom Navigation Bar (`Beranda`, `Laporan`, `Kegiatan`, `Pembayaran`, `Profil`).
- **Border Radius**: `rounded-2xl` (`16px`) for content cards, `rounded-full` reserved strictly for filter chips, status badges, and the primary Login CTA button as shown in the reference design.
- **Shadows**: Subtle elevation (`shadow-sm` with `border-slate-100`) on white cards; pastel summary cards sit flat inside the white sheet with subtle 1px tinted borders.

## 5. Purpose-Gate Decisions Log (R-31)
- **Why Plus Jakarta Sans?** Warm, friendly Indonesian-designed geometric humanist typeface that keeps parent-teacher communication approachable and clear on mobile screens.
- **Why Pastel Semantic Cards (Rose, Mint, Amber, Sky)?** Mirrors the physical classroom observation rubric (Materi, Aktivitas, Perilaku, Catatan Guru) from the reference mockup so parents can scan daily progress in under 5 seconds.
- **Why Custom SVG Icons instead of default Lucide thin strokes?** Duotone/filled rounded educational icons (book, pencil, 123 block, smile face, wallet, bell) match the reference design's warm, tactile early-childhood aesthetic.
- **Why Role Switcher & Teacher/Admin Action Modals?** Enables full verification of RBAC (Admin, Teacher, Parent) and CRUD workflows (creating reports, uploading photos, confirming invoices) directly in the UI without dead controls (R-26, R-35).

