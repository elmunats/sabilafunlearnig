<?php

use App\Http\Controllers\ActivityGalleryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassGroupController;
use App\Http\Controllers\DailyReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonthlyReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrivateMediaController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Landing Page / Home & Dashboard Entry
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/beranda-publik', [DashboardController::class, 'renderLandingPage'])->name('landing');

// Guest / Authentication Routes (1. Login Screen)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
    Route::post('/demo-login', [AuthController::class, 'demoLogin'])->name('login.demo');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/switch-role', [AuthController::class, 'switchRole'])->name('auth.switch-role');

    // 2. Dashboard Actions
    Route::post('/children/{child}/select', [DashboardController::class, 'selectChild'])->name('children.select');
    Route::post('/children/enroll', [ClassGroupController::class, 'enrollChild'])->name('children.enroll');
    Route::patch('/children/{child}/approval', [ClassGroupController::class, 'approveEnrollment'])->name('children.approval');

    // Kelas, Kuota, Mapping Guru, & Daftar Siswa (Admin & Guru)
    Route::get('/kelas', [ClassGroupController::class, 'index'])->name('classes.index');
    Route::post('/kelas', [ClassGroupController::class, 'store'])->name('classes.store');
    Route::patch('/kelas/{classGroup}/quota', [ClassGroupController::class, 'updateQuota'])->name('classes.quota');
    Route::delete('/kelas/{classGroup}', [ClassGroupController::class, 'destroy'])->name('classes.destroy');
    Route::post('/kelas/{classGroup}/teachers', [ClassGroupController::class, 'syncTeachers'])->name('classes.teachers');
    Route::post('/kelas/attach-teacher', [ClassGroupController::class, 'attachTeacherClass'])->name('classes.attach-teacher');
    Route::post('/teachers', [ClassGroupController::class, 'storeTeacher'])->name('teachers.store');

    // 3. Laporan Harian & 4. Detail Kegiatan / Laporan + Foto
    Route::get('/laporan-harian', [DailyReportController::class, 'index'])->name('reports.daily');
    Route::post('/laporan-harian/izin', [DailyReportController::class, 'submitLeave'])->name('reports.daily.leave');
    Route::get('/laporan-harian/{dailyReport}', [DailyReportController::class, 'show'])->name('reports.daily.show');
    Route::post('/laporan-harian', [DailyReportController::class, 'store'])->name('reports.daily.store');

    // 5. Laporan Bulanan
    Route::get('/laporan-bulanan', [MonthlyReportController::class, 'index'])->name('reports.monthly');
    Route::post('/laporan-bulanan', [MonthlyReportController::class, 'store'])->name('reports.monthly.store');

    // 6. Foto Kegiatan
    Route::get('/foto-kegiatan', [ActivityGalleryController::class, 'index'])->name('activities.index');
    Route::post('/foto-kegiatan', [ActivityGalleryController::class, 'store'])->name('activities.store');

    // 7. Pembayaran & 8. Detail Tagihan
    Route::get('/pembayaran', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/pembayaran/{invoice}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/pembayaran', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/pembayaran/{invoice}/proof', [PaymentController::class, 'uploadProof'])->name('payments.proof');
    Route::patch('/pembayaran/{invoice}/status', [PaymentController::class, 'updateStatus'])->name('payments.status');

    // 9. Profil Pengguna, Anak & Orang Tua
    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profil/account', [ProfileController::class, 'updateAccount'])->name('profile.account.update');
    Route::post('/profil/{child}', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/children', [ProfileController::class, 'storeChild'])->name('children.store');

    // 10. Notifikasi
    Route::get('/notifikasi', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifikasi', [NotificationController::class, 'store'])->name('notifications.store');
    Route::post('/notifikasi/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::post('/notifikasi/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    // Private Media Signed Route (Temporary Signed URL Verification + Row-Level Auth)
    Route::get('/private-media/{path}', [PrivateMediaController::class, 'show'])
        ->where('path', '.*')
        ->name('media.private');
});
