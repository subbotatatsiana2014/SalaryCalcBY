<?php

namespace App\Models\Salary;

use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    protected $table = 'tax_rates';

    protected $fillable = [
        'code', 'name', 'category', 'calculation_type',
        'rate_value', 'rate_unit', 'min_base', 'max_base',
        'is_active', 'effective_from', 'effective_to', 'sort_order', 'description'
    ];

    protected $casts = [
        'rate_value' => 'decimal:4',
        'min_base' => 'decimal:2',
        'max_base' => 'decimal:2',
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

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public static function getRate($code)
    {
        $rate = self::active()->where('code', $code)->first();
        return $rate ? $rate->rate_value : 0;
    }
}
