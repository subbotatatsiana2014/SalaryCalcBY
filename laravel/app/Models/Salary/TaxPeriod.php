<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;

class TaxPeriod extends Model
{
    protected $table = 'tax_periods';

    protected $fillable = [
        'year', 'month', 'period_type', 'start_date', 'end_date', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function getPeriodNameAttribute()
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
            5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
            9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];

        if ($this->period_type === 'monthly') {
            return "{$months[$this->month]} {$this->year}";
        }

        if ($this->period_type === 'quarterly') {
            $quarter = ceil($this->month / 3);
            return "{$quarter}-й квартал {$this->year}";
        }

        return (string) $this->year;
    }
}
