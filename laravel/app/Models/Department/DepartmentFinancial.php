<?php

namespace App\Models\Department;

use Illuminate\Database\Eloquent\Model;

class DepartmentFinancial extends Model
{
    protected $table = 'department_financials';

    protected $fillable = [
        'department_id', 'budget', 'salary_fund', 'max_employees'
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'salary_fund' => 'decimal:2',
        'max_employees' => 'integer',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
