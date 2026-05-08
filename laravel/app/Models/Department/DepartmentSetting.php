<?php

namespace App\Models\Department;

use Illuminate\Database\Eloquent\Model;

class DepartmentSetting extends Model
{
    protected $table = 'department_settings';

    protected $fillable = [
        'department_id', 'short_name', 'type', 'icon', 'additional_info'
    ];

    protected $casts = ['additional_info' => 'array'];

    const TYPES = [
        'management' => 'Руководство',
        'development' => 'Разработка',
        'qa' => 'Тестирование',
        'design' => 'Дизайн',
        'operations' => 'Операции',
        'support' => 'Поддержка',
        'sales' => 'Продажи',
        'marketing' => 'Маркетинг',
        'hr' => 'Кадры',
        'finance' => 'Финансы',
        'administration' => 'Администрация',
        'other' => 'Другое'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? 'Неизвестно';
    }
}
