<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('category', 50)->default('Calistung'); // Calistung, Pracalis
            $table->unsignedSmallInteger('quota')->default(10);
            $table->string('schedule', 100)->default('Senin - Jumat, 10.00 - 11.00');
            $table->timestamps();
        });

        Schema::create('class_group_teacher', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_group_id')->constrained('class_groups')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['class_group_id', 'teacher_id']);
        });

        Schema::table('children', function (Blueprint $table) {
            $table->foreignId('class_group_id')
                ->nullable()
                ->after('parent_id')
                ->constrained('class_groups')
                ->nullOnDelete();

            $table->string('enrollment_status', 20)
                ->default('approved')
                ->after('group_name')
                ->index(); // pending, approved, rejected

            $table->timestamp('approved_at')->nullable()->after('enrollment_status');
        });
    }

    public function down(): void
    {
        Schema::table('children', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_group_id');
            $table->dropColumn(['enrollment_status', 'approved_at']);
        });

        Schema::dropIfExists('class_group_teacher');
        Schema::dropIfExists('class_groups');
    }
};

