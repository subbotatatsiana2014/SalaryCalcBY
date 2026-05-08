<?php

namespace App\Models\Department;

use Illuminate\Database\Eloquent\Model;

class DepartmentContact extends Model
{
    protected $table = 'department_contacts';

    protected $fillable = [
        'department_id', 'phone', 'email', 'address', 'location'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function getFullAddressAttribute()
    {
        $parts = [];
        if ($this->address) $parts[] = $this->address;
        if ($this->location) $parts[] = $this->location;
        return implode(', ', $parts) ?: 'Не указан';
    }

    public function getMapUrlAttribute()
    {
        if ($this->address || $this->location) {
            return "https://maps.google.com/?q=" . urlencode($this->full_address);
        }
        return null;
    }
}
