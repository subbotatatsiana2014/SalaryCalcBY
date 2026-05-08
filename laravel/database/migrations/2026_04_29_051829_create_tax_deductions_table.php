<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tax_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tax_period_id')->constrained('tax_periods')->cascadeOnDelete();
            $table->string('deduction_code', 50); // CHILD, STUDENT, MORTGAGE, etc.
            $table->string('deduction_name');
            $table->enum('calculation_type', ['fixed', 'percentage', 'per_child'])->default('fixed');
            $table->decimal('amount', 12, 2)->default(0);
            $table->integer('children_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'tax_period_id', 'deduction_code']);
            $table->index(['employee_id', 'tax_period_id']);
            $table->index('deduction_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_deductions');
    }
};
