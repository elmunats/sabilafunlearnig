<?php

namespace Tests\Feature;

use App\Models\Child;
use App\Models\ClassGroup;
use App\Models\DailyReport;
use App\Models\Invoice;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class SabilaFunLearningTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
        $this->seedTestFixtures();
    }

    private function seedTestFixtures(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->first();
        if (! $admin) {
            $admin = User::create([
                'name' => 'Admin Sabila',
                'email' => 'admin@sabilafun.id',
                'phone' => '081100001111',
                'role' => 'admin',
                'password' => 'password123',
            ]);
            $admin->assignRole('admin');
        }

        $teacherNisa = User::firstOrCreate(
            ['email' => 'guru@sabilafun.id'],
            [
                'name' => 'Ustadzah Annisa',
                'phone' => '081222223333',
                'role' => 'teacher',
                'password' => 'password123',
            ]
        );
        $teacherNisa->syncRoles(['teacher']);

        $teacherTari = User::firstOrCreate(
            ['email' => 'tari@sabilafun.id'],
            [
                'name' => 'Ustadzah Tari',
                'phone' => '081233334444',
                'role' => 'teacher',
                'password' => 'password123',
            ]
        );
        $teacherTari->syncRoles(['teacher']);

        $parentSiti = User::firstOrCreate(
            ['email' => 'siti@example.com'],
            [
                'name' => 'Siti Aminah',
                'phone' => '081234567890',
                'role' => 'parent',
                'address' => 'Jl. Anggrek No. 12, Bandung',
                'password' => 'password123',
            ]
        );
        $parentSiti->syncRoles(['parent']);

        $parentBudi = User::firstOrCreate(
            ['email' => 'budi@example.com'],
            [
                'name' => 'Budi Santoso',
                'phone' => '081298765432',
                'role' => 'parent',
                'address' => 'Jl. Melati No. 5, Bandung',
                'password' => 'password123',
            ]
        );
        $parentBudi->syncRoles(['parent']);

        $calistungA = ClassGroup::firstOrCreate(
            ['name' => 'Kelompok Calistung A'],
            [
                'category' => 'Calistung',
                'quota' => 8,
                'schedule' => 'Senin - Jumat, 10.00 - 11.00',
            ]
        );
        $calistungA->teachers()->syncWithoutDetaching([$teacherNisa->id]);

        $calistungB = ClassGroup::firstOrCreate(
            ['name' => 'Kelompok Calistung B'],
            [
                'category' => 'Calistung',
                'quota' => 8,
                'schedule' => 'Senin - Jumat, 11.15 - 12.15',
            ]
        );

        $aisyah = Child::firstOrCreate(
            ['name' => 'Aisyah'],
            [
                'parent_id' => $parentSiti->id,
                'class_group_id' => $calistungA->id,
                'group_name' => 'Kelompok Calistung A',
                'photo' => 'avatars/aisyah.svg',
                'enrollment_status' => 'approved',
                'birth_date' => '2021-04-12',
                'join_date' => '2026-07-01',
            ]
        );

        \Illuminate\Support\Facades\Storage::disk(\App\Services\PrivateMediaService::diskName())
            ->put('avatars/aisyah.svg', '<svg xmlns="http://www.w3.org/2000/svg"></svg>');

        $rafa = Child::firstOrCreate(
            ['name' => 'Rafa'],
            [
                'parent_id' => $parentBudi->id,
                'class_group_id' => $calistungB->id,
                'group_name' => 'Kelompok Calistung B',
                'enrollment_status' => 'approved',
                'birth_date' => '2021-08-20',
                'join_date' => '2026-07-01',
            ]
        );

        $dailyReport = DailyReport::firstOrCreate(
            [
                'child_id' => $aisyah->id,
                'report_date' => '2026-10-14',
            ],
            [
                'teacher_id' => $teacherNisa->id,
                'time_range' => '10.00 - 11.00',
                'materials' => ['Mengenal huruf C dan D', 'Berhitung 1 sampai 10'],
                'activities' => ['Menulis huruf C dan D di buku kotak', 'Bermain kartu angka'],
                'behavior' => [
                    'Fokus' => 'Sangat baik',
                    'Antusias' => 'Sangat baik',
                    'Kemandirian' => 'Baik',
                ],
                'teacher_notes' => 'Aisyah sangat antusias dan konsentrasi baik.',
                'attendance_status' => 'hadir',
            ]
        );

        // Seed other attendance records for stats test
        foreach (['2026-10-01', '2026-10-02', '2026-10-05', '2026-10-06'] as $date) {
            DailyReport::firstOrCreate(
                ['child_id' => $aisyah->id, 'report_date' => $date],
                [
                    'teacher_id' => $teacherNisa->id,
                    'time_range' => '10.00 - 11.00',
                    'materials' => ['Belajar'],
                    'activities' => ['Bermain'],
                    'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Baik', 'Kemandirian' => 'Baik'],
                    'teacher_notes' => 'Catatan harian.',
                    'attendance_status' => 'hadir',
                ]
            );
        }
        DailyReport::firstOrCreate(
            ['child_id' => $aisyah->id, 'report_date' => '2026-10-07'],
            [
                'teacher_id' => $teacherNisa->id,
                'time_range' => '10.00 - 11.00',
                'materials' => ['Izin'],
                'activities' => ['Izin'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Baik', 'Kemandirian' => 'Baik'],
                'teacher_notes' => 'Izin',
                'attendance_status' => 'izin',
                'absence_reason' => 'Acara',
            ]
        );
        DailyReport::firstOrCreate(
            ['child_id' => $aisyah->id, 'report_date' => '2026-10-08'],
            [
                'teacher_id' => $teacherNisa->id,
                'time_range' => '10.00 - 11.00',
                'materials' => ['Sakit'],
                'activities' => ['Sakit'],
                'behavior' => ['Fokus' => 'Baik', 'Antusias' => 'Baik', 'Kemandirian' => 'Baik'],
                'teacher_notes' => 'Sakit',
                'attendance_status' => 'sakit',
                'absence_reason' => 'Demam',
            ]
        );

        DailyReport::firstOrCreate(
            [
                'child_id' => $rafa->id,
                'report_date' => '2026-10-14',
            ],
            [
                'teacher_id' => $teacherNisa->id,
                'time_range' => '11.15 - 12.15',
                'materials' => ['Mengenal huruf'],
                'activities' => ['Mewarnai'],
                'behavior' => [
                    'Fokus' => 'Baik',
                    'Antusias' => 'Baik',
                    'Kemandirian' => 'Baik',
                ],
                'teacher_notes' => 'Rafa aktif di kelas.',
                'attendance_status' => 'hadir',
            ]
        );

        Invoice::firstOrCreate(
            [
                'child_id' => $aisyah->id,
                'month_year' => '2026-10',
            ],
            [
                'invoice_number' => 'INV-202610-001',
                'amount' => 350000,
                'breakdown' => [
                    ['item' => 'SPP Bulan Oktober 2026', 'cost' => 300000],
                    ['item' => 'Modul Belajar', 'cost' => 50000],
                ],
                'status' => 'paid',
                'due_date' => '2026-10-10',
                'paid_at' => '2026-10-05',
                'payment_method' => 'BCA Virtual Account',
            ]
        );
    }

    public function test_public_home_renders_landing_page_instead_of_redirecting_to_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Landing');
        $response->assertSee('publicClasses');
    }

    public function test_admin_can_create_mengaji_class_with_days_start_time_and_end_time(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('classes.store'), [
                'name' => 'Kelompok Mengaji Iqra Sore',
                'category' => 'Mengaji',
                'quota' => 12,
                'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
                'start_time' => '15:30',
                'end_time' => '16:30',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('class_groups', [
            'name' => 'Kelompok Mengaji Iqra Sore',
            'category' => 'Mengaji',
            'quota' => 12,
            'schedule' => 'Senin - Minggu, 15.30 - 16.30',
        ]);
    }

    public function test_login_screen_renders_and_authenticates_with_email_or_phone(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);

        $loginByEmail = $this->post('/login', [
            'identifier' => 'siti@example.com',
            'password' => 'password123',
        ]);
        $loginByEmail->assertRedirect(route('dashboard'));
        $this->post('/logout');

        $loginByPhone = $this->post('/login', [
            'identifier' => '0812 3456 7890',
            'password' => 'password123',
        ]);
        $loginByPhone->assertRedirect(route('dashboard'));
    }

    public function test_admin_seed_account_can_login_with_email_and_phone(): void
    {
        $response = $this->post('/login', [
            'identifier' => 'admin@sabilafun.id',
            'password' => 'password123',
        ]);
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
        $this->post('/logout');

        $responsePhone = $this->post('/login', [
            'identifier' => '081100001111',
            'password' => 'password123',
        ]);
        $responsePhone->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->post('/logout');
    }

    public function test_admin_can_delete_class_group_and_non_admin_is_forbidden(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();
        $teacher = User::where('email', 'guru@sabilafun.id')->firstOrFail();
        $classToDelete = ClassGroup::create([
            'name' => 'Kelas Sementara',
            'category' => 'Bermain & Belajar',
            'quota' => 5,
            'schedule' => 'Sabtu',
        ]);

        // Non-admin (teacher) cannot delete class
        $this->actingAs($teacher)
            ->delete(route('classes.destroy', $classToDelete->id))
            ->assertStatus(403);

        // Admin can delete class
        $this->actingAs($admin)
            ->delete(route('classes.destroy', $classToDelete->id))
            ->assertRedirect();

        $this->assertDatabaseMissing('class_groups', ['id' => $classToDelete->id]);
    }

    public function test_user_can_update_account_profile_and_change_password(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();

        // 1. Password change without matching current_password fails
        $this->actingAs($admin)
            ->patch(route('profile.account.update'), [
                'name' => 'Admin Baru',
                'email' => 'admin_baru@sabilafun.id',
                'phone' => '081199990000',
                'current_password' => 'salah123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertSessionHasErrors(['current_password']);

        // 2. Successful update with valid current_password
        $this->actingAs($admin)
            ->patch(route('profile.account.update'), [
                'name' => 'Admin Terupdate',
                'email' => 'admin_baru@sabilafun.id',
                'phone' => '081199990000',
                'current_password' => 'password123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $admin->refresh();
        $this->assertEquals('Admin Terupdate', $admin->name);
        $this->assertEquals('admin_baru@sabilafun.id', $admin->email);
        $this->assertEquals('081199990000', $admin->phone);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $admin->password));
    }

    public function test_all_ten_screens_render_for_authenticated_parent(): void
    {
        $parent = User::where('email', 'siti@example.com')->firstOrFail();
        $aisyah = Child::where('name', 'Aisyah')->firstOrFail();
        $dailyReport = DailyReport::where('child_id', $aisyah->id)->whereDate('report_date', '2026-10-14')->firstOrFail();
        $invoice = Invoice::where('child_id', $aisyah->id)->where('month_year', '2026-10')->firstOrFail();

        $this->actingAs($parent);

        $this->get('/')->assertStatus(200);
        $this->post(route('children.select', $aisyah->id))->assertRedirect();
        $this->get('/laporan-harian?date=2026-10-14')->assertStatus(200);
        $this->get(route('reports.daily.show', $dailyReport->id))->assertStatus(200);
        $this->get('/laporan-bulanan?month=2026-10')->assertStatus(200);
        $this->get('/foto-kegiatan?category=Calistung')->assertStatus(200);
        $this->get('/pembayaran')->assertStatus(200);
        $this->get(route('payments.show', $invoice->id))->assertStatus(200);
        $this->get('/profil')->assertStatus(200);
        $this->get('/notifikasi')->assertStatus(200);
    }

    public function test_parent_can_enroll_new_child_from_home_with_pending_approval_and_admin_can_approve_with_quota_check(): void
    {
        $parent = User::where('email', 'siti@example.com')->firstOrFail();
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();
        $calistungA = ClassGroup::where('name', 'Kelompok Calistung A')->firstOrFail();

        // 1. Parent registers a new child from Beranda -> status must be 'pending'
        $this->actingAs($parent)->post(route('children.enroll'), [
            'name' => 'Nayla',
            'birth_date' => '2022-11-10',
            'class_group_id' => $calistungA->id,
        ])->assertRedirect();

        $nayla = Child::where('name', 'Nayla')->firstOrFail();
        $this->assertEquals('pending', $nayla->enrollment_status);
        $this->assertEquals($calistungA->id, $nayla->class_group_id);

        // 2. Admin sets class quota to 1 (already occupied by Aisyah), so approving Nayla is blocked by quota
        $this->actingAs($admin)->patch(route('classes.quota', $calistungA->id), [
            'name' => 'Kelompok Calistung A',
            'category' => 'Calistung',
            'quota' => 1,
            'schedule' => 'Senin - Jumat, 10.00 - 11.00',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('children.approval', $nayla->id), [
            'enrollment_status' => 'approved',
            'class_group_id' => $calistungA->id,
        ])->assertSessionHas('error');

        $this->assertEquals('pending', $nayla->fresh()->enrollment_status);

        // 3. Admin increases class quota to 10 and approves Nayla -> status becomes 'approved'
        $this->actingAs($admin)->patch(route('classes.quota', $calistungA->id), [
            'name' => 'Kelompok Calistung A',
            'category' => 'Calistung',
            'quota' => 10,
            'schedule' => 'Senin - Jumat, 10.00 - 11.00',
        ])->assertRedirect();

        $this->actingAs($admin)->patch(route('children.approval', $nayla->id), [
            'enrollment_status' => 'approved',
            'class_group_id' => $calistungA->id,
        ])->assertRedirect();

        $this->assertEquals('approved', $nayla->fresh()->enrollment_status);
    }

    public function test_admin_and_teacher_can_view_classes_map_teachers_and_admin_can_perform_teacher_actions(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();
        $teacherNisa = User::where('email', 'guru@sabilafun.id')->firstOrFail();
        $teacherTari = User::where('email', 'tari@sabilafun.id')->firstOrFail();
        $calistungA = ClassGroup::where('name', 'Kelompok Calistung A')->firstOrFail();
        $calistungB = ClassGroup::where('name', 'Kelompok Calistung B')->firstOrFail();
        $aisyah = Child::where('name', 'Aisyah')->firstOrFail();

        // Teacher Nisa teaches multiple classes and can view /kelas
        $this->actingAs($teacherNisa)->get('/kelas')->assertStatus(200);

        // Teacher CANNOT create a class or attach a teaching class (Admin-only features)
        $this->actingAs($teacherNisa)->post(route('classes.store'), [
            'name' => 'Kelompok Tidak Sah',
            'category' => 'Calistung',
            'quota' => 10,
            'schedule' => 'Senin - Jumat',
        ])->assertStatus(403);

        $this->actingAs($teacherNisa)->post(route('classes.attach-teacher'), [
            'teacher_id' => $teacherNisa->id,
            'class_group_id' => $calistungB->id,
        ])->assertStatus(403);

        // Admin CAN attach an additional class for a teacher and map multiple teachers to a class
        $this->actingAs($admin)->post(route('classes.attach-teacher'), [
            'teacher_id' => $teacherNisa->id,
            'class_group_id' => $calistungB->id,
        ])->assertRedirect();
        $this->assertTrue($teacherNisa->teachingClasses()->where('class_groups.id', $calistungB->id)->exists());

        $this->actingAs($admin)->get('/kelas')->assertStatus(200);
        $this->actingAs($admin)->post(route('classes.teachers', $calistungA->id), [
            'teacher_ids' => [$teacherNisa->id, $teacherTari->id],
        ])->assertRedirect();
        $this->assertEquals(2, $calistungA->teachers()->count());

        // Admin has all Teacher features (creating Daily Reports, Monthly Reports, Activity Photos)
        $this->actingAs($admin)->post('/laporan-harian', [
            'child_id' => $aisyah->id,
            'report_date' => '2026-10-20',
            'time_range' => '10.00 - 11.00',
            'materials' => ['Mengenal huruf G dan H'],
            'activities' => ['Menulis kata gajah dan harimau'],
            'behavior_fokus' => 'Sangat baik',
            'behavior_antusias' => 'Sangat baik',
            'behavior_kemandirian' => 'Baik',
            'teacher_notes' => 'Dievaluasi langsung oleh Admin/Kepala Sekolah.',
        ])->assertRedirect();

        $this->actingAs($admin)->post('/laporan-bulanan', [
            'child_id' => $aisyah->id,
            'month_year' => '2026-11',
            'reading_score' => 85,
            'writing_score' => 80,
            'counting_score' => 88,
            'attitude_score' => 92,
            'note_reading' => 'Lancar membaca suku kata.',
            'note_writing' => 'Tulisan rapi.',
            'note_counting' => 'Lancar berhitung.',
            'note_attitude' => 'Sangat sopan dan aktif.',
        ])->assertRedirect();

        $this->assertDatabaseHas('daily_reports', [
            'child_id' => $aisyah->id,
            'teacher_notes' => 'Dievaluasi langsung oleh Admin/Kepala Sekolah.',
        ]);
        $this->assertDatabaseHas('monthly_reports', [
            'child_id' => $aisyah->id,
            'month_year' => '2026-11',
        ]);
    }

    public function test_row_level_authorization_prevents_parent_from_viewing_other_parents_children(): void
    {
        $parentSiti = User::where('email', 'siti@example.com')->firstOrFail();
        $otherChild = Child::where('name', 'Rafa')->firstOrFail();
        $otherReport = DailyReport::where('child_id', $otherChild->id)->firstOrFail();

        $this->actingAs($parentSiti);
        $this->post(route('children.select', $otherChild->id))->assertStatus(403);
        $this->get(route('reports.daily.show', $otherReport->id))->assertStatus(403);
    }

    public function test_private_media_requires_valid_temporary_signed_url(): void
    {
        $parent = User::where('email', 'siti@example.com')->firstOrFail();
        $this->actingAs($parent);

        $encodedPath = base64_encode('avatars/aisyah.svg');
        $this->get('/private-media/' . $encodedPath)->assertStatus(403);

        $signedUrl = URL::temporarySignedRoute('media.private', now()->addMinutes(15), ['path' => $encodedPath]);
        $this->get($signedUrl)->assertStatus(200);
    }

    public function test_admin_can_send_notifications_with_title_and_description_and_all_roles_can_view_notification_details(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();
        $teacher = User::where('email', 'guru@sabilafun.id')->firstOrFail();
        $parent = User::where('email', 'siti@example.com')->firstOrFail();

        // Non-admin cannot send broadcast notifications
        $this->actingAs($parent)->post(route('notifications.store'), [
            'target_audience' => 'all',
            'type' => 'info',
            'title' => 'Percobaan Tidak Sah',
            'message' => 'Deskripsi pesan tidak sah',
        ])->assertStatus(403);

        // Admin sends notification to all users with title and description
        $this->actingAs($admin)->post(route('notifications.store'), [
            'target_audience' => 'all',
            'type' => 'info',
            'title' => 'Pengumuman Evaluasi Tengah Semester',
            'message' => 'Kegiatan evaluasi belajar akan dilaksanakan mulai hari Senin depan pukul 09.00 WIB.',
        ])->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $parent->id,
            'title' => 'Pengumuman Evaluasi Tengah Semester',
            'message' => 'Kegiatan evaluasi belajar akan dilaksanakan mulai hari Senin depan pukul 09.00 WIB.',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $teacher->id,
            'title' => 'Pengumuman Evaluasi Tengah Semester',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $admin->id,
            'title' => 'Pengumuman Evaluasi Tengah Semester',
        ]);

        // Parent, Teacher, and Admin can view notifications and mark a notification read on detail view
        foreach ([$parent, $teacher, $admin] as $roleUser) {
            $this->actingAs($roleUser)->get('/notifikasi')->assertStatus(200);
            $notification = \App\Models\AppNotification::where('user_id', $roleUser->id)
                ->where('title', 'Pengumuman Evaluasi Tengah Semester')
                ->firstOrFail();

            $this->actingAs($roleUser)->post(route('notifications.read', $notification->id))->assertRedirect();
            $this->assertTrue($notification->fresh()->is_read);
        }
    }

    public function test_parent_can_submit_izin_or_sakit_and_monthly_and_daily_reports_reflect_attendance_statistics(): void
    {
        $parentSiti = User::where('email', 'siti@example.com')->firstOrFail();
        $aisyah = Child::where('name', 'Aisyah')->firstOrFail();
        $otherChild = Child::where('name', 'Rafa')->firstOrFail();

        // Parent cannot submit izin/sakit for another parent's child
        $this->actingAs($parentSiti)->post(route('reports.daily.leave'), [
            'child_id' => $otherChild->id,
            'report_date' => '2026-10-19',
            'attendance_status' => 'izin',
            'absence_reason' => 'Acara keluarga',
        ])->assertStatus(403);

        // Parent submits an Izin request for Aisyah on 2026-10-19
        $this->actingAs($parentSiti)->post(route('reports.daily.leave'), [
            'child_id' => $aisyah->id,
            'report_date' => '2026-10-19',
            'attendance_status' => 'izin',
            'absence_reason' => 'Mengunjungi kakek nenek di Bandung',
        ])->assertRedirect();

        // Parent submits a Sakit request for Aisyah on 2026-10-21
        $this->actingAs($parentSiti)->post(route('reports.daily.leave'), [
            'child_id' => $aisyah->id,
            'report_date' => '2026-10-21',
            'attendance_status' => 'sakit',
            'absence_reason' => 'Batuk pilek dan istirahat',
        ])->assertRedirect();

        $this->assertDatabaseHas('daily_reports', [
            'child_id' => $aisyah->id,
            'attendance_status' => 'izin',
            'absence_reason' => 'Mengunjungi kakek nenek di Bandung',
        ]);
        $this->assertDatabaseHas('daily_reports', [
            'child_id' => $aisyah->id,
            'attendance_status' => 'sakit',
            'absence_reason' => 'Batuk pilek dan istirahat',
        ]);

        // Verify October 2026 counts in DailyReport (5 seeded hadir + 2 seeded izin/sakit + 2 new izin/sakit = 5 hadir, 2 izin, 2 sakit)
        $octReports = DailyReport::where('child_id', $aisyah->id)
            ->whereYear('report_date', 2026)
            ->whereMonth('report_date', 10)
            ->get();

        $this->assertEquals(5, $octReports->where('attendance_status', 'hadir')->count());
        $this->assertEquals(2, $octReports->where('attendance_status', 'izin')->count());
        $this->assertEquals(2, $octReports->where('attendance_status', 'sakit')->count());

        // Both Daily and Monthly report pages render with attendanceStats prop
        $this->actingAs($parentSiti)
            ->get('/laporan-harian?date=2026-10-19&child_id=' . $aisyah->id)
            ->assertStatus(200)
            ->assertSee('attendanceStats');

        $this->actingAs($parentSiti)
            ->get('/laporan-bulanan?month=2026-10&child_id=' . $aisyah->id)
            ->assertStatus(200)
            ->assertSee('attendanceStats');
    }

    public function test_new_parent_can_register_with_gender_address_and_optional_photo(): void
    {
        $calistungA = ClassGroup::firstOrFail();

        $response = $this->post('/register', [
            'name' => 'Bunda Dian',
            'email' => 'dian@example.com',
            'phone' => '081299998888',
            'address' => 'Jl. Kenanga No. 45, Jakarta Selatan',
            'password' => 'secret123',
            'child_name' => 'Fatih Rizqi',
            'child_birth_date' => '2021-08-20',
            'child_gender' => 'laki-laki',
            'child_group' => $calistungA->name,
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'dian@example.com',
            'phone' => '081299998888',
            'address' => 'Jl. Kenanga No. 45, Jakarta Selatan',
            'role' => 'parent',
        ]);

        $child = Child::where('name', 'Fatih Rizqi')->first();
        $this->assertNotNull($child);
        $this->assertEquals('laki-laki', $child->gender);
        $this->assertEquals('Jl. Kenanga No. 45, Jakarta Selatan', $child->address);
        $this->assertEquals('pending', $child->enrollment_status);
        $this->assertEquals('avatars/rafa.svg', $child->photo);
    }

    public function test_admin_can_delete_teacher_student_and_parent_while_non_admin_is_forbidden(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();
        $parent = User::where('email', 'siti@example.com')->firstOrFail();
        $classGroup = ClassGroup::firstOrFail();

        // Create dummy teacher, child, parent
        $teacherToDelete = User::factory()->create([
            'role' => 'teacher',
            'email' => 'temp_teacher@sabilafun.id',
        ]);
        $parentToDelete = User::factory()->create([
            'role' => 'parent',
            'email' => 'temp_parent@sabilafun.id',
        ]);
        $childToDelete = Child::create([
            'parent_id' => $parentToDelete->id,
            'class_group_id' => $classGroup->id,
            'group_name' => $classGroup->name,
            'name' => 'Anak Hapus Test',
            'birth_date' => '2022-01-01',
            'join_date' => '2026-01-01',
            'enrollment_status' => 'approved',
        ]);

        // Non-admin attempts deletion -> 403 Forbidden
        $this->actingAs($parent)->delete("/teachers/{$teacherToDelete->id}")->assertStatus(403);
        $this->actingAs($parent)->delete("/children/{$childToDelete->id}")->assertStatus(403);
        $this->actingAs($parent)->delete("/parents/{$parentToDelete->id}")->assertStatus(403);

        // Admin can delete teacher
        $this->actingAs($admin)->delete("/teachers/{$teacherToDelete->id}")->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $teacherToDelete->id]);

        // Admin can delete child
        $this->actingAs($admin)->delete("/children/{$childToDelete->id}")->assertRedirect();
        $this->assertDatabaseMissing('children', ['id' => $childToDelete->id]);

        // Admin can delete parent (cascading children)
        $childOfParent = Child::create([
            'parent_id' => $parentToDelete->id,
            'class_group_id' => $classGroup->id,
            'group_name' => $classGroup->name,
            'name' => 'Anak Cascade Test',
            'birth_date' => '2022-02-02',
            'join_date' => '2026-01-01',
            'enrollment_status' => 'approved',
        ]);
        $this->actingAs($admin)->delete("/parents/{$parentToDelete->id}")->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $parentToDelete->id]);
        $this->assertDatabaseMissing('children', ['id' => $childOfParent->id]);
    }

    public function test_admin_can_update_parent_details_and_non_admin_is_forbidden(): void
    {
        $admin = User::where('email', 'admin@sabilafun.id')->firstOrFail();
        $parent = User::where('email', 'siti@example.com')->firstOrFail();

        $targetParent = User::factory()->create([
            'name' => 'Bunda Asli',
            'email' => 'bunda_asli@example.com',
            'phone' => '081211112222',
            'role' => 'parent',
            'address' => 'Alamat Asli',
        ]);

        // Non-admin cannot update parent
        $this->actingAs($parent)->patch("/parents/{$targetParent->id}", [
            'name' => 'Bunda Palsu',
            'email' => 'bunda_palsu@example.com',
            'phone' => '081299990000',
        ])->assertStatus(403);

        // Admin can update parent
        $response = $this->actingAs($admin)->patch("/parents/{$targetParent->id}", [
            'name' => 'Bunda Diperbarui',
            'email' => 'bunda_baru@example.com',
            'phone' => '081277778888',
            'address' => 'Perum Baru No 10',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id' => $targetParent->id,
            'name' => 'Bunda Diperbarui',
            'email' => 'bunda_baru@example.com',
            'phone' => '081277778888',
            'address' => 'Perum Baru No 10',
        ]);
    }
}
