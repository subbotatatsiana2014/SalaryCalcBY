<?php
namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    protected $table = 'employee_documents';

    protected $fillable = [
        'employee_id', 'passport_number', 'passport_issued_by', 'passport_issued_date',
        'passport_expiry_date', 'tax_id', 'social_security_number', 'bank_account',
        'bank_name', 'insurance_policy_number'
    ];

    protected $casts = [
        'passport_issued_date' => 'date',
        'passport_expiry_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
