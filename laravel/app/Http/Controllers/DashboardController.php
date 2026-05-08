<?php

namespace App\Http\Controllers;

use App\Models\Department\Department;
use App\Models\Employee\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = Cache::remember('dashboard_stats', 3600, function () {
            return [
                'total_employees' => Employee::count(),
                'salary_fund' => number_format(Employee::sum('salary'), 2, '.', ''),
                'taxes_due' => number_format(Employee::sum('salary') * 0.14, 2, '.', ''),
                'total_departments' => Department::count(),
            ];
        });

        $users = Cache::remember('users_list', 86400, fn() => User::all());
        $departments = Cache::remember('departments_list', 86400, fn() => Department::all());
        $employees = Cache::remember('employees_list', 86400, fn() => Employee::all());

        return view('dashboard.index', compact('stats', 'users', 'departments', 'employees'));
    }
}
