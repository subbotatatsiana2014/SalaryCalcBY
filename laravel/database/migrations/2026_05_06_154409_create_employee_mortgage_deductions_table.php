<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_mortgage_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->decimal('property_cost', 15, 2)->comment('Стоимость недвижимости');
            $table->decimal('total_possible_deduction', 15, 2)->comment('Максимально возможный вычет (13% от стоимости, но не более лимита)');
            $table->decimal('used_deduction', 15, 2)->default(0)->comment('Уже использованная сумма вычета');
            $table->decimal('remaining_deduction', 15, 2)->comment('Оставшаяся сумма вычета');
            $table->boolean('is_active')->default(true)->comment('Активен ли вычет');
            $table->date('start_date')->comment('Дата начала применения вычета');
            $table->date('end_date')->nullable()->comment('Дата окончания применения вычета');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_mortgage_deductions');
    }
};
