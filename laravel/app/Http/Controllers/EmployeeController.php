<?php
namespace App\Http\Controllers;

use App\Models\Department\Department;
use App\Models\Employee\Employee;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewEmployeeNotificationMail;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(request $request)
    {
        try {
            $cacheKey = 'employees_index_' . md5($request->fullUrl());

            $employees = Cache::remember($cacheKey, 1800, function () {
                return Employee::with(['user', 'department'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);
            });

            $totalEmployees = Employee::count();
            $activeEmployees = Employee::where('is_active', true)->count();
            $totalSalary = Employee::sum('salary');
            $avgSalary = Employee::avg('salary');
            $departments = Cache::remember('department_for_filter', 86400, fn() => Department::all());
            $stats = Cache::remember('employees_stats', 3600, function () {
                return [
                    'total_employees' => Employee::count(),
                    'salary_fund' => number_format(Employee::sum('salary'), 2, '.', ''),
                    'taxes_due' => number_format(Employee::sum('salary') * 0.14, 2, '.', ''),
                    'total_departments' => Department::count(),
                ];
            });

            return view('employees.index', compact(
                'employees',
                'totalEmployees',
                'activeEmployees',
                'totalSalary',
                'avgSalary',
                'departments',
                'stats'
            ));

        } catch (\Exception $e) {
            \Log::error('EmployeeController index error: ' . $e->getMessage());

            $stats = [
                'total_employees' => 0,
                'salary_fund' => '0.00',
                'taxes_due' => '0.00',
                'total_departments' => 0,
            ];

            return view('employees.index', [
                'employees' => collect(),
                'totalEmployees' => 0,
                'activeEmployees' => 0,
                'totalSalary' => 0,
                'avgSalary' => 0,
                'departments' => collect(),
                'stats' => $stats,
                'error' => 'Произошла ошибка при загрузке данных'
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $departments = Department::where('is_active', true)->get();

            return view('employees.create', compact('departments'));

        } catch (\Exception $e) {
            \Log::error('EmployeeController create error: ' . $e->getMessage());

            return redirect()->route('employees.index')
                ->with('error', 'Ошибка при загрузке формы создания');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'last_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation error', $validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $userName = trim(($request->last_name ?? '') . ' ' . ($request->first_name ?? ''));
            if (empty($userName)) $userName = 'Сотрудник';

            $user = User::create([
                'name' => $userName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'employee',
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'gender' => $request->gender,
                'address' => $request->address,
            ]);

            $avatarPath = null;
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $avatarPath = $request->file('avatar')->store('employees/avatars', 'public');
            }

            $employee = Employee::create([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'avatar' => $avatarPath,
                'position' => $request->position,
                'position_code' => $request->position_code,
                'salary' => (float)$request->salary,
                'hire_date' => $request->hire_date,
                'fire_date' => $request->fire_date,
                'probation_end_date' => $request->probation_end_date,
                'employment_type' => $request->employment_type ?? 'full',
                'work_type' => $request->work_type ?? 'office',
                'work_schedule' => $request->work_schedule,
                'working_hours_per_week' => $request->working_hours_per_week ?? 40,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Личные данные
            $employee->personalInfo()->create([
                'last_name' => $request->last_name,
                'first_name' => $request->first_name,
                'middle_name' => $request->middle_name,
                'birth_place' => $request->birth_place,
                'nationality' => $request->nationality ?? 'Беларусь',
                'marital_status' => $request->marital_status,
                'children_count' => $request->children_count ?? 0,
            ]);

            // Контактные данные
            $employee->contactInfo()->create([
                'work_phone' => $request->work_phone,
                'education_level' => $request->education_level,
                'skills' => $request->skills,
                'languages' => $request->language,
                'notes' => $request->notes,
            ]);

            // Документы
            $employee->documents()->create([
                'passport_number' => $request->passport_number,
                'passport_issued_by' => $request->passport_issued_by,
                'passport_issued_date' => $request->passport_issued_date,
                'passport_expiry_date' => $request->passport_expiry_date,
                'tax_id' => $request->tax_id,
                'social_security_number' => $request->social_security_number,
                'bank_account' => $request->bank_account,
                'bank_name' => $request->bank_name,
                'insurance_policy_number' => $request->insurance_policy_number,
            ]);

            // Контактное лицо
            if ($request->emergency_contact_name || $request->emergency_contact_phone) {
                $employee->emergencyContacts()->create([
                    'name' => $request->emergency_contact_name,
                    'phone' => $request->emergency_contact_phone,
                    'relation' => $request->emergency_contact_relation,
                ]);
            }

            CacheService::flushEmployees();

            DB::commit();

            Mail::to($user->email)->send(new NewEmployeeNotificationMail($employee, $user));
            \Log::info('Welcome email sent to: ' . $user->email);

            $telegram = new TelegramService();

            $message = "🆕 <b>Новый сотрудник зарегистрирован!</b>\n\n";
            $message .= "👤 <b>ФИО:</b> {$employee->full_name}\n";
            $message .= "📧 <b>Email:</b> {$user->email}\n";
            $message .= "📱 <b>Телефон:</b> " . ($user->phone ?? 'не указан') . "\n";
            $message .= "💼 <b>Должность:</b> {$employee->position}\n";
            $message .= "🏢 <b>Подразделение:</b> " . ($employee->department->name ?? 'не назначено') . "\n";
            $message .= "📅 <b>Дата приема:</b> " . ($employee->hire_date?->format('d.m.Y') ?? 'не указана') . "\n";
            $message .= "🔑 <b>Пароль</b> будет выдан лично администратором!";

            $adminChatId = config('services.telegram.admin_chat_id');
            if ($adminChatId) {
                $telegram->sendMessage($adminChatId, $message);
                \Log::info('Telegram notification sent to admin');
            }

            return redirect()->route('employees.index')
                ->with('success', 'Сотрудник "' . $employee->full_name . '" успешно добавлен!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return redirect()->back()
                ->with('error', 'Ошибка при создании сотрудника или отправки email: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $employee = Employee::with([
                'user',
                'department',
                'department.manager',
                'personalInfo',
                'contactInfo',
                'documents',
                'emergencyContacts'
            ])->findOrFail($id);

            return view('employees.show', compact('employee'));

        } catch (\Exception $e) {
            \Log::error('EmployeeController show error: ' . $e->getMessage());

            return redirect()->route('employees.index')
                ->with('error', 'Сотрудник не найден');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $employee = Employee::with([
                'user',
                'personalInfo',
                'contactInfo',
                'documents',
                'emergencyContacts'
            ])->findOrFail($id);

            $departments = Department::where('is_active', true)->get();

            return view('employees.edit', compact('employee', 'departments'));

        } catch (\Exception $e) {

            \Log::error('EmployeeController edit error: ' . $e->getMessage());

            return redirect()->route('employees.index')
                ->with('error', 'Ошибка при загрузке данных для редактирования: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email,' . ($employee->user_id ?? 'NULL'),
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'position' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
        ], [
            'email.required' => 'Email обязателен',
            'email.unique' => 'Пользователь с таким email уже существует',
            'position.required' => 'Должность обязательна',
            'salary.required' => 'Оклад обязателен',
            'salary.numeric' => 'Оклад должен быть числом',
            'hire_date.required' => 'Дата приема обязательна',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            if ($employee->user) {
                $userName = trim(($request->last_name ?? '') . ' ' . ($request->first_name ?? '') . ' ' . ($request->middle_name ?? ''));
                if (empty($userName)) {
                    $userName = 'Сотрудник';
                }

                $employee->user->update([
                    'name' => $userName,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'birth_date' => $request->birth_date,
                    'gender' => $request->gender,
                    'address' => $request->address,
                ]);
            }

            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                // Удаляем старый аватар
                if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                    Storage::disk('public')->delete($employee->avatar);
                }
                $employee->avatar = $request->file('avatar')->store('employees/avatars', 'public');
            }

            if ($request->has('remove_avatar') && $request->remove_avatar == 1) {
                if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                    Storage::disk('public')->delete($employee->avatar);
                }
                $employee->avatar = null;
            }

            $employee->update([
                'department_id' => $request->department_id,
                'position' => $request->position,
                'position_code' => $request->position_code,
                'salary' => (float)$request->salary,
                'hire_date' => $request->hire_date,
                'fire_date' => $request->fire_date,
                'probation_end_date' => $request->probation_end_date,
                'employment_type' => $request->employment_type ?? $employee->employment_type,
                'work_type' => $request->work_type,
                'work_schedule' => $request->work_schedule,
                'working_hours_per_week' => $request->working_hours_per_week ?? 40,
                'is_active' => $request->boolean('is_active', $employee->is_active),
            ]);

            if ($employee->personalInfo) {
                $employee->personalInfo->update([
                    'last_name' => $request->last_name,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'birth_place' => $request->birth_place,
                    'nationality' => $request->nationality ?? 'Беларусь',
                    'marital_status' => $request->marital_status,
                    'children_count' => $request->children_count ?? 0,
                ]);
            } else {
                $employee->personalInfo()->create([
                    'last_name' => $request->last_name,
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'birth_place' => $request->birth_place,
                    'nationality' => $request->nationality ?? 'Беларусь',
                    'marital_status' => $request->marital_status,
                    'children_count' => $request->children_count ?? 0,
                ]);
            }

            if ($employee->contactInfo) {
                $employee->contactInfo->update([
                    'work_phone' => $request->work_phone,
                    'education_level' => $request->education_level,
                    'skills' => $request->skills,
                    'languages' => $request->language,
                    'notes' => $request->notes,
                ]);
            } else {
                $employee->contactInfo()->create([
                    'work_phone' => $request->work_phone,
                    'education_level' => $request->education_level,
                    'skills' => $request->skills,
                    'languages' => $request->language,
                    'notes' => $request->notes,
                ]);
            }

            if ($employee->documents) {
                $employee->documents->update([
                    'passport_number' => $request->passport_number,
                    'passport_issued_by' => $request->passport_issued_by,
                    'passport_issued_date' => $request->passport_issued_date,
                    'passport_expiry_date' => $request->passport_expiry_date,
                    'tax_id' => $request->tax_id,
                    'social_security_number' => $request->social_security_number,
                    'bank_account' => $request->bank_account,
                    'bank_name' => $request->bank_name,
                    'insurance_policy_number' => $request->insurance_policy_number,
                ]);
            } else {
                $employee->documents()->create([
                    'passport_number' => $request->passport_number,
                    'passport_issued_by' => $request->passport_issued_by,
                    'passport_issued_date' => $request->passport_issued_date,
                    'passport_expiry_date' => $request->passport_expiry_date,
                    'tax_id' => $request->tax_id,
                    'social_security_number' => $request->social_security_number,
                    'bank_account' => $request->bank_account,
                    'bank_name' => $request->bank_name,
                    'insurance_policy_number' => $request->insurance_policy_number,
                ]);
            }

            if ($request->emergency_contact_name || $request->emergency_contact_phone) {
                if ($employee->emergencyContacts) {
                    $employee->emergencyContacts->update([
                        'name' => $request->emergency_contact_name,
                        'phone' => $request->emergency_contact_phone,
                        'relation' => $request->emergency_contact_relation,
                    ]);
                } else {
                    $employee->emergencyContacts()->create([
                        'name' => $request->emergency_contact_name,
                        'phone' => $request->emergency_contact_phone,
                        'relation' => $request->emergency_contact_relation,
                    ]);
                }
            } else if ($employee->emergencyContacts) {
                $employee->emergencyContacts->delete();
            }

            CacheService::flushEmployees();

            DB::commit();

            return redirect()->route('employees.show', $employee->id)
                ->with('success', 'Данные сотрудника "' . $employee->full_name . '" успешно обновлены!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Employee update error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ошибка при обновлении данных сотрудника: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            if ($employee->avatar && Storage::disk('public')->exists($employee->avatar)) {
                Storage::disk('public')->delete($employee->avatar);
            }

            $employee->update([
                'is_active' => false,
                'fire_date' => now()
            ]);

            return redirect()->route('employees.index')
                ->with('success', 'Сотрудник успешно уволен');

        } catch (\Exception $e) {
            \Log::error('EmployeeController destroy error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ошибка при увольнении сотрудника: ' . $e->getMessage());
        }
    }

    /**
     * Restore employee
     */
    public function restore($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $employee->update([
                'is_active' => true,
                'fire_date' => null
            ]);

            return redirect()->route('employees.index')
                ->with('success', 'Сотрудник успешно восстановлен');

        } catch (\Exception $e) {
            \Log::error('EmployeeController restore error: ' . $e->getMessage());

            return redirect()->back()
                ->with('error', 'Ошибка при восстановлении сотрудника');
        }
    }

    /**
     * Search employees
     */
    public function search(Request $request)
    {
        try {
            $query = Employee::with(['user', 'department']);

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('position', 'like', "%{$search}%")
                        ->orWhereHas('user', function($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            }

            if ($request->has('department_id') && $request->department_id) {
                $query->where('department_id', $request->department_id);
            }

            if ($request->has('is_active') && $request->is_active !== '') {
                $query->where('is_active', $request->is_active);
            }

            $employees = $query->orderBy('created_at', 'desc')->paginate(20);

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            \Log::error('EmployeeController search error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при поиске сотрудников'
            ], 500);
        }
    }
}
