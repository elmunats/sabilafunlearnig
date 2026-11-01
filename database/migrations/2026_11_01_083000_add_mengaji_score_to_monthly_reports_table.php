<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('monthly_reports', 'mengaji_score')) {
                $table->unsignedTinyInteger('mengaji_score')->default(80)->after('counting_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('monthly_reports', function (Blueprint $table) {
            if (Schema::hasColumn('monthly_reports', 'mengaji_score')) {
                $table->dropColumn('mengaji_score');
            }
        });
    }
};

