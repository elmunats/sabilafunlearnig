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

    public function test_quick_demo_login_works_for_all_roles(): void
    {
        $this->post('/demo-login', ['role' => 'parent'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('parent', auth()->user()->role);
        $this->post('/logout');

        $this->post('/demo-login', ['role' => 'teacher'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('teacher', auth()->user()->role);
        $this->post('/logout');

        $this->post('/demo-login', ['role' => 'admin'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertEquals('admin', auth()->user()->role);
        $this->post('/logout');
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
}
