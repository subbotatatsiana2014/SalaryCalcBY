<?php
namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class EmployeeEmergencyContact extends Model
{
    protected $table = 'employee_emergency_contacts';

    protected $fillable = [
        'employee_id', 'name', 'phone', 'relation'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
