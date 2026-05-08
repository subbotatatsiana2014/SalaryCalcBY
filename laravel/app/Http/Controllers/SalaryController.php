<?php

namespace App\Http\Controllers;

use App\Models\Department\Department;
use App\Models\Employee\Employee;
use App\Models\Salary\SalaryPayment;
use App\Models\Salary\TaxPeriod;
use App\Models\Salary\TaxRate;
use App\Services\SalaryCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Services\CacheService;

class SalaryController extends Controller
{
    protected $salaryService;

    public function __construct(SalaryCalculationService $salaryService)
    {
        $this->salaryService = $salaryService;
    }

    /**
     * Главная страница модуля зарплаты
     */
    public function index()
    {
        $departments = Department::where('is_active', true)->get();
        $currentYear = date('Y');
        $currentMonth = date('m');
        $lastCalculations = SalaryPayment::with(['employee.user', 'department'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $stats = Cache::remember('salary_stats', 3600, function () {
            return [
                'total_calculations' => SalaryPayment::count(),
                'total_paid' => SalaryPayment::where('status', 'paid')->sum('net_salary'),
                'total_tax' => SalaryPayment::sum('income_tax'),
                'avg_salary' => round(SalaryPayment::avg('net_salary'), 2),
            ];
        });

        return view('salary.index', compact('departments', 'currentYear', 'currentMonth', 'lastCalculations', 'stats'));
    }

    /**
     * Страница калькулятора зарплаты
     */
    public function calculate()
    {
        $departments = Department::where('is_active', true)->get();
        $employees = Employee::where('is_active', true)->get();
        $currentYear = date('Y');
        $currentMonth = date('m');
        $taxRates = TaxRate::active()->orderBy('sort_order')->get();

        return view('salary.calculate', compact(
            'departments',
            'employees',
            'currentYear',
            'currentMonth',
            'taxRates'
        ));
    }

    /**
     * История расчетов
     */
    public function history(Request $request)
    {
        $query = SalaryPayment::with(['employee.user', 'department', 'taxPeriod']);

        // Фильтрация
        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $calculations = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(20);

        $departments = Department::where('is_active', true)->get();
        $years = range(date('Y') - 2, date('Y') + 1);
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март', 4 => 'Апрель',
            5 => 'Май', 6 => 'Июнь', 7 => 'Июль', 8 => 'Август',
            9 => 'Сентябрь', 10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];
        $statuses = ['draft' => 'Черновик', 'calculated' => 'Рассчитан', 'approved' => 'Утверждён', 'paid' => 'Выплачен'];

        return view('salary.history', compact('calculations', 'departments', 'years', 'months', 'statuses'));
    }

    /**
     * Результаты расчета
     */
    public function results($id)
    {
        $calculation = SalaryPayment::with(['employee.user', 'department', 'taxPeriod'])
            ->findOrFail($id);

        // Получаем все расчеты за этот же период для общего отчета
        $periodCalculations = SalaryPayment::with(['employee.user', 'department'])
            ->where('year', $calculation->year)
            ->where('month', $calculation->month)
            ->get();

        $summary = $this->calculateSummary($periodCalculations);

        return view('salary.results', compact('calculation', 'periodCalculations', 'summary'));
    }

    /**
     * Предварительный просмотр расчета
     */
    public function preview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
            'department_id' => 'nullable|exists:departments,id',
            'employee_id' => 'nullable|exists:employees,id',
            'base_salary' => 'nullable|numeric|min:0',
            'base_days' => 'nullable|numeric|min:0|max:31',
            'base_hours' => 'nullable|numeric|min:0',
            'fact_days' => 'nullable|numeric|min:0|max:31',
            'fact_hours' => 'nullable|numeric|min:0',
            'bonus_percentage' => 'nullable|numeric|min:0|max:100',
            'overtime_hours' => 'nullable|numeric|min:0',
            'sick_days' => 'nullable|numeric|min:0',
            'vacation_days' => 'nullable|numeric|min:0',
            'other_accruals' => 'nullable|numeric|min:0',
            'include_trade_union' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $year = $request->year;
        $month = $request->month;
        $departmentId = $request->department_id;
        $employeeId = $request->employee_id;
        $bonusPercentage = $request->bonus_percentage ?? 0;
        $overtimeHours = $request->overtime_hours ?? 0;
        $sickDays = $request->sick_days ?? 0;
        $vacationDays = $request->vacation_days ?? 0;
        $otherAccruals = $request->other_accruals ?? 0;
        $includeTradeUnion = $request->include_trade_union ?? false;
        $normDays = $request->base_days ?? 0;
        $normHours = $request->base_hours ?? 0;
        $actualDays = $request->fact_days ?? 0;
        $actualHours = $request->fact_hours ?? 0;
        $baseSalary = $request->base_salary ?? 0;

        // Получаем сотрудников для расчета
        $query = Employee::with(['user', 'department', 'personalInfo'])
            ->where('is_active', true);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employees = $query->get();

        // Выполняем расчет для каждого сотрудника
        $calculations = [];
        $summary = [
            'total_employees' => 0,
            'total_base_salary' => 0,
            'total_bonus' => 0,
            'total_accrued' => 0,
            'total_income_tax' => 0,
            'total_pension_fund' => 0,
            'total_social_security' => 0,
            'total_trade_union' => 0,
            'total_deductions' => 0,
            'total_net_salary' => 0,
        ];

        foreach ($employees as $employee) {
            // Получаем оклад сотрудника
            $employeeBaseSalary = $baseSalary;

            // Расчет премии от оклада
            $bonus = $employeeBaseSalary * ($bonusPercentage / 100);

            // Расчет зарплаты с учетом отработанного времени
            $calculatedBaseSalary = $employeeBaseSalary;
            if ($actualDays > 0 && $normDays > 0) {
                // Пропорциональный расчет оклада за отработанные дни
                $calculatedBaseSalary = ($employeeBaseSalary / $normDays) * $actualDays;
            } elseif ($actualHours > 0 && $normHours > 0) {
                // Пропорциональный расчет оклада за отработанные часы
                $calculatedBaseSalary = ($employeeBaseSalary / $normHours) * $actualHours;
            }

            $additionalData = [
                'base_salary_calculated' => $calculatedBaseSalary,
                'bonus' => $bonus,
                'overtime_hours' => $overtimeHours,
                'sick_days' => $sickDays,
                'vacation_days' => $vacationDays,
                'other_accruals' => $otherAccruals,
                'trade_union' => $includeTradeUnion ? $employeeBaseSalary * 0.01 : 0,
                'norm_days' => $normDays,
                'norm_hours' => $normHours,
                'actual_days' => $actualDays,
                'actual_hours' => $actualHours,
            ];

            $calculation = $this->salaryService->calculateEmployeeSalary($employee, $year, $month, $additionalData);
            $calculations[] = $calculation;

            $summary['total_employees']++;
            $summary['total_base_salary'] += $calculation['base_salary'];
            $summary['total_bonus'] += $calculation['bonus'];
            $summary['total_accrued'] += $calculation['total_accrued'];
            $summary['total_income_tax'] += $calculation['income_tax'];
            $summary['total_pension_fund'] += $calculation['pension_fund'];
            $summary['total_social_security'] += $calculation['social_security'];
            $summary['total_trade_union'] += $calculation['trade_union'];
            $summary['total_deductions'] += $calculation['total_deductions'];
            $summary['total_net_salary'] += $calculation['net_salary'];
        }

        $monthName = $this->getMonthName($month);
        $period = "{$monthName} {$year}";

        return view('salary.partials.preview-table', compact(
            'calculations', 'summary', 'period', 'year', 'month',
            'normDays', 'normHours', 'actualDays', 'actualHours',
            'bonusPercentage', 'includeTradeUnion'
        ));
    }

