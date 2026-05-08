<?php

namespace App\Models\Employee;

use App\Models\Employee\Employee;
use App\Models\Salary\DeductionSetting;
use Illuminate\Database\Eloquent\Model;

class TaxDeduction extends Model
{
    protected $table = 'tax_deductions';

    protected $fillable = [
        'employee_id', 'tax_period_id', 'deduction_code',
        'deduction_name', 'calculation_type', 'amount',
        'children_count', 'is_active', 'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'children_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function taxPeriod()
    {
        return $this->belongsTo(TaxPeriod::class);
    }

    public function getTotalAmountAttribute()
    {
        $setting = DeductionSetting::where('code', $this->deduction_code)->first();

        if (!$setting) {
            return $this->amount;
        }

        if ($setting->calculation_type === 'per_child') {
            return $setting->base_amount * $this->children_count;
        }

        if ($setting->is_percentage) {
            return ($this->amount * $setting->base_amount) / 100;
        }

        return $setting->base_amount;
    }
}
