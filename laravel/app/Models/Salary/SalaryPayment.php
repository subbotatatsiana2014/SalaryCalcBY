<?php

namespace App\Models\Salary;

use App\Models\Department\Department;
use App\Models\Employee\Employee;
use App\Models\Salary\TaxPeriod;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    protected $table = 'salary_payments';

    protected $fillable = [
        'employee_id', 'department_id', 'tax_period_id', 'year', 'month',
        'base_salary', 'bonus', 'overtime_pay', 'sick_leave', 'vacation_pay',
        'other_accruals', 'total_accrued', 'total_tax_deductions', 'tax_deductions_details',
        'income_tax', 'pension_fund', 'social_security', 'trade_union',
        'other_deductions', 'total_deductions', 'net_salary',
        'status', 'notes'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'bonus' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'sick_leave' => 'decimal:2',
        'vacation_pay' => 'decimal:2',
        'other_accruals' => 'decimal:2',
        'total_accrued' => 'decimal:2',
        'total_tax_deductions' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'pension_fund' => 'decimal:2',
        'social_security' => 'decimal:2',
        'trade_union' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'tax_deductions_details' => 'array',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function taxPeriod()
    {
        return $this->belongsTo(TaxPeriod::class);
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            'draft' => 'Черновик',
            'calculated' => 'Рассчитан',
            'approved' => 'Утверждён',
            'paid' => 'Выплачен'
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    public function getMonthNameAttribute()
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
            5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
            9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];
        return $months[$this->month] ?? '';
    }
}
