<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class EmployeeMortgageDeduction extends Model
{
    protected $table = 'employee_mortgage_deductions';

    protected $fillable = [
        'employee_id',
        'property_cost',
        'total_possible_deduction',
        'used_deduction',
        'remaining_deduction',
        'is_active',
        'start_date',
        'end_date',
        'notes'
    ];

    protected $casts = [
        'property_cost' => 'decimal:2',
        'total_possible_deduction' => 'decimal:2',
        'used_deduction' => 'decimal:2',
        'remaining_deduction' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Получить активный имущественный вычет сотрудника
     */
    public static function getActiveForEmployee(int $employeeId): ?self
    {
        return self::where('employee_id', $employeeId)
            ->where('is_active', true)
            ->where('remaining_deduction', '>', 0)
            ->first();
    }
}