    /**
     * Сохранение расчета
     */
    public function saveCalculation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'calculations' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $saved = $this->salaryService->saveSalaryCalculations($request->calculations, $request->year, $request->month);

        if ($saved) {
            return response()->json([
                'success' => true,
                'message' => 'Расчёт зарплаты успешно сохранён!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Ошибка при сохранении расчёта'
        ], 500);
    }

    /**
     * Экспорт результатов в Excel/CSV
     */
    public function export($id)
    {
        $calculation = SalaryPayment::with(['employee.user', 'department'])->findOrFail($id);
        $periodCalculations = SalaryPayment::with(['employee.user', 'department'])
            ->where('year', $calculation->year)
            ->where('month', $calculation->month)
            ->get();

        $monthName = $this->getMonthName($calculation->month);

        $csvData = [];
        $csvData[] = ['Расчет заработной платы за', "{$monthName} {$calculation->year}"];
        $csvData[] = ['Сотрудник', $calculation->employee->full_name];
        $csvData[] = ['Должность', $calculation->employee->position];
        $csvData[] = ['Подразделение', $calculation->department->name ?? '—'];
        $csvData[] = ['Дата формирования', Carbon::now()->format('d.m.Y H:i:s')];
        $csvData[] = [];
        $csvData[] = ['Показатель', 'Сумма (BYN)'];
        $csvData[] = ['Оклад', number_format($calculation->base_salary, 2)];
        $csvData[] = ['Премия', number_format($calculation->bonus, 2)];
        $csvData[] = ['Начислено', number_format($calculation->total_accrued, 2)];
        $csvData[] = ['Подоходный налог (13%)', number_format($calculation->income_tax, 2)];
        $csvData[] = ['Пенсионный фонд (ФСЗН 1%)', number_format($calculation->pension_fund, 2)];
        $csvData[] = ['Социальное страхование (0.6%)', number_format($calculation->social_security, 2)];
        $csvData[] = ['Удержано', number_format($calculation->total_deductions, 2)];
        $csvData[] = ['К выплате', number_format($calculation->net_salary, 2)];
        $csvData[] = [];
        $csvData[] = ['Детализация по сотрудникам за период:'];
        $csvData[] = [];
        $csvData[] = ['Сотрудник', 'Должность', 'Подразделение', 'Оклад', 'Премия', 'Начислено', 'Налог', 'ФСЗН', 'Соцстрах', 'Удержано', 'К выплате'];

        foreach ($periodCalculations as $calc) {
            $csvData[] = [
                $calc->employee->full_name,
                $calc->employee->position,
                $calc->department->name ?? '—',
                number_format($calc->base_salary, 2),
                number_format($calc->bonus, 2),
                number_format($calc->total_accrued, 2),
                number_format($calc->income_tax, 2),
                number_format($calc->pension_fund, 2),
                number_format($calc->social_security, 2),
                number_format($calc->total_deductions, 2),
                number_format($calc->net_salary, 2),
            ];
        }

        $csvData[] = [];
        $csvData[] = ['ИТОГО:', '', '', '', '',
            number_format($periodCalculations->sum('total_accrued'), 2),
            number_format($periodCalculations->sum('income_tax'), 2),
            number_format($periodCalculations->sum('pension_fund'), 2),
            number_format($periodCalculations->sum('social_security'), 2),
            number_format($periodCalculations->sum('total_deductions'), 2),
            number_format($periodCalculations->sum('net_salary'), 2),
        ];

        $filename = "salary_{$calculation->employee->full_name}_{$calculation->year}_{$calculation->month}.csv";

        $handle = fopen('php://temp', 'w');
        fwrite($handle, "\xEF\xBB\xBF");

        foreach ($csvData as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Получение сотрудников по подразделению для AJAX
     */
    public function getEmployees(Request $request)
    {
        $employees = Employee::with(['user', 'personalInfo'])
            ->when($request->department_id, function($query, $departmentId) {
                $query->where('department_id', $departmentId);
            })
            ->where('is_active', true)
            ->get()
            ->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'position' => $employee->position,
                    'salary' => $employee->salary,
                    'department_id' => $employee->department_id,
                    'children_count' => $employee->personalInfo->children_count ?? 0,
                ];
            });

