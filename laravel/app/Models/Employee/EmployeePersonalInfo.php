<?php
namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class EmployeePersonalInfo extends Model
{
    protected $table = 'employee_personal_info';

    protected $fillable = [
        'employee_id', 'last_name', 'first_name', 'middle_name',
        'birth_place', 'nationality', 'marital_status', 'children_count'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
