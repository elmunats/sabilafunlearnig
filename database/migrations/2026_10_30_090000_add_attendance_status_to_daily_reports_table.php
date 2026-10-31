<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->string('attendance_status', 20)->default('hadir')->after('report_date'); // hadir | izin | sakit
            $table->text('absence_reason')->nullable()->after('attendance_status');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['attendance_status', 'absence_reason']);
        });
    }
};

