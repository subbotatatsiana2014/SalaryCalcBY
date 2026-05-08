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
        Schema::table('employee_personal_info', function (Blueprint $table) {
            $table->integer('students_count')->default(0)->after('children_count');
            $table->boolean('has_disabled_child')->default(false)->after('students_count');
            $table->boolean('is_low_income')->default(false)->after('has_disabled_child');
            $table->decimal('mortgage_amount', 12, 2)->nullable()->after('is_low_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_personal_info', function (Blueprint $table) {
            $table->dropColumn(['students_count', 'has_disabled_child', 'is_low_income', 'mortgage_amount']);
        });
    }
};
