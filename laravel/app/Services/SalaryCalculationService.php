<?php

namespace App\Services;

use App\Models\Employee\Employee;
use App\Models\Salary\SalaryPayment;
use App\Models\Employee\EmployeeMortgageDeduction;
use App\Models\Salary\TaxPeriod;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryCalculationService
{
    private const INCOME_TAX_RATE = 13;      // Подоходный налог 13%
    private const PENSION_FUND_RATE = 1;     // Пенсионный фонд (ФСЗН) 1%
    private const TRADE_UNION_RATE = 1;       // Профсоюзные взносы 1%

    private const DEDUCTION_STANDARD = 0;        // Стандартный вычет (пока 0)
    private const DEDUCTION_FIRST_CHILD = 63;    // Вычет на первого ребенка
    private const DEDUCTION_OTHER_CHILD = 120;   // Вычет на второго и последующих детей
    private const DEDUCTION_DISABLED_CHILD = 120; // Вычет на ребенка-инвалида
    private const DEDUCTION_STUDENT = 63;        // Вычет на ребенка-студента

    // Максимальная сумма для имущественного вычета (лимит 78100 BYN - 13% = 10153 BYN возврата)
    private const MORTGAGE_MAX_BASE = 78100;

    // Минимальная заработная плата в РБ (вынести в настройки)
    protected $minSalary = 858;

    /**
     * Расчет зарплаты для одного сотрудника
     */
    public function calculateEmployeeSalary(Employee $employee, int $year, int $month, array $additionalData = []): array
    {
        $baseSalary = $employee->salary;

        // Начисления
        $bonus = $additionalData['bonus'] ?? 0;
        $overtimePay = $additionalData['overtime_pay'] ?? 0;
        $sickLeave = $additionalData['sick_leave'] ?? 0;
        $vacationPay = $additionalData['vacation_pay'] ?? 0;
        $otherAccruals = $additionalData['other_accruals'] ?? 0;

        // Итого начислено
        $totalAccrued = $baseSalary + $bonus + $overtimePay + $sickLeave + $vacationPay + $otherAccruals;

        // Расчет налоговых вычетов
        $taxDeductions = $this->calculateTaxDeductions($employee, $year, $month);
        $totalTaxDeductions = $taxDeductions['total'];

        // Расчет рабочего времени
        $normDays = $additionalData['norm_days'] ?? 21;
        $normHours = $additionalData['norm_hours'] ?? 168;
        $actualWorkDays = $additionalData['actual_days'] ?? $normDays;
        $actualWorkHours  = $additionalData['actual_hours'] ?? $normHours;

        // Расчет налогооблагаемого дохода
        $taxableIncome = max(0, $totalAccrued - $totalTaxDeductions);

        // Расчет имущественного вычета (из БД)
        $mortgageDeduction = $this->calculateMortgageDeductionFromDB($employee);

        // Применяем имущественный вычет
        $incomeTaxBeforeMortgage = $this->calculateIncomeTax($taxableIncome);
        $incomeTax = max(0, $incomeTaxBeforeMortgage - $mortgageDeduction['current_deduction']);

        // Расчет остальных налогов и отчислений
        $pensionFund = $this->calculatePensionFund($totalAccrued);

        // Профсоюзные взносы (только если включено в настройках)
        $tradeUnion = $additionalData['trade_union'] ?? 0;

        // Другие удержания (алименты, кредиты и т.д.)
        $otherDeductions = $additionalData['other_deductions'] ?? 0;

        // Итого удержано
        $totalDeductions = $incomeTax + $pensionFund + $tradeUnion + $otherDeductions;
        // Чистая зарплата к выплате
        $netSalary = $totalAccrued - $totalDeductions;

        // Проверка на минимальную зарплату
        if ($netSalary < $this->minSalary && $actualWorkHours >= $normDays) {
           $netSalary = $this->minSalary;
        }

        // Формируем результат
        return [
            'employee_id' => $employee->id,
            'employee_name' => $employee->full_name,
            'position' => $employee->position,
            'department_id' => $employee->department_id,
            'department_name' => $employee->department->name ?? 'Не назначено',
            'year' => $year,
            'month' => $month,

            // Начисления
            'base_salary' => $baseSalary,
            'bonus' => $bonus,
            'overtime_pay' => $overtimePay,
            'sick_leave' => $sickLeave,
            'vacation_pay' => $vacationPay,
            'other_accruals' => $otherAccruals,
            'total_accrued' => $totalAccrued,

            // Информация о рабочем времени
            'work_days_norm' => $normDays,
            'work_days_actual' => $actualWorkDays,
            'work_hours_norm' => $normHours,
            'work_hours_actual' => $actualWorkHours,

            // Налоговые вычеты
            'total_tax_deductions' => $totalTaxDeductions,
            'tax_deductions_details' => $taxDeductions['details'],

            // Налоги и удержания
            'taxable_income' => $taxableIncome,
            'income_tax' => $incomeTax,
            'income_tax_rate' => self::INCOME_TAX_RATE,
            'pension_fund' => $pensionFund,
            'pension_fund_rate' => self::PENSION_FUND_RATE,
            'trade_union' => $tradeUnion,
            'trade_union_rate' => self::TRADE_UNION_RATE,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,

            // Итог к выплате
            'net_salary' => $netSalary,

            // Дополнительная информация
            'children_info' => [
                'count' => $employee->personalInfo->children_count ?? 0,
                'students' => $employee->personalInfo->students_count ?? 0,
                'has_disabled' => $employee->personalInfo->has_disabled_child ?? false,
            ],
        ];
    }

    /**
     * Расчет налоговых вычетов для сотрудника
     */
    protected function calculateTaxDeductions(Employee $employee, int $year, int $month): array
    {
        $personalInfo = $employee->personalInfo;

        // Получаем данные из личной карточки сотрудника
        $childrenCount = (int) ($personalInfo->children_count ?? 0);
        $studentsCount = (int) ($personalInfo->students_count ?? 0);
        $hasDisabledChild = (bool) ($personalInfo->has_disabled_child ?? false);

        $totalDeduction = 0.0;
        $details = [];

        // 1. Стандартный вычет (если есть)
        if (self::DEDUCTION_STANDARD > 0) {
            $totalDeduction += self::DEDUCTION_STANDARD;
            $details['standard'] = [
                'name' => 'Стандартный вычет',
                'amount' => self::DEDUCTION_STANDARD,
                'type' => 'standard'
            ];
        }

        // 2. Вычет на первого ребенка
        if ($childrenCount >= 1 && self::DEDUCTION_FIRST_CHILD > 0) {
            $totalDeduction += self::DEDUCTION_FIRST_CHILD;
            $details['child_first'] = [
                'name' => 'Вычет на первого ребенка',
                'amount' => self::DEDUCTION_FIRST_CHILD,
                'type' => 'child',
                'child_number' => 1
            ];
        }

        // 3. Вычет на второго и последующих детей
        if ($childrenCount > 1 && self::DEDUCTION_OTHER_CHILD > 0) {
            $additionalChildren = $childrenCount - 1;
            $amount = self::DEDUCTION_OTHER_CHILD * $additionalChildren;
            $totalDeduction += $amount;
            $details['child_other'] = [
                'name' => 'Вычет на следующих детей',
                'amount' => $amount,
                'type' => 'child',
                'children_count' => $additionalChildren,
                'per_child' => self::DEDUCTION_OTHER_CHILD
            ];
        }

        // 4. Вычет на детей-студентов
        if ($studentsCount > 0 && self::DEDUCTION_STUDENT > 0) {
            $amount = self::DEDUCTION_STUDENT * $studentsCount;
            $totalDeduction += $amount;
            $details['student'] = [
                'name' => 'Вычет на обучающихся детей',
                'amount' => $amount,
                'type' => 'student',
                'students_count' => $studentsCount
            ];
        }

        // 5. Вычет на ребенка-инвалида
        if ($hasDisabledChild && self::DEDUCTION_DISABLED_CHILD > 0) {
            $totalDeduction += self::DEDUCTION_DISABLED_CHILD;
            $details['disabled_child'] = [
                'name' => 'Вычет на ребенка-инвалида',
                'amount' => self::DEDUCTION_DISABLED_CHILD,
                'type' => 'disabled_child'
            ];
        }

        return [
            'total' => round($totalDeduction, 2),
            'details' => $details
        ];
    }


    /**
     * Расчет имущественного вычета из базы данных
     */
    protected function calculateMortgageDeductionFromDB(Employee $employee): array
    {
        // Получаем активный имущественный вычет сотрудника
        $mortgageDeduction = EmployeeMortgageDeduction::getActiveForEmployee($employee->id);

        // Если вычета нет или остаток 0 - не применяем
        if (!$mortgageDeduction || $mortgageDeduction->remaining_deduction <= 0) {
            return [
                'is_active' => false,
                'property_cost' => 0,
                'total_possible_deduction' => 0,
                'used_deduction_before' => 0,
                'current_deduction' => 0,
                'remaining_deduction' => 0,
                'message' => 'Имущественный вычет не активен или полностью использован'
            ];
        }

        return [
            'is_active' => true,
            'id' => $mortgageDeduction->id,
            'property_cost' => $mortgageDeduction->property_cost,
            'total_possible_deduction' => $mortgageDeduction->total_possible_deduction,
            'used_deduction_before' => $mortgageDeduction->used_deduction,
            'remaining_deduction' => $mortgageDeduction->remaining_deduction,
            'current_deduction' => 0,
            'message' => 'Имущественный вычет активен'
        ];
    }

    /**
     * Применить имущественный вычет к налогу (вызывать после расчета налога)
     */
    public function applyMortgageDeduction(int $employeeId, float $incomeTaxAmount): array
    {
        $mortgageDeduction = EmployeeMortgageDeduction::getActiveForEmployee($employeeId);

        if (!$mortgageDeduction || $mortgageDeduction->remaining_deduction <= 0) {
            return [
                'income_tax' => $incomeTaxAmount,
                'deduction_applied' => 0,
                'remaining_deduction' => $mortgageDeduction ? $mortgageDeduction->remaining_deduction : 0
            ];
        }

        // Текущий вычет не может быть больше налога за месяц и не может превышать остаток
        $currentDeduction = min($incomeTaxAmount, $mortgageDeduction->remaining_deduction);

        // Обновляем остаток в БД
        $newRemainingDeduction = $mortgageDeduction->remaining_deduction - $currentDeduction;
        $newUsedDeduction = $mortgageDeduction->used_deduction + $currentDeduction;

        $mortgageDeduction->update([
            'used_deduction' => $newUsedDeduction,
            'remaining_deduction' => $newRemainingDeduction,
            'end_date' => $newRemainingDeduction <= 0 ? now() : null,
            'is_active' => $newRemainingDeduction > 0,
        ]);

        return [
            'income_tax' => round($incomeTaxAmount - $currentDeduction, 2),
            'deduction_applied' => round($currentDeduction, 2),
            'remaining_deduction' => round($newRemainingDeduction, 2),
            'total_used' => round($newUsedDeduction, 2)
        ];
    }
    /**
     * Получить информацию об имущественном вычете сотрудника
     */
    public function getMortgageDeductionInfo(int $employeeId): ?array
    {
        $deduction = EmployeeMortgageDeduction::getActiveForEmployee($employeeId);

        if (!$deduction) {
            return null;
        }

        return [
            'id' => $deduction->id,
            'property_cost' => $deduction->property_cost,
            'total_possible_deduction' => $deduction->total_possible_deduction,
            'used_deduction' => $deduction->used_deduction,
            'remaining_deduction' => $deduction->remaining_deduction,
            'start_date' => $deduction->start_date->format('d.m.Y'),
            'end_date' => $deduction->end_date?->format('d.m.Y'),
        ];
    }

    /**
     * Расчет подоходного налога (13%)
     */
    private function calculateIncomeTax(float $taxableIncome): float
    {
        return round($taxableIncome * (self::INCOME_TAX_RATE / 100), 2);
    }

    /**
     * Расчет взносов в пенсионный фонд (ФСЗН 1%)
     */
    private function calculatePensionFund(float $income): float
    {
        return round($income * (self::PENSION_FUND_RATE / 100), 2);
    }

    /**
     * Сохранение результатов расчета в базу данных
     */
    public function saveSalaryCalculations(array $calculations, int $year, int $month): bool
    {
        try {
            DB::beginTransaction();

            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            $taxPeriod = TaxPeriod::firstOrCreate(
                [
                    'year' => $year,
                    'month' => $month,
                    'period_type' => 'monthly',
                ],
                [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_active' => true,
                ]
            );

            foreach ($calculations as $calc) {
                // Применяем имущественный вычет к рассчитанному налогу
                $mortgageResult = $this->applyMortgageDeduction(
                    $calc['employee_id'],
                    $calc['income_tax']
                );

                SalaryPayment::updateOrCreate(
                    [
                        'employee_id' => $calc['employee_id'],
                        'year' => $year,
                        'month' => $month,
                    ],
                    [
                        'department_id' => $calc['department_id'] ?? null,
                        'tax_period_id' => $taxPeriod->id,
                        'base_salary' => $calc['base_salary'],
                        'bonus' => $calc['bonus'],
                        'overtime_pay' => $calc['overtime_pay'] ?? 0,
                        'sick_leave' => $calc['sick_leave'] ?? 0,
                        'vacation_pay' => $calc['vacation_pay'] ?? 0,
                        'other_accruals' => $calc['other_accruals'] ?? 0,
                        'total_accrued' => $calc['total_accrued'],
                        'total_tax_deductions' => $calc['total_tax_deductions'] ?? 0,
                        'tax_deductions_details' => json_encode($calc['tax_deductions_details'] ?? []),
                        'income_tax' => $mortgageResult['income_tax'],
                        'pension_fund' => $calc['pension_fund'],
                        'social_security' => 0, // Соцстрах не используется в РБ для работника
                        'trade_union' => $calc['trade_union'] ?? 0,
                        'other_deductions' => $calc['other_deductions'] ?? 0,
                        'total_deductions' => $mortgageResult['income_tax'] + $calc['pension_fund'] + ($calc['trade_union'] ?? 0) + ($calc['other_deductions'] ?? 0),
                        'net_salary' => $calc['total_accrued'] - ($mortgageResult['income_tax'] + $calc['pension_fund'] + ($calc['trade_union'] ?? 0) + ($calc['other_deductions'] ?? 0)),
                        'status' => 'calculated',
                        'tax_rates_snapshot' => json_encode([
                            'income_tax' => self::INCOME_TAX_RATE,
                            'pension_fund' => self::PENSION_FUND_RATE,
                            'trade_union' => self::TRADE_UNION_RATE,
                        ]),
                        'notes' => $calc['notes'] ?? null,
                    ]
                );
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salary calculation save error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Экспорт расчетов в формат для печати
     */
    public function exportForPrint(array $calculations, array $summary, int $year, int $month): array
    {
        $monthName = $this->getMonthName($month);

        return [
            'period' => "{$monthName} {$year}",
            'calculations' => $calculations,
            'summary' => $summary,
            'generated_at' => Carbon::now()->format('d.m.Y H:i:s'),
            'tax_rates' => $this->getTaxRatesForDisplay(),
        ];
    }

    /**
     * Получение названия месяца
     */
    protected function getMonthName(int $month): string
    {
        $months = [
            1 => 'Январь', 2 => 'Февраль', 3 => 'Март',
            4 => 'Апрель', 5 => 'Май', 6 => 'Июнь',
            7 => 'Июль', 8 => 'Август', 9 => 'Сентябрь',
            10 => 'Октябрь', 11 => 'Ноябрь', 12 => 'Декабрь'
        ];

        return $months[$month] ?? '';
    }
}
