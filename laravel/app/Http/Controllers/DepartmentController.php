<?php

namespace App\Http\Controllers;

use App\Models\Department\Department;
use App\Models\Employee\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\CacheService;
use Illuminate\Support\Facades\Cache;

class DepartmentController extends Controller
{
    /**
     * Отображение главной страницы подразделений.
     */
    public function index()
    {
        try {

            $tablesExist = $this->checkTablesExist();

            if (!$tablesExist) {
                return view('departments.index', [
                    'departments' => collect(),
                    'departmentsCount' => $departments = Department::all()->count(),
                    'totalEmployees' => $employees = Employee::all()->count(),
                    'totalSalary' => 0,
                    'totalBudget' => 0,
                    'managers' => collect(),
                    'treeDepartments' => collect(),
                    'message' => 'Таблицы базы данных еще не созданы. Пожалуйста, запустите миграции.'
                ]);
            }

            // Получаем всех менеджеров (пользователей с ролью руководителя)
            $managers = User::whereHas('employee', function ($query) {
                $query->where('position', 'like', '%руководитель%')
                    ->orWhere('position', 'like', '%директор%')
                    ->orWhere('position', 'like', '%начальник%')
                    ->orWhere('position', 'like', '%менеджер%');
            })->get();

            // Если нет пользователей в должностях руководителей, берем всех пользователей
            if ($managers->isEmpty()) {
                $managers = User::all();
            }

            // Получаем все подразделения с отношениями
            $departments = Department::with(['manager', 'parent', 'children'])
                ->withCount('employees')
                ->orderBy('name')
                ->paginate(10);

            // Получаем подразделения для древовидной структуры
            $treeDepartments = Cache::remember('departments_tree', 3600, function () {
                return Department::with(['manager', 'children.manager', 'children.children'])
                    ->withCount('employees')
                    ->whereNull('parent_id')
                    ->orderBy('name')
                    ->get();
            });

            // Рассчитываем статистику
            $departmentsCount = $departments->total();
            $totalEmployees = Employee::count();
            $totalBudget = $departments->sum('budget');
            $totalSalary = Employee::sum('salary');
            $departments = Department::all();
            $stats = Cache::remember('departments_stats', 3600, function () {
                return [
                    'total_employees' => Employee::count(),
                    'salary_fund' => number_format(Employee::sum('salary'), 2, '.', ''),
                    'taxes_due' => number_format(Employee::sum('salary') * 0.14, 2, '.', ''),
                    'total_departments' => Department::count(),
                ];
            });

            // Рассчитываем ФОТ для каждого подразделения
            foreach ($departments as $department) {
                $department->salary_fund = $department->employees()->sum('salary');
            }

            return view('departments.index', compact(
                'departments',
                'departmentsCount',
                'totalEmployees',
                'totalSalary',
                'totalBudget',
                'stats',
                'managers',
                'treeDepartments'
            ));

        } catch (\Exception $e) {
            \Log::error('DepartmentController index error: ' . $e->getMessage());

            $stats = [
                'total_employees' => 0,
                'salary_fund' => '0.00',
                'taxes_due' => '0.00',
                'total_departments' => 0,
            ];

            return view('departments.index', [
                'departments' => collect(),
                'departmentsCount' => 0,
                'totalEmployees' => 0,
                'totalSalary' => 0,
                'totalBudget' => 0,
                'stats' => $stats,
                'managers' => collect(),
                'treeDepartments' => collect(),
                'error' => 'Произошла ошибка при загрузке данных: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Форма для создания нового подразделения.
     */
    public function create()
    {
        try {
            $managers = User::all();
            $departments = Department::all();

            return view('departments.create', compact('managers', 'departments'));

        } catch (\Exception $e) {
            \Log::error('DepartmentController create error: ' . $e->getMessage());

            return redirect()->route('departments.index')
                ->with('error', 'Ошибка при загрузке формы создания');
        }
    }

    /**
     * Сохранение нового подразделения в базе данных.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code',
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:0',
            'salary_fund' => 'nullable|numeric|min:0',
            'color' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Название подразделения обязательно',
            'code.required' => 'Код подразделения обязателен',
            'code.unique' => 'Такой код подразделения уже существует',
            'budget.required' => 'Бюджет обязателен',
            'budget.numeric' => 'Бюджет должен быть числом',
            'budget.min' => 'Бюджет не может быть отрицательным'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Создаем подразделение
            $department = Department::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'color' => $request->color ?? '#3B82F6',
                'manager_id' => $request->manager_id,
                'parent_id' => $request->parent_id,
                'is_active' => $request->boolean('is_active', true)
            ]);

            // Сохраняем финансовые данные в таблицу department_financials
            $department->financials()->create([
                'budget' => $request->budget,
                'salary_fund' => $request->salary_fund ?? 0,
                'max_employees' => $request->max_employees ?? null,
            ]);

            CacheService::flushDepartments();

            DB::commit();

            \Log::info('Подразделение создано: ' . $department->name, ['id' => $department->id]);

            return redirect()->route('departments.index')
                ->with('success', 'Подразделение "' . $department->name . '" успешно создано!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Department creation error: ' . $e->getMessage());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при создании подразделения: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Ошибка при создании подразделения: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Отображение выбанного подразделения.
     */
    public function show(string $id)
    {
        try {
            $department = Department::with([
                'manager',
                'parent',
                'children.manager',
                'employees.user'
            ])
                ->withCount('employees')
                ->findOrFail($id);

            return view('departments.show', compact('department'));

        } catch (\Exception $e) {
            \Log::error('DepartmentController show error: ' . $e->getMessage());

            return redirect()->route('departments.index')
                ->with('error', 'Подразделение не найдено');
        }
    }

    /**
     * Редактирование выбранного подразделения.
     */
    public function edit(string $id)
    {
        try {
            $department = Department::findOrFail($id);
            $managers = User::all();
            $departments = Department::where('id', '!=', $id)->get();

            \Log::info('Подразделение успешно отредактировано: ' . $department->name, ['id' => $department->id]);

            return view('departments.edit', compact('department', 'managers', 'departments'));

        } catch (\Exception $e) {
            \Log::error('DepartmentController edit error: ' . $e->getMessage());

            return view('departments.index')
                ->with('error', 'Ошибка при редактировании подразделения!');
        }
    }

    /**
     * Обновление данных подразделения в базе данных.
     */
    public function update(Request $request, string $id)
    {
        $department = Department::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:departments,code,' . $id,
            'description' => 'nullable|string',
            'budget' => 'required|numeric|min:0',
            'color' => 'nullable|string|size:7',
            'manager_id' => 'nullable|exists:users,id',
            'parent_id' => 'nullable|exists:departments,id',
            'is_active' => 'boolean'
        ], [
            'name.required' => 'Название подразделения обязательно',
            'code.required' => 'Код подразделения обязателен',
            'code.unique' => 'Такой код подразделения уже существует',
            'budget.required' => 'Бюджет обязателен',
            'budget.numeric' => 'Бюджет должен быть числом',
            'budget.min' => 'Бюджет не может быть отрицательным'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Проверяем, не пытаемся ли сделать подразделение родителем самого себя
            if ($request->parent_id == $id) {
                throw new \Exception('Подразделение не может быть родителем самого себя');
            }

            $department->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'color' => $request->color ?? $department->color,
                'manager_id' => $request->manager_id,
                'parent_id' => $request->parent_id,
                'is_active' => $request->boolean('is_active')
            ]);

            $financials = $department->financials()->first();

            if ($financials) {
                $financials->update([
                    'budget' => $request->budget,
                    'salary_fund' => $request->salary_fund ?? $financials->salary_fund,
                    'max_employees' => $request->max_employees ?? $financials->max_employees,
                ]);
            } else {
                $department->financials()->create([
                    'budget' => $request->budget,
                    'salary_fund' => $request->salary_fund ?? 0,
                    'max_employees' => $request->max_employees ?? null,
                ]);
            }

            CacheService::flushDepartments();

            DB::commit();

            \Log::info('Подразделение обновлено: ' . $department->name, ['id' => $department->id]);

            return redirect()->route('departments.show', $department->id)
                ->with('success', 'Подразделение "' . $department->name . '" успешно обновлено');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('DepartmentController update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении подразделения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удаление выбранного подразделения.
     */
    public function destroy(string $id)
    {
        try {
            $department = Department::findOrFail($id);

            // Проверяем, можно ли удалить подразделение
            if ($department->employees()->count() > 0 && !$department->parent_id) {
                // Перемещаем сотрудников в корневое подразделение
                $this->moveEmployeesToRoot($department);
            }

            // Удаляем подразделение
            $department->delete();

            CacheService::flushDepartments();

            \Log::info('Подразделение удалено: ' . $department->name, ['id' => $department->id]);

            return response()->json([
                'success' => true,
                'message' => 'Подразделение успешно удалено'
            ]);

        } catch (\Exception $e) {
            \Log::error('DepartmentController destroy error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении подразделения: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Отображение сотрудников определенного подразделения.
     */
    public function employees(string $id)
    {
        try {
            $department = Department::with(['employees.user'])->findOrFail($id);

            return view('departments.employees', compact('department'));

        } catch (\Exception $e) {
            \Log::error('DepartmentController employees error: ' . $e->getMessage());

            return redirect()->route('departments.index')
                ->with('error', 'Подразделение не найдено');
        }
    }

    /**
     * Получение подразделений для древовидной иерархии.
     */
    public function tree()
    {
        try {
            $departments = Department::with(['children.children'])
                ->whereNull('parent_id')
                ->orderBy('name')
                ->get();

            return response()->json([
                'success' => true,
                'departments' => $departments
            ]);

        } catch (\Exception $e) {
            \Log::error('DepartmentController tree error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при загрузке структуры'
            ], 500);
        }
    }

    /**
     * Перемещение сотрудников в корневое подразделение, при удалении подразделения.
     */
    private function moveEmployeesToRoot(Department $department)
    {
        try {
            $employees = $department->employees;

            foreach ($employees as $employee) {
                $employee->department_id = null;
                $employee->save();
            }

            \Log::info('Сотрудники перемещены из удаляемого подразделения', [
                'department_id' => $department->id,
                'employees_count' => $employees->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Ошибка при перемещении сотрудников: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Проверяка существование таблиц.
     */
    private function checkTablesExist(): bool
    {
        try {
            $tables = ['departments', 'users', 'employees'];

            foreach ($tables as $table) {
                if (!DB::getSchemaBuilder()->hasTable($table)) {
                    return false;
                }
            }

            return true;

        } catch (\Exception $e) {
            return false;
        }
    }
}
