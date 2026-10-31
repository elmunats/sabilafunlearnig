<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->date('birth_date');
            $table->string('group_name');
            $table->date('join_date');
            $table->timestamps();

            $table->index(['parent_id', 'name']);
        });

        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->date('report_date');
            $table->string('time_range', 50)->default('10.00 - 11.00');
            $table->json('materials');
            $table->json('activities');
            $table->json('behavior');
            $table->text('teacher_notes');
            $table->timestamps();

            $table->unique(['child_id', 'report_date']);
            $table->index(['child_id', 'report_date']);
        });

        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->string('month_year', 7); // Format: YYYY-MM e.g. 2026-10
            $table->unsignedTinyInteger('reading_score')->default(0);
            $table->unsignedTinyInteger('writing_score')->default(0);
            $table->unsignedTinyInteger('counting_score')->default(0);
            $table->unsignedTinyInteger('attitude_score')->default(0);
            $table->json('notes');
            $table->timestamps();

            $table->unique(['child_id', 'month_year']);
            $table->index(['child_id', 'month_year']);
        });

        Schema::create('activity_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_report_id')->constrained('daily_reports')->cascadeOnDelete();
            $table->string('category', 50)->default('Calistung'); // Calistung, Pracalis, Kegiatan Lain
            $table->string('photo_path');
            $table->string('caption');
            $table->string('taken_time', 20)->default('10.00');
            $table->timestamps();

            $table->index(['daily_report_id', 'category']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained('children')->cascadeOnDelete();
            $table->string('month_year', 7); // Format: YYYY-MM e.g. 2026-10
            $table->unsignedBigInteger('amount');
            $table->json('breakdown');
            $table->enum('status', ['unpaid', 'paid', 'pending'])->default('unpaid');
            $table->string('payment_method', 60)->default('Transfer Bank');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->timestamps();

            $table->unique(['child_id', 'month_year']);
            $table->index(['child_id', 'status']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('child_id')->nullable()->constrained('children')->nullOnDelete();
            $table->string('type', 30)->default('info'); // laporan, pembayaran, info
            $table->string('title');
            $table->text('message');
            $table->string('action_url')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'type', 'is_read']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('activity_photos');
        Schema::dropIfExists('monthly_reports');
        Schema::dropIfExists('daily_reports');
        Schema::dropIfExists('children');
    }
};

