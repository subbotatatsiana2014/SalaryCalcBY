<?php
namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class EmployeeContactInfo extends Model
{
    protected $table = 'employee_contact_info';

    protected $fillable = [
        'employee_id', 'work_phone', 'education_level', 'skills', 'languages', 'notes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
