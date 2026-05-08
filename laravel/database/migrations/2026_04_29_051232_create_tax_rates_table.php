<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->string('category')->index();
            $table->enum('calculation_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('rate_value', 10, 4);
            $table->string('rate_unit', 10)->default('%');
            $table->decimal('min_base', 15, 2)->nullable();
            $table->decimal('max_base', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['category', 'is_active', 'effective_from']);
        });

        $now = date('Y-m-d');

        DB::table('tax_rates')->insert([
            [
                'code' => 'INCOME_TAX_2026',
                'name' => 'Подоходный налог',
                'category' => 'income_tax',
                'calculation_type' => 'percentage',
                'rate_value' => 13,
                'rate_unit' => '%',
                'min_base' => null,
                'max_base' => null,
                'is_active' => true,
                'effective_from' => '2026-01-01',
                'effective_to' => null,
                'sort_order' => 10,
                'description' => 'Подоходный налог с физических лиц',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'PENSION_FUND_2026',
                'name' => 'Пенсионный фонд (ФСЗН)',
                'category' => 'social_security',
                'calculation_type' => 'percentage',
                'rate_value' => 1,
                'rate_unit' => '%',
                'min_base' => null,
                'max_base' => null,
                'is_active' => true,
                'effective_from' => '2026-01-01',
                'effective_to' => null,
                'sort_order' => 20,
                'description' => 'Взносы в Фонд социальной защиты населения',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SOCIAL_SECURITY_2026',
                'name' => 'Социальное страхование',
                'category' => 'social_security',
                'calculation_type' => 'percentage',
                'rate_value' => 0.6,
                'rate_unit' => '%',
                'min_base' => null,
                'max_base' => null,
                'is_active' => true,
                'effective_from' => '2026-01-01',
                'effective_to' => null,
                'sort_order' => 30,
                'description' => 'Взносы на обязательное социальное страхование',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TRADE_UNION_2026',
                'name' => 'Профсоюзные взносы',
                'category' => 'trade_union',
                'calculation_type' => 'percentage',
                'rate_value' => 1,
                'rate_unit' => '%',
                'min_base' => null,
                'max_base' => null,
                'is_active' => true,
                'effective_from' => '2026-01-01',
                'effective_to' => null,
                'sort_order' => 40,
                'description' => 'Профсоюзные взносы (по желанию)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
    }
};
