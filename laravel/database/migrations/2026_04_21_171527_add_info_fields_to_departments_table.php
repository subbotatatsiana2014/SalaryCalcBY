<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {

            if (!Schema::hasColumn('departments', 'short_name')) {
                $table->string('short_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('departments', 'type')) {
                $table->string('type')->default('other')->after('description');
            }
            if (!Schema::hasColumn('departments', 'icon')) {
                $table->string('icon')->default('fas fa-building')->after('color');
            }
            if (!Schema::hasColumn('departments', 'additional_info')) {
                $table->json('additional_info')->nullable()->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $columns = ['short_name', 'type', 'max_employees', 'icon', 'additional_info'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('departments', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
