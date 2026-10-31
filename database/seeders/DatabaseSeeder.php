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

        // 2. Users (Admin, Teachers, Parents)
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

        $teacher = User::updateOrCreate(
            ['email' => 'guru@sabilafun.id'],
            [
                'name' => 'Bu Guru Nisa',
                'phone' => '0811 2233 4455',
                'role' => 'teacher',
                'address' => 'Jl. Cempaka Putih No. 19, Jakarta Pusat',
                'password' => 'password123',
            ]
        );
        $teacher->syncRoles([$teacherRole]);

        $teacherTari = User::updateOrCreate(
            ['email' => 'tari@sabilafun.id'],
            [
                'name' => 'Bu Guru Tari',
                'phone' => '0811 6677 8899',
                'role' => 'teacher',
                'address' => 'Jl. Kemang Raya No. 14, Jakarta Selatan',
                'password' => 'password123',
            ]
        );
        $teacherTari->syncRoles([$teacherRole]);

        $parentSiti = User::updateOrCreate(
            ['email' => 'siti@example.com'],
            [
                'name' => 'Ibu Siti',
                'phone' => '0812 3456 7890',
                'role' => 'parent',
                'address' => 'Jl. Taman Melati Blok C4 No. 15, Tangerang Selatan',
                'password' => 'password123',
            ]
        );
        $parentSiti->syncRoles([$parentRole]);

        $parentRina = User::updateOrCreate(
            ['email' => 'rina@example.com'],
            [
                'name' => 'Ibu Rina',
                'phone' => '0813 9876 5432',
                'role' => 'parent',
                'address' => 'Jl. Anggrek Lestari No. 7, Depok',
                'password' => 'password123',
            ]
        );
        $parentRina->syncRoles([$parentRole]);

        // 2b. Class Groups with Quotas & Teacher Mapping (Bu Guru Nisa teaches 2 classes)
        $calistungA = ClassGroup::updateOrCreate(
            ['name' => 'Kelompok Calistung A'],
            [
                'category' => 'Calistung',
                'quota' => 10,
                'schedule' => 'Senin - Jumat, 10.00 - 11.00',
            ]
        );

        $calistungB = ClassGroup::updateOrCreate(
            ['name' => 'Kelompok Calistung B'],
            [
                'category' => 'Calistung',
                'quota' => 10,
                'schedule' => 'Senin - Jumat, 11.00 - 12.00',
            ]
        );

        $pracalisA = ClassGroup::updateOrCreate(
            ['name' => 'Kelompok Pracalis A'],
            [
                'category' => 'Pracalis',
                'quota' => 8,
                'schedule' => 'Senin - Jumat, 08.30 - 09.30',
            ]
        );

        $pracalisB = ClassGroup::updateOrCreate(
            ['name' => 'Kelompok Pracalis B'],
            [
                'category' => 'Pracalis',
                'quota' => 8,
                'schedule' => 'Senin - Jumat, 08.30 - 09.30',
            ]
        );

        // Map Teachers to Classes (Bu Guru Nisa teaches both Calistung A and Pracalis B)
        $calistungA->teachers()->sync([$teacher->id]);
        $pracalisB->teachers()->sync([$teacher->id]);
        $calistungB->teachers()->sync([$teacherTari->id]);
        $pracalisA->teachers()->sync([$teacherTari->id]);

        // 3. Children for Ibu Siti (Aisyah & Syifa as shown in Screen 2 Beranda)
        $aisyah = Child::updateOrCreate(
            ['parent_id' => $parentSiti->id, 'name' => 'Aisyah'],
            [
                'class_group_id' => $calistungA->id,
                'photo' => 'avatars/aisyah.svg',
                'birth_date' => '2022-03-12',
                'group_name' => $calistungA->name,
                'enrollment_status' => 'approved',
                'approved_at' => Carbon::parse('2026-07-01 08:00:00'),
                'join_date' => '2026-07-01',
            ]
        );

        $syifa = Child::updateOrCreate(
            ['parent_id' => $parentSiti->id, 'name' => 'Syifa'],
            [
                'class_group_id' => $pracalisB->id,
                'photo' => 'avatars/syifa.svg',
                'birth_date' => '2023-05-18',
                'group_name' => $pracalisB->name,
                'enrollment_status' => 'approved',
                'approved_at' => Carbon::parse('2026-08-01 08:00:00'),
                'join_date' => '2026-08-01',
            ]
        );

        // Child for Ibu Rina (to verify strict Row-Level Authorization + Pending Approval sample)
        $rafa = Child::updateOrCreate(
            ['parent_id' => $parentRina->id, 'name' => 'Rafa'],
            [
                'class_group_id' => $calistungB->id,
                'photo' => 'avatars/rafa.svg',
                'birth_date' => '2022-01-20',
                'group_name' => $calistungB->name,
                'enrollment_status' => 'approved',
                'approved_at' => Carbon::parse('2026-07-01 08:00:00'),
                'join_date' => '2026-07-01',
            ]
        );

        Child::updateOrCreate(
            ['parent_id' => $parentRina->id, 'name' => 'Zaki'],
            [
                'class_group_id' => $calistungA->id,
                'photo' => 'avatars/rafa.svg',
                'birth_date' => '2022-09-14',
                'group_name' => $calistungA->name,
                'enrollment_status' => 'pending',
                'approved_at' => null,
                'join_date' => '2026-10-29',
            ]
        );

        // 4. Daily Reports for Aisyah (Okt 2026: includes Hadir, Izin, and Sakit records)
        $weeklyReports = [
            '2026-10-08' => [
                'attendance_status' => 'izin',
                'absence_reason' => 'Mengikuti acara keluarga di luar kota',
                'time_range' => 'Pengajuan Izin',
                'materials' => ['Siswa Izin (Mengikuti acara keluarga di luar kota)'],
                'activities' => ['Tidak mengikuti kegiatan kelas karena izin'],
                'behavior' => ['Status Kehadiran' => 'Izin', 'Keterangan' => 'Mengikuti acara keluarga di luar kota'],
                'teacher_notes' => 'Pengajuan Izin oleh Orang Tua (Ibu Siti): Mengikuti acara keluarga di luar kota',
                'photos' => [],
            ],
            '2026-10-09' => [
                'attendance_status' => 'sakit',
                'absence_reason' => 'Demam ringan dan istirahat di rumah',
                'time_range' => 'Pengajuan Sakit',
                'materials' => ['Siswa Sakit (Demam ringan dan istirahat di rumah)'],
                'activities' => ['Tidak mengikuti kegiatan kelas karena sakit'],
                'behavior' => ['Status Kehadiran' => 'Sakit', 'Keterangan' => 'Demam ringan dan istirahat di rumah'],
                'teacher_notes' => 'Pengajuan Sakit oleh Orang Tua (Ibu Siti): Demam ringan dan istirahat di rumah',
                'photos' => [],
            ],
            '2026-10-12' => [
                'time_range' => '09.00 - 10.00',
                'materials' => ['Mengenal huruf vokal A, I, U, E, O', 'Membaca kartu suku kata terbuka'],
                'activities' => ['Bernyanyi lagu alfabet bersama', 'Menyusun balok huruf vokal', 'Mencocokkan gambar benda'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Sangat baik', 'Kemandirian' => 'Baik'],
                'teacher_notes' => 'Aisyah sangat bersemangat menyusun balok huruf vokal dan mampu menyebutkan bunyi huruf dengan tepat.',
                'photos' => [
                    ['category' => 'Calistung', 'path' => 'seed/blocks_play.svg', 'caption' => 'Menyusun balok warna dan huruf', 'time' => '09.30'],
                ],
            ],
            '2026-10-13' => [
                'time_range' => '10.00 - 11.00',
                'materials' => ['Mengenal angka 1 sampai 10', 'Menghitung jumlah benda di kelas'],
                'activities' => ['Menghitung kancing warna-warni', 'Menebalkan garis angka 1-5', 'Permainan kelompok berhitung'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Baik', 'Kemandirian' => 'Baik'],
                'teacher_notes' => 'Aisyah sudah lancar menghitung benda konkret 1 sampai 10 tanpa terlewat.',
                'photos' => [
                    ['category' => 'Calistung', 'path' => 'seed/group_counting.svg', 'caption' => 'Belajar berhitung bersama teman', 'time' => '10.15'],
                ],
            ],
            '2026-10-14' => [
                'time_range' => '10.00 - 11.00',
                'materials' => ['Mengenal huruf B', 'Menulis suku kata ba, bi, bu'],
                'activities' => ['Latihan menulis', 'Permainan huruf', 'Mewarnai gambar', 'Menebalkan huruf', 'Menulis mandiri'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Baik', 'Kemandirian' => 'Mulai berkembang'],
                'teacher_notes' => 'Aisyah sudah mulai dapat menulis huruf B dengan bantuan. Masih perlu latihan konsistensi bentuk huruf.',
                'photos' => [
                    ['category' => 'Calistung', 'path' => 'seed/writing_b_main.svg', 'caption' => 'Latihan menulis huruf', 'time' => '10.00'],
                    ['category' => 'Calistung', 'path' => 'seed/letter_b_card.svg', 'caption' => 'Mengenal kartu huruf B', 'time' => '10.15'],
                    ['category' => 'Pracalis', 'path' => 'seed/coloring_book.svg', 'caption' => 'Mewarnai gambar berawalan B', 'time' => '10.30'],
                    ['category' => 'Kegiatan Lain', 'path' => 'seed/reading_book.svg', 'caption' => 'Membaca buku cerita bergambar', 'time' => '10.45'],
                    ['category' => 'Kegiatan Lain', 'path' => 'seed/clay_craft.svg', 'caption' => 'Kreasi motorik halus di kelas', 'time' => '10.55'],
                ],
            ],
            '2026-10-15' => [
                'time_range' => '10.00 - 11.00',
                'materials' => ['Mengenal huruf C dan D', 'Membaca suku kata ca, ci, cu'],
                'activities' => ['Menebalkan huruf C di buku kotak', 'Permainan tebak kartu kata', 'Mewarnai gambar ceri'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Baik', 'Kemandirian' => 'Baik'],
                'teacher_notes' => 'Genggaman pensil Aisyah semakin stabil saat menebalkan lengkungan huruf C.',
                'photos' => [],
            ],
            '2026-10-16' => [
                'time_range' => '09.30 - 10.30',
                'materials' => ['Evaluasi ringan suku kata ba-bi-bu dan ca-ci-cu', 'Senam jari motorik halus'],
                'activities' => ['Membaca nyaring bergantian', 'Menempel stiker bintang pada kata yang tepat'],
                'behavior' => ['Fokus' => 'Sangat baik', 'Antusias' => 'Sangat baik', 'Kemandirian' => 'Baik'],
                'teacher_notes' => 'Perkembangan minggu ini sangat positif. Aisyah berani tampil membaca di depan teman-teman.',
                'photos' => [],
            ],
        ];

        foreach ($weeklyReports as $dateStr => $data) {
            $report = DailyReport::updateOrCreate(
                ['child_id' => $aisyah->id, 'report_date' => $dateStr],
                [
                    'teacher_id' => $teacher->id,
                    'attendance_status' => $data['attendance_status'] ?? 'hadir',
                    'absence_reason' => $data['absence_reason'] ?? null,
                    'time_range' => $data['time_range'],
                    'materials' => $data['materials'],
                    'activities' => $data['activities'],
                    'behavior' => $data['behavior'],
                    'teacher_notes' => $data['teacher_notes'],
                ]
            );

            $report->activityPhotos()->delete();
            foreach ($data['photos'] as $photoData) {
                ActivityPhoto::create([
                    'daily_report_id' => $report->id,
                    'category' => $photoData['category'],
                    'photo_path' => $photoData['path'],
                    'caption' => $photoData['caption'],
                    'taken_time' => $photoData['time'],
                ]);
            }
        }

        // Daily Report for Syifa (2nd child of Ibu Siti)
        $syifaReport = DailyReport::updateOrCreate(
            ['child_id' => $syifa->id, 'report_date' => '2026-10-14'],
            [
                'teacher_id' => $teacher->id,
                'time_range' => '08.30 - 09.30',
                'materials' => ['Latihan motorik halus menarik garis lurus', 'Mengenal warna dasar dan bentuk geometri'],
                'activities' => ['Menarik garis putus-putus', 'Menyusun puzzle bentuk lingkaran dan segitiga', 'Bernyanyi lagu warna'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Sangat baik', 'Kemandirian' => 'Mulai berkembang'],
                'teacher_notes' => 'Syifa sangat ceria saat menyusun puzzle warna dan mulai terbiasa memegang krayon dengan tiga jari.',
            ]
        );
        $syifaReport->activityPhotos()->delete();
        ActivityPhoto::create([
            'daily_report_id' => $syifaReport->id,
            'category' => 'Pracalis',
            'photo_path' => 'seed/blocks_play.svg',
            'caption' => 'Bermain balok warna Pracalis',
            'taken_time' => '09.00',
        ]);

        // Daily Report for Rafa (Ibu Rina's child)
        DailyReport::updateOrCreate(
            ['child_id' => $rafa->id, 'report_date' => '2026-10-14'],
            [
                'teacher_id' => $teacher->id,
                'time_range' => '11.00 - 12.00',
                'materials' => ['Membaca kalimat sederhana'],
                'activities' => ['Latihan membaca buku jilid 2'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Baik', 'Kemandirian' => 'Baik'],
                'teacher_notes' => 'Rafa lancar membaca kalimat pendek.',
            ]
        );

        // 5. Monthly Reports for Aisyah & Syifa (Screen 5: Laporan Bulanan)
        MonthlyReport::updateOrCreate(
            ['child_id' => $aisyah->id, 'month_year' => '2026-10'],
            [
                'reading_score' => 78,
                'writing_score' => 74,
                'counting_score' => 82,
                'attitude_score' => 88,
                'notes' => [
                    'reading' => 'Mulai mengenal huruf vokal dan konsonan.',
                    'writing' => 'Menulis huruf dengan lebih rapi dan konsisten.',
                    'counting' => 'Mengenal angka 1-10 dan menghitung benda.',
                    'attitude' => 'Lebih fokus dan aktif dalam kegiatan.',
                ],
            ]
        );

        MonthlyReport::updateOrCreate(
            ['child_id' => $aisyah->id, 'month_year' => '2026-09'],
            [
                'reading_score' => 68,
                'writing_score' => 65,
                'counting_score' => 75,
                'attitude_score' => 82,
                'notes' => [
                    'reading' => 'Mengenal bunyi huruf vokal A, I, U, E, O melalui kartu bergambar.',
                    'writing' => 'Belajar menebalkan garis lengkung dan lurus dengan bantuan guru.',
                    'counting' => 'Mengenal angka 1-5 menggunakan alat peraga balok.',
                    'attitude' => 'Mudah beradaptasi dan senang berbagi alat tulis dengan teman.',
                ],
            ]
        );

        MonthlyReport::updateOrCreate(
            ['child_id' => $syifa->id, 'month_year' => '2026-10'],
            [
                'reading_score' => 70,
                'writing_score' => 68,
                'counting_score' => 76,
                'attitude_score' => 90,
                'notes' => [
                    'reading' => 'Senang menyimak cerita bergambar dan menyebutkan nama objek.',
                    'writing' => 'Koordinasi tangan dan mata berkembang baik saat menebalkan pola.',
                    'counting' => 'Mampu mengelompokkan benda berdasarkan ukuran dan jumlah 1-5.',
                    'attitude' => 'Sangat ceria, antusias, dan patuh pada instruksi guru di kelas.',
                ],
            ]
        );

        // 6. Invoices for Aisyah & Syifa (Screen 7: Pembayaran & Screen 8: Detail Tagihan)
        $invoiceMonths = [
            ['ym' => '2026-10', 'paid_at' => '2026-10-05 09:15:00'],
            ['ym' => '2026-09', 'paid_at' => '2026-09-05 10:20:00'],
            ['ym' => '2026-08', 'paid_at' => '2026-08-05 14:05:00'],
        ];

        foreach ($invoiceMonths as $inv) {
            Invoice::updateOrCreate(
                ['child_id' => $aisyah->id, 'month_year' => $inv['ym']],
                [
                    'amount' => 350000,
                    'breakdown' => [
                        ['label' => 'Bimbel Calistung', 'amount' => 300000],
                        ['label' => 'Kegiatan tambahan', 'amount' => 50000],
                    ],
                    'status' => 'paid',
                    'payment_method' => 'Transfer Bank',
                    'paid_at' => Carbon::parse($inv['paid_at']),
                    'payment_proof_path' => 'seed/payment_proof_okt2026.svg',
                ]
            );
        }

        Invoice::updateOrCreate(
            ['child_id' => $syifa->id, 'month_year' => '2026-10'],
            [
                'amount' => 325000,
                'breakdown' => [
                    ['label' => 'Bimbel Pracalis', 'amount' => 275000],
                    ['label' => 'Kegiatan tambahan', 'amount' => 50000],
                ],
                'status' => 'paid',
                'payment_method' => 'Transfer Bank',
                'paid_at' => Carbon::parse('2026-10-05 09:18:00'),
                'payment_proof_path' => 'seed/payment_proof_okt2026.svg',
            ]
        );

        // 7. Notifications for Ibu Siti (Screen 10: Notifikasi)
        $parentSiti->appNotifications()->delete();

        $oct14Report = DailyReport::where('child_id', $aisyah->id)->whereDate('report_date', '2026-10-14')->first();
        $octInvoice = Invoice::where('child_id', $aisyah->id)->where('month_year', '2026-10')->first();

        $notificationsData = [
            [
                'type' => 'laporan',
                'title' => 'Laporan harian hari ini sudah tersedia',
                'message' => 'Aisyah telah menyelesaikan sesi belajar mengenal huruf B dan suku kata ba, bi, bu.',
                'action_url' => $oct14Report ? route('reports.daily.show', $oct14Report->id, false) : route('reports.daily', [], false),
                'is_read' => false,
                'created_at' => Carbon::parse('2026-10-14 12:30:00'),
            ],
            [
                'type' => 'laporan',
                'title' => 'Foto kegiatan baru diunggah',
                'message' => 'Bu Guru Nisa mengunggah 5 foto kegiatan kelas Kelompok Calistung A.',
                'action_url' => route('activities.index', [], false),
                'is_read' => false,
                'created_at' => Carbon::parse('2026-10-14 11:00:00'),
            ],
            [
                'type' => 'pembayaran',
                'title' => 'Pembayaran bulan Oktober sudah dikonfirmasi',
                'message' => 'Terima kasih Bunda, pembayaran SPP Oktober 2026 sebesar Rp 350.000 telah diterima.',
                'action_url' => $octInvoice ? route('payments.show', $octInvoice->id, false) : route('payments.index', [], false),
                'is_read' => true,
                'created_at' => Carbon::parse('2026-10-05 09:15:00'),
            ],
            [
                'type' => 'laporan',
                'title' => 'Laporan bulanan September sudah tersedia',
                'message' => 'Ringkasan perkembangan Membaca, Menulis, Berhitung, dan Sikap Belajar bulan September dapat dilihat.',
                'action_url' => route('reports.monthly', ['month' => '2026-09'], false),
                'is_read' => true,
                'created_at' => Carbon::parse('2026-09-30 16:20:00'),
            ],
            [
                'type' => 'info',
                'title' => 'Pengumuman jadwal libur',
                'message' => 'Kegiatan belajar libur pada hari peringatan nasional dan kembali aktif sesuai jadwal kelompok.',
                'action_url' => route('notifications.index', ['filter' => 'Info'], false),
                'is_read' => true,
                'created_at' => Carbon::parse('2026-09-28 10:00:00'),
            ],
        ];

        foreach ($notificationsData as $nData) {
            $notification = new AppNotification([
                'user_id' => $parentSiti->id,
                'child_id' => $aisyah->id,
                'type' => $nData['type'],
                'title' => $nData['title'],
                'message' => $nData['message'],
                'action_url' => $nData['action_url'],
                'is_read' => $nData['is_read'],
            ]);
            $notification->created_at = $nData['created_at'];
            $notification->updated_at = $nData['created_at'];
            $notification->save();
        }

        // Seed Notifications for Teacher (Bu Guru Nisa) & Admin (Admin Sabila)
        $teacher->appNotifications()->delete();
        $teacherNotifs = [
            [
                'type' => 'laporan',
                'title' => 'Pengingat evaluasi bulanan Oktober 2026',
                'message' => 'Mohon lengkapi catatan perkembangan Membaca, Menulis, Berhitung, dan Sikap Belajar untuk seluruh siswa Kelompok Calistung A dan Pracalis B sebelum akhir bulan.',
                'action_url' => route('reports.monthly', [], false),
                'is_read' => false,
                'created_at' => Carbon::parse('2026-10-14 08:00:00'),
            ],
            [
                'type' => 'info',
                'title' => 'Penugasan kelas mengajar aktif',
                'message' => 'Anda telah di-mapping oleh Admin Sabila sebagai guru pengajar pada Kelompok Calistung A (10.00 - 11.00) dan Kelompok Pracalis B (08.30 - 09.30).',
                'action_url' => route('classes.index', [], false),
                'is_read' => true,
                'created_at' => Carbon::parse('2026-10-01 09:00:00'),
            ],
        ];
        foreach ($teacherNotifs as $tn) {
            $n = new AppNotification([
                'user_id' => $teacher->id,
                'child_id' => $aisyah->id,
                'type' => $tn['type'],
                'title' => $tn['title'],
                'message' => $tn['message'],
                'action_url' => $tn['action_url'],
                'is_read' => $tn['is_read'],
            ]);
            $n->created_at = $tn['created_at'];
            $n->updated_at = $tn['created_at'];
            $n->save();
        }

        $admin->appNotifications()->delete();
        $adminNotifs = [
            [
                'type' => 'info',
                'title' => '1 Pendaftaran siswa baru menunggu persetujuan',
                'message' => 'Ibu Rina telah mendaftarkan Zaki ke Kelompok Calistung A. Silakan periksa ketersediaan kuota kelas dan berikan persetujuan.',
                'action_url' => route('classes.index', [], false),
                'is_read' => false,
                'created_at' => Carbon::parse('2026-10-14 13:10:00'),
            ],
            [
                'type' => 'pembayaran',
                'title' => 'Rekap pembayaran SPP Oktober 2026',
                'message' => 'Pembayaran SPP untuk Aisyah (Rp 350.000) dan Syifa (Rp 325.000) telah dikonfirmasi lunas melalui Transfer Bank.',
                'action_url' => route('payments.index', [], false),
                'is_read' => true,
                'created_at' => Carbon::parse('2026-10-05 09:30:00'),
            ],
        ];
        foreach ($adminNotifs as $an) {
            $n = new AppNotification([
                'user_id' => $admin->id,
                'child_id' => $aisyah->id,
                'type' => $an['type'],
                'title' => $an['title'],
                'message' => $an['message'],
                'action_url' => $an['action_url'],
                'is_read' => $an['is_read'],
            ]);
            $n->created_at = $an['created_at'];
            $n->updated_at = $an['created_at'];
            $n->save();
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
