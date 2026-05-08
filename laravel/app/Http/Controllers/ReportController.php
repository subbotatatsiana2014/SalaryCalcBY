<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee\Employee;
use App\Models\Department\Department;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    public function index() {
        $departments = Department::with(['manager', 'parent', 'children'])
            ->withCount('employees')
            ->orderBy('name')
            ->paginate(10);

        $departmentsCount = $departments->total();
        $totalEmployees = Employee::count();
        $totalBudget = $departments->sum('budget');
        $totalSalary = Employee::sum('salary');
        $departments = Department::all();
        $stats = Cache::remember('tax_stats', 3600, function () {
            $totalSalary = Employee::sum('salary');
            $totalEmployees = Employee::count();
            $allDepartments = Department::count();

            return [
                'total_employees' => $totalEmployees,
                'salary_fund' => number_format($totalSalary, 2, '.', ''),
                'taxes_due' => number_format($totalSalary * 0.14, 2, '.', ''),
                'total_departments' => $allDepartments,
            ];
        });

        // Рассчитываем ФОТ для каждого подразделения
        foreach ($departments as $department) {
            $department->salary_fund = $department->employees()->sum('salary');
        }

        return view('reports.index', compact(
            'departments',
            'departmentsCount',
            'totalEmployees',
            'totalSalary',
            'totalBudget',
            'stats',
        ));
    }
}