        return response()->json(['success' => true, 'employees' => $employees]);
    }

    /**
     * Страница налоговых настроек
     */
    public function taxSettings()
    {
        $taxRates = Cache::remember('tax_rates_active', 86400, function () {
            return TaxRate::orderBy('sort_order')->get();
        });

        return view('salary.tax-settings', compact('taxRates'));
    }

    /**
     * Обновление налоговой ставки
     */
    public function updateTaxRate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rate_value' => 'required|numeric|min:0|max:100',
            'effective_from' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $taxRate = TaxRate::findOrFail($id);
            $taxRate->update(['is_active' => false]);

            TaxRate::create([
                'code' => $taxRate->code . '_' . date('Ymd'),
                'name' => $taxRate->name,
                'category' => $taxRate->category,
                'calculation_type' => $taxRate->calculation_type,
                'rate_value' => $request->rate_value,
                'rate_unit' => $taxRate->rate_unit,
                'is_active' => true,
                'effective_from' => $request->effective_from,
                'sort_order' => $taxRate->sort_order,
                'description' => $request->description ?? $taxRate->description,
            ]);

            CacheService::flushSalary();

            return response()->json(['success' => true, 'message' => 'Ставка обновлена!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Обновление настройки вычета
     */
    public function updateDeductionSetting(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
        'rate_value' => 'required|numeric|min:0|max:100',
        'effective_from' => 'required|date',
    ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $taxRate = TaxRate::findOrFail($id);
            $taxRate->update(['is_active' => false]);

            TaxRate::create([
                'code' => $taxRate->code . '_' . date('Ymd'),
                'name' => $taxRate->name,
                'category' => $taxRate->category,
                'calculation_type' => $taxRate->calculation_type,
                'rate_value' => $request->rate_value,
                'rate_unit' => $taxRate->rate_unit,
                'is_active' => true,
                'effective_from' => $request->effective_from,
                'sort_order' => $taxRate->sort_order,
                'description' => $request->description ?? $taxRate->description,
            ]);

            return response()->json(['success' => true, 'message' => 'Ставка обновлена!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function calculateSummary($calculations)
    {
        return [
            'total_employees' => $calculations->count(),
            'total_accrued' => $calculations->sum('total_accrued'),
            'total_income_tax' => $calculations->sum('income_tax'),
            'total_pension_fund' => $calculations->sum('pension_fund'),
            'total_social_security' => $calculations->sum('social_security'),
            'total_trade_union' => $calculations->sum('trade_union'),
            'total_deductions' => $calculations->sum('total_deductions'),
            'total_net_salary' => $calculations->sum('net_salary'),
        ];
    }

    private function getMonthName($month)
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март',
            4 => 'Апрель', 5 => 'Май', 6 => 'Июнь',
            7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь',
            10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];
        return $months[$month] ?? '';
    }

    /**
     * Получить данные для расчетного листа (AJAX)
     */
    public function getPayslipData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
            'base_salary' => 'required|numeric|min:0',
            'norm_days' => 'nullable|numeric',
            'norm_hours' => 'nullable|numeric',
            'actual_days' => 'nullable|numeric',
            'actual_hours' => 'nullable|numeric',
            'bonus_percentage' => 'nullable|numeric',
            'overtime_hours' => 'nullable|numeric',
            'sick_days' => 'nullable|numeric',
            'vacation_days' => 'nullable|numeric',
            'other_accruals' => 'nullable|numeric',
            'include_trade_union' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $employee = Employee::with(['user', 'department', 'personalInfo'])->findOrFail($request->employee_id);
        $year = $request->year;
        $month = $request->month;

        $bonusPercentage = $request->bonus_percentage ?? 0;
        $includeTradeUnion = $request->include_trade_union ?? false;
        $normDays = $request->norm_days ?? 21;
        $normHours = $request->norm_hours ?? 168;
        $actualDays = $request->actual_days ?? $normDays;
        $actualHours = $request->actual_hours ?? $normHours;

        $baseSalary = $request->base_salary;

        // Расчет оклада с учетом отработанного времени
        $calculatedBaseSalary = $baseSalary;
        if ($actualDays > 0 && $normDays > 0 && $actualDays != $normDays) {
            $calculatedBaseSalary = ($baseSalary / $normDays) * $actualDays;
        } elseif ($actualHours > 0 && $normHours > 0 && $actualHours != $normHours) {
            $calculatedBaseSalary = ($baseSalary / $normHours) * $actualHours;
        }

        // Часовая ставка для сверхурочных
        $hourlyRate = $baseSalary / $normHours;
        $overtimePay = ($request->overtime_hours ?? 0) * $hourlyRate * 1.5;

        // Дневная ставка для больничных и отпускных
        $dailyRate = $baseSalary / $normDays;
        $sickLeave = ($request->sick_days ?? 0) * $dailyRate * 0.8;
        $vacationPay = ($request->vacation_days ?? 0) * $dailyRate;

        $bonus = $baseSalary * ($bonusPercentage / 100);
        $tradeUnion = $includeTradeUnion ? $baseSalary * 0.01 : 0;
        $otherAccruals = $request->other_accruals ?? 0;

        $totalAccrued = $calculatedBaseSalary + $bonus + $overtimePay + $sickLeave + $vacationPay + $otherAccruals;

        // Налоговые вычеты
        $taxDeductions = 0;
        $taxDeductionsDetails = [];

        if ($employee->personalInfo && $employee->personalInfo->children_count > 0) {
            $childDeduction = $this->salaryService->getDeductionAmount('CHILD_1');
            if ($childDeduction > 0) {
                $taxDeductions += $childDeduction;
                $taxDeductionsDetails['child'] = [
                    'name' => 'Вычет на ребенка',
                    'amount' => $childDeduction
                ];
            }
        }

        if ($employee->personalInfo && $employee->personalInfo->mortgage_amount > 0) {
            $mortgageDeduction = min($employee->personalInfo->mortgage_amount * 0.13, 78100);
            if ($mortgageDeduction > 0) {
                $taxDeductions += $mortgageDeduction;
                $taxDeductionsDetails['mortgage'] = [
                    'name' => 'Имущественный вычет (ипотека)',
                    'amount' => $mortgageDeduction
                ];
            }
        }

        $taxableIncome = max(0, $totalAccrued - $taxDeductions);
        $incomeTax = round($taxableIncome * 0.13, 2);
        $pensionFund = round($totalAccrued * 0.01, 2);
        $socialSecurity = round($totalAccrued * 0.006, 2);

        $totalDeductions = $incomeTax + $pensionFund + $socialSecurity + $tradeUnion;
        $netSalary = $totalAccrued - $totalDeductions;

        $calculation = [
            'employee_id' => $employee->id,
            'employee_name' => $employee->full_name,
            'position' => $employee->position,
            'department_name' => $employee->department->name ?? 'Не назначено',
            'hire_date' => $employee->hire_date ? $employee->hire_date->format('d.m.Y') : '—',
            'norm_days' => $normDays,
            'norm_hours' => $normHours,
            'actual_days' => $actualDays,
            'actual_hours' => $actualHours,
            'overtime_hours' => $request->overtime_hours ?? 0,
            'sick_days' => $request->sick_days ?? 0,
            'vacation_days' => $request->vacation_days ?? 0,
            'bonus_percentage' => $bonusPercentage,
            'base_salary_input' => $baseSalary,
            'base_salary_calculated' => $calculatedBaseSalary,
            'bonus' => $bonus,
            'overtime_pay' => $overtimePay,
            'sick_leave' => $sickLeave,
            'vacation_pay' => $vacationPay,
            'other_accruals' => $otherAccruals,
            'total_accrued' => $totalAccrued,
            'total_tax_deductions' => $taxDeductions,
            'tax_deductions_details' => $taxDeductionsDetails,
            'income_tax' => $incomeTax,
            'pension_fund' => $pensionFund,
            'social_security' => $socialSecurity,
            'trade_union' => $tradeUnion,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
        ];

        $settings = $this->getCompanySettings();
        $monthName = $this->getMonthName($month);

        return view('salary.payslip-print', compact('calculation', 'year', 'month', 'monthName', 'settings'));
    }

    /**
     * Получить настройки компании из .env
     */
    private function getCompanySettings()
    {
        $settings = [];
        $envFile = file(base_path('.env'));
        foreach ($envFile as $line) {
            if (strpos($line, 'COMPANY_') === 0) {
                $parts = explode('=', $line, 2);
                $key = $parts[0];
                $value = isset($parts[1]) ? trim($parts[1], " \n\r\"'") : '';
                $settings[$key] = $value;
            }
        }

        // Значения по умолчанию
        if (empty($settings['COMPANY_NAME'])) $settings['COMPANY_NAME'] = config('app.name', 'SalaryCalc BY');
        if (empty($settings['COMPANY_ADDRESS'])) $settings['COMPANY_ADDRESS'] = 'г. Минск, ул. Компьютерная, 15';
        if (empty($settings['COMPANY_TAX_ID'])) $settings['COMPANY_TAX_ID'] = '123456789';

        return $settings;
    }

    /**
     * Получить информацию об имущественном вычете сотрудника
     */
    public function getMortgageInfo(int $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $info = $this->salaryService->getMortgageDeductionInfo($employeeId);

        return response()->json([
            'success' => true,
            'employee' => $employee->full_name,
            'mortgage_info' => $info
        ]);
    }

    /**
     * Сохранить имущественный вычет для сотрудника
     */
    public function saveMortgageDeduction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'property_cost' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Если стоимость 0 - удаляем вычет
            if ($request->property_cost == 0) {
                // Деактивируем активные вычеты
                \App\Models\Employee\EmployeeMortgageDeduction::where('employee_id', $request->employee_id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);

                $message = 'Имущественный вычет успешно удален';
            } else {
                // Создаем или обновляем вычет
                $mortgage = $this->salaryService->createOrUpdateMortgageDeduction(
                    $request->employee_id,
                    $request->property_cost,
                    $request->start_date
                );
                $message = 'Имущественный вычет успешно сохранен';
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('salary.mortgage.edit', $request->employee_id)
                ->with('success', $message);

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при сохранении: ' . $e->getMessage()
                ], 500);
            }

            return back()->with('error', 'Ошибка при сохранении: ' . $e->getMessage());
        }
    }

    /**
     * Страница редактирования имущественного вычета сотрудника
     */
    public function mortgageEdit(int $employeeId)
    {
        $employee = Employee::with(['department'])->findOrFail($employeeId);
        $mortgageInfo = $this->salaryService->getMortgageDeductionInfo($employeeId);

        return view('salary.mortgage.edit', compact('employee', 'mortgageInfo'));
    }

    public function mortgageList()
    {
        $employees = Employee::with(['department', 'activeMortgageDeduction'])
            ->where('is_active', true)
            ->paginate(20);

        return view('salary.mortgage.index', compact('employees'));
    }
}
