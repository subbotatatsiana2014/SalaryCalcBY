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
        Schema::create('deduction_settings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('category'); // CHILD, STUDENT, MORTGAGE, HEALTH, LOW_INCOME, etc.
            $table->enum('calculation_type', ['fixed', 'percentage', 'per_child'])->default('fixed');
            $table->decimal('base_amount', 12, 2);
            $table->boolean('is_percentage')->default(false);
            $table->decimal('max_amount', 12, 2)->nullable();
            $table->text('requirements')->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Стандартные вычеты РБ
        $now = date('Y-m-d');

        DB::table('deduction_settings')->insert([
            // Стандартные вычеты на детей
            [
                'code' => 'CHILD_1',
                'name' => 'Вычет на первого ребенка',
                'category' => 'CHILD',
                'calculation_type' => 'fixed',
                'base_amount' => 0,
                'is_percentage' => false,
                'max_amount' => null,
                'requirements' => 'На первого ребенка до 18 лет',
                'is_active' => true,
                'effective_from' => $now,
                'sort_order' => 10
            ],
            [
                'code' => 'CHILD_2_PLUS',
                'name' => 'Вычет на второго и последующих детей',
                'category' => 'CHILD',
                'calculation_type' => 'fixed',
                'base_amount' => 0,
                'is_percentage' => false,
                'max_amount' => null,
                'requirements' => 'На второго и каждого последующего ребенка',
                'is_active' => true,
                'effective_from' => $now,
                'sort_order' => 20
            ],
            [
                'code' => 'CHILD_DISABLED',
                'name' => 'Вычет на ребенка-инвалида',
                'category' => 'CHILD',
                'calculation_type' => 'fixed',
                'base_amount' => 0,
                'is_percentage' => false,
                'max_amount' => null,
                'requirements' => 'На ребенка-инвалида до 18 лет',
                'is_active' => true,
                'effective_from' => $now,
                'sort_order' => 30
            ],
            // Иные вычеты
            [
                'code' => 'STUDENT_ADULT',
                'name' => 'Вычет на обучающегося ребенка (18-24 лет)',
                'category' => 'STUDENT',
                'calculation_type' => 'fixed',
                'base_amount' => 0,
                'is_percentage' => false,
                'max_amount' => null,
                'requirements' => 'На ребенка в возрасте от 18 до 24 лет, обучающегося на дневном отделении',
                'is_active' => true,
                'effective_from' => $now,
                'sort_order' => 40
            ],
            [
                'code' => 'MORTGAGE',
                'name' => 'Имущественный вычет (ипотека)',
                'category' => 'MORTGAGE',
                'calculation_type' => 'percentage',
                'base_amount' => 13,
                'is_percentage' => true,
                'max_amount' => 78100,
                'requirements' => 'При покупке квартиры в ипотеку',
                'is_active' => true,
                'effective_from' => $now,
                'sort_order' => 50
            ],
            [
                'code' => 'HEALTH_INSURANCE',
                'name' => 'Страховые взносы (ДМС)',
                'category' => 'HEALTH',
                'calculation_type' => 'fixed',
                'base_amount' => 0,
                'is_percentage' => false,
                'max_amount' => 5200,
                'requirements' => 'Договор добровольного медицинского страхования',
                'is_active' => true,
                'effective_from' => $now,
                'sort_order' => 60
            ],
            [
                'code' => 'LOW_INCOME',
                'name' => 'Доплата до минимальной ЗП',
                'category' => 'LOW_INCOME',
                'calculation_type' => 'fixed',
                'base_amount' => 0,
                'is_percentage' => false,
                'max_amount' => null,
                'requirements' => 'Для работников с низким уровнем дохода',
                'is_active' => true,
                'effective_from' => $now,
                'sort_order' => 70
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deduction_settings');
    }
};
