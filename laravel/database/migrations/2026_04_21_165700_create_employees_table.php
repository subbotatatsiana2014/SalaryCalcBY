<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('avatar')->nullable();
            $table->string('position');
            $table->string('position_code')->nullable();
            $table->decimal('salary', 12, 2);
            $table->date('hire_date');
            $table->date('fire_date')->nullable();
            $table->date('probation_end_date')->nullable();
            $table->enum('employment_type', ['full', 'part', 'contract', 'temporary', 'remote'])->default('full');
            $table->enum('work_type', ['office', 'hybrid', 'remote'])->nullable();
            $table->string('work_schedule')->nullable();
            $table->integer('working_hours_per_week')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('department_id');
            $table->index('is_active');
            $table->index('employment_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
