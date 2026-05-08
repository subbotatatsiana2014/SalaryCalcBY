<?php

namespace App\Models\Employee;

use App\Models\Department\Department;
use App\Models\User;
use App\Services\CacheService;
use App\Traits\HasCacheClear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Employee extends Model
{
    use SoftDeletes;
    use HasCacheClear;

    protected $table = 'employees';

    protected $fillable = [
        'user_id', 'department_id', 'avatar', 'position', 'position_code',
        'salary', 'hire_date', 'fire_date', 'probation_end_date',
        'employment_type', 'work_type', 'work_schedule', 'working_hours_per_week', 'is_active',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
        'hire_date' => 'date',
        'fire_date' => 'date',
        'probation_end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function personalInfo()
    {
        return $this->hasOne(EmployeePersonalInfo::class, 'employee_id');
    }

    public function contactInfo()
    {
        return $this->hasOne(EmployeeContactInfo::class, 'employee_id');
    }

    public function documents()
    {
        return $this->hasOne(EmployeeDocument::class, 'employee_id');
    }

    public function emergencyContacts()
    {
        return $this->hasOne(EmployeeEmergencyContact::class, 'employee_id');
    }

    public function getFullNameAttribute()
    {
        if ($this->personalInfo) {
            $parts = [];
            if ($this->personalInfo->last_name) $parts[] = $this->personalInfo->last_name;
            if ($this->personalInfo->first_name) $parts[] = $this->personalInfo->first_name;
            if ($this->personalInfo->middle_name) $parts[] = $this->personalInfo->middle_name;
            if (!empty($parts)) return implode(' ', $parts);
        }
        return $this->user?->name ?? 'Не указано';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=' . urlencode($this->full_name ?? 'User');
    }

    public function getExperienceAttribute()
    {
        if ($this->hire_date) {
            $end = $this->fire_date ?? now();
            $years = $end->diffInYears($this->hire_date);
            $months = $end->diffInMonths($this->hire_date) % 12;
            $result = [];
            if ($years > 0) $result[] = "{$years} " . $this->declension($years, 'год', 'года', 'лет');
            if ($months > 0) $result[] = "{$months} " . $this->declension($months, 'месяц', 'месяца', 'месяцев');
            return implode(' ', $result) ?: '0 месяцев';
        }
        return 'Не указано';
    }

    private function declension($number, $one, $two, $five)
    {
        $number = abs($number) % 100;
        if ($number > 10 && $number < 20) return $five;
        $number %= 10;
        if ($number == 1) return $one;
        if ($number >= 2 && $number <= 4) return $two;
        return $five;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    protected function clearRelatedCache(): void
    {
        CacheService::flushEmployees();
    }
}
