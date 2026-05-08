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
        Schema::create('tax_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('year');
            $table->integer('month')->nullable();
            $table->enum('period_type', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['year', 'month', 'period_type']);
            $table->index(['year', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_periods');
    }
};
