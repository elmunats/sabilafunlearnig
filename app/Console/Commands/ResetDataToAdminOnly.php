<?php

namespace App\Console\Commands;

use App\Models\AppNotification;
use App\Models\Child;
use App\Models\ClassGroup;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;

class ResetDataToAdminOnly extends Command
{
    protected $signature = 'app:reset-clean {--force : Jalankan tanpa konfirmasi}';

    protected $description = 'Reset seluruh data murid, laporan, dan akun guru/orang tua, hanya menyisakan akun Admin';

    public function handle(): int
    {
        if (! $this->option('force') && ! $this->confirm('Apakah Anda yakin ingin MENGHAPUS SEMUA data murid, laporan, tagihan, dan menyisakan akun Admin saja?', false)) {
            $this->info('Operasi dibatalkan.');
            return self::SUCCESS;
        }

        $this->warn('Memulai proses reset data...');

        // 1. Hapus data anak (otomatis cascade menghapus daily_reports, monthly_reports, activity_photos, invoices)
        $childrenCount = Child::count();
        Child::query()->delete();
        $this->line("- Terhapus {$childrenCount} data murid beserta seluruh riwayat laporan.");

        // 2. Hapus sisa notifikasi
        AppNotification::query()->delete();
        $this->line('- Terhapus seluruh riwayat notifikasi.');

        // 3. Hapus seluruh pengguna selain Admin
        $deletedUsers = User::where('role', '!=', 'admin')
            ->where('email', '!=', 'admin@sabilafun.id')
            ->delete();
        $this->line("- Terhapus {$deletedUsers} akun guru dan orang tua.");

        // 4. Pastikan Role Spatie dan Akun Admin tetap aktif
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'teacher']);
        Role::firstOrCreate(['name' => 'parent']);

        $admin = User::firstOrCreate(
            ['email' => 'admin@sabilafun.id'],
            [
                'name' => 'Admin Sabila',
                'phone' => '0811 0000 1111',
                'role' => 'admin',
                'address' => 'Kantor Pusat Sabila Fun Learning, Jl. Pendidikan No. 8, Jakarta',
                'password' => 'password123',
            ]
        );
        $admin->role = 'admin';
        $admin->save();
        $admin->syncRoles([$adminRole]);

        // 5. Pastikan kelompok kelas default tersedia
        $defaultClasses = [
            ['name' => 'Kelompok Calistung A', 'category' => 'Calistung', 'quota' => 8, 'schedule' => 'Senin - Jumat, 10.00 - 11.00'],
            ['name' => 'Kelompok Calistung B', 'category' => 'Calistung', 'quota' => 8, 'schedule' => 'Senin - Jumat, 11.15 - 12.15'],
            ['name' => 'Kelompok Pracalis', 'category' => 'Pracalis', 'quota' => 8, 'schedule' => 'Senin - Jumat, 08.30 - 09.30'],
            ['name' => 'Kelompok Mengaji', 'category' => 'Mengaji', 'quota' => 10, 'schedule' => 'Senin - Jumat, 15.30 - 16.30'],
        ];
        foreach ($defaultClasses as $cls) {
            ClassGroup::firstOrCreate(['name' => $cls['name']], $cls);
        }

        // 6. Bersihkan cache aplikasi
        Artisan::call('optimize:clear');

        $this->newLine();
        $this->info('✓ Database berhasil di-reset bersih!');
        $this->table(
            ['Parameter', 'Informasi'],
            [
                ['Akun Tersisa', 'Hanya Admin (' . $admin->name . ')'],
                ['Email Login', $admin->email],
                ['Kelompok Kelas', ClassGroup::count() . ' kelompok siap digunakan'],
                ['Status', 'Sistem bersih dan siap menerima pendaftaran baru'],
            ]
        );

        return self::SUCCESS;
    }
}
