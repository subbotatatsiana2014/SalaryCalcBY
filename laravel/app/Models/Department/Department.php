<?php

namespace App\Models\Department;

use App\Models\Employee\Employee;
use App\Models\User;
use App\Services\CacheService;
use App\Traits\HasCacheClear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    use HasCacheClear;

    protected $table = 'departments';

    protected $fillable = [
        'name', 'code', 'description', 'color',
        'manager_id', 'parent_id', 'is_active'
    ];

    protected $casts = ['is_active' => 'boolean'];

    // Связи
    public function financials()
    {
        return $this->hasOne(DepartmentFinancial::class, 'department_id');
    }

    public function settings()
    {
        return $this->hasOne(DepartmentSetting::class, 'department_id');
    }

    public function contacts()
    {
        return $this->hasOne(DepartmentContact::class, 'department_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function parent()
    {
        return $this->belongsTo(Department::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Department::class, 'parent_id');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class, 'department_id');
    }

    // Аксессоры
    public function getBudgetAttribute()
    {
        return $this->financials?->budget ?? 0;
    }

    public function getSalaryFundAttribute()
    {
        return $this->financials?->salary_fund ?? 0;
    }

    public function getEmployeesCountAttribute()
    {
        return $this->employees()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRootDepartments($query)
    {
        return $query->whereNull('parent_id');
    }

    protected function clearRelatedCache(): void
    {
        CacheService::flushDepartments();
    }
}
