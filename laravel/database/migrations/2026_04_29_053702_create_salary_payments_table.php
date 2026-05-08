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
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tax_period_id')->constrained('tax_periods');
            $table->integer('year');
            $table->integer('month');

            // Начисления
            $table->decimal('base_salary', 12, 2);
            $table->decimal('bonus', 12, 2)->default(0);
            $table->decimal('overtime_pay', 12, 2)->default(0);
            $table->decimal('sick_leave', 12, 2)->default(0);
            $table->decimal('vacation_pay', 12, 2)->default(0);
            $table->decimal('other_accruals', 12, 2)->default(0);
            $table->decimal('total_accrued', 12, 2);

            // Налоговые вычеты
            $table->decimal('total_tax_deductions', 12, 2)->default(0);
            $table->json('tax_deductions_details')->nullable();

            // Налоги и удержания
            $table->decimal('income_tax', 12, 2);
            $table->decimal('pension_fund', 12, 2);
            $table->decimal('social_security', 12, 2);
            $table->decimal('trade_union', 12, 2)->default(0);
            $table->decimal('other_deductions', 12, 2)->default(0);
            $table->decimal('total_deductions', 12, 2);

            // Итог к выплате
            $table->decimal('net_salary', 12, 2);

            // Статус
            $table->enum('status', ['draft', 'calculated', 'approved', 'paid'])->default('draft');

            // Дополнительно
            $table->text('notes')->nullable();
            $table->json('tax_rates_snapshot')->nullable(); // Сохраняем ставки на момент расчета
            $table->timestamps();

            $table->unique(['employee_id', 'year', 'month']);
            $table->index(['year', 'month', 'department_id']);
            $table->index('status');
            $table->index('tax_period_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};
