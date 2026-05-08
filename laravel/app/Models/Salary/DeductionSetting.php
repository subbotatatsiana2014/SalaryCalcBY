<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;

class DeductionSetting extends Model
{
    protected $table = 'deduction_settings';

    protected $fillable = [
        'code', 'name', 'category', 'calculation_type',
        'base_amount', 'is_percentage', 'max_amount',
        'requirements', 'is_active', 'effective_from', 'effective_to', 'sort_order'
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'is_percentage' => 'boolean',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('effective_from', '<=', now())
            ->where(function($q) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', now());
            });
    }
}
