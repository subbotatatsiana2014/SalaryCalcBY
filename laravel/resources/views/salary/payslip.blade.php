<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Расчетный листок за {{ $monthName }} {{ $year }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
            color: #000;
            background: #fff;
            padding: 15mm;
        }

        .payslip {
            max-width: 210mm;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 8mm;
        }

        .header {
            text-align: center;
            margin-bottom: 12px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .company-name {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .document-title {
            font-size: 13pt;
            font-weight: bold;
            margin: 8px 0;
        }

        .employee-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .employee-info td {
            padding: 4px 6px;
            border: 1px solid #000;
            vertical-align: top;
        }

        .employee-info td:first-child {
            width: 30%;
            font-weight: bold;
            background: #f5f5f5;
        }

        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin: 12px 0 8px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #000;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .table th,
        .table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }

        .table th {
            background: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }

        .table td.text-right {
            text-align: right;
        }

        .table td.text-center {
            text-align: center;
        }

        .total-row {
            font-weight: bold;
            background: #fafafa;
        }

        .signature {
            margin-top: 25px;
            display: flex;
            justify-content: space-between;
        }

        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin-top: 25px;
            text-align: center;
            font-size: 9pt;
        }

        .footer {
            margin-top: 15px;
            font-size: 8pt;
            text-align: center;
            color: #666;
        }

        .print-date {
            text-align: right;
            font-size: 8pt;
            margin-bottom: 8px;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .payslip {
                border: none;
                padding: 5mm;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="payslip">
    <div class="print-date no-print">
        <button onclick="window.print();" style="padding: 5px 15px; cursor: pointer;">🖨️ Распечатать</button>
        <button onclick="window.close();" style="padding: 5px 15px; margin-left: 10px; cursor: pointer;">✖ Закрыть</button>
    </div>

    <div class="header">
        <div class="company-name">{{ $settings['COMPANY_NAME'] ?? config('app.name', 'SalaryCalc BY') }}</div>
        <div>{{ $settings['COMPANY_ADDRESS'] ?? 'г. Минск, ул. Компьютерная, 15' }}</div>
        <div>УНП: {{ $settings['COMPANY_TAX_ID'] ?? '123456789' }}</div>
        <div class="document-title">РАСЧЕТНЫЙ ЛИСТОК</div>
        <div>за {{ $monthName }} {{ $year }} года</div>
    </div>

    <!-- Информация о сотруднике -->
    <table class="employee-info">
        <tr>
            <td>Фамилия, имя, отчество</td>
            <td colspan="3"><strong>{{ $calculation['employee_name'] }}</strong></td>
        </tr>
        <tr>
            <td>Табельный номер</td>
            <td>{{ $calculation['employee_id'] }}</td>
            <td>Дата приема</td>
            <td>{{ $calculation['hire_date'] ?? '—' }}</td>
        </tr>
        <tr>
            <td>Подразделение</td>
            <td colspan="3">{{ $calculation['department_name'] }}</td>
        </tr>
        <tr>
            <td>Должность (профессия)</td>
            <td colspan="3">{{ $calculation['position'] }}</td>
        </tr>
    </table>

    <!-- Начислено -->
    <div class="section-title">НАЧИСЛЕНО</div>
    <table class="table">
        <thead>
        <tr>
            <th>Вид начисления</th>
            <th>Количество</th>
            <th>Сумма, BYN</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Оклад</td>
            <td class="text-center">
                @if(($calculation['actual_days'] ?? 0) > 0)
                    {{ $calculation['actual_days'] }} / {{ $calculation['norm_days'] ?? 22 }} дн.
                @elseif(($calculation['actual_hours'] ?? 0) > 0)
                    {{ $calculation['actual_hours'] }} / {{ $calculation['norm_hours'] ?? 168 }} ч.
                @else
                    {{ $calculation['norm_days'] ?? 22 }} дн.
                @endif
            </td>
            <td class="text-right">{{ number_format($calculation['base_salary_calculated'], 2) }} (@if($calculation['base_salary_input'] != $calculation['base_salary_calculated']) оклад {{ number_format($calculation['base_salary_input'], 2) }} @endif)</td>
        </tr>
        <tr>
            <td>Премия ({{ $calculation['bonus_percentage'] }}%)</td>
            <td class="text-center">—</td>
            <td class="text-right">{{ number_format($calculation['bonus'], 2) }}</td>
        </tr>
        @if(($calculation['overtime_hours'] ?? 0) > 0)
            <tr>
                <td>Сверхурочные</td>
                <td class="text-center">{{ $calculation['overtime_hours'] }} час.</td>
                <td class="text-right">{{ number_format($calculation['overtime_pay'], 2) }}</td>
            </tr>
        @endif
        @if(($calculation['sick_days'] ?? 0) > 0)
            <tr>
                <td>Пособие по временной нетрудоспособности</td>
                <td class="text-center">{{ $calculation['sick_days'] }} дн.</td>
                <td class="text-right">{{ number_format($calculation['sick_leave'], 2) }}</td>
            </tr>
        @endif
        @if(($calculation['vacation_days'] ?? 0) > 0)
            <tr>
                <td>Отпускные</td>
                <td class="text-center">{{ $calculation['vacation_days'] }} дн.</td>
                <td class="text-right">{{ number_format($calculation['vacation_pay'], 2) }}</td>
            </tr>
        @endif
        @if(($calculation['other_accruals'] ?? 0) > 0)
            <tr>
                <td>Другие начисления</td>
                <td class="text-center">—</td>
                <td class="text-right">{{ number_format($calculation['other_accruals'], 2) }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td colspan="2"><strong>ИТОГО начислено</strong></td>
            <td class="text-right"><strong>{{ number_format($calculation['total_accrued'], 2) }}</strong></td>
        </tr>
        </tbody>
    </table>

    <!-- Удержано -->
    <div class="section-title">УДЕРЖАНО</div>
    <table class="table">
        <thead>
        <tr>
            <th>Вид удержания</th>
            <th>Процент/Основание</th>
            <th>Сумма, BYN</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Подоходный налог</td>
            <td class="text-center">13%</td>
            <td class="text-right">{{ number_format($calculation['income_tax'], 2) }}</td>
        </tr>
        <tr>
            <td>Взносы в ФСЗН (пенсионное страхование)</td>
            <td class="text-center">1%</td>
            <td class="text-right">{{ number_format($calculation['pension_fund'], 2) }}</td>
        </tr>
        <tr>
            <td>Взносы на социальное страхование</td>
            <td class="text-center">0.6%</td>
            <td class="text-right">{{ number_format($calculation['social_security'], 2) }}</td>
        </tr>
        @if(($calculation['trade_union'] ?? 0) > 0)
            <tr>
                <td>Профсоюзные взносы</td>
                <td class="text-center">1%</td>
                <td class="text-right">{{ number_format($calculation['trade_union'], 2) }}</td>
            </tr>
        @endif
        <tr class="total-row">
            <td colspan="2"><strong>ИТОГО удержано</strong></td>
            <td class="text-right"><strong>{{ number_format($calculation['total_deductions'], 2) }}</strong></td>
        </tr>
        </tbody>
    </table>

    <!-- Налоговые вычеты -->
    @if(!empty($calculation['tax_deductions_details']) && count($calculation['tax_deductions_details']) > 0)
        <div class="section-title">НАЛОГОВЫЕ ВЫЧЕТЫ</div>
        <table class="table">
            <thead>
            <tr><th>Основание для вычета</th><th>Сумма, BYN</th></tr>
            </thead>
            <tbody>
            @foreach($calculation['tax_deductions_details'] as $deduction)
                <tr><td>{{ $deduction['name'] }}</td><td class="text-right">{{ number_format($deduction['amount'], 2) }}</td></tr>
            @endforeach
            <tr class="total-row"><td><strong>ИТОГО вычетов</strong></td><td class="text-right"><strong>{{ number_format($calculation['total_tax_deductions'], 2) }}</strong></td></tr>
            </tbody>
        </table>
    @endif

    <!-- Итог к выплате -->
    <table class="table" style="margin-top: 12px;">
        <tbody>
        <tr style="background: #e8f5e9;">
            <td style="width: 70%; font-weight: bold; font-size: 13pt;">ИТОГО К ВЫПЛАТЕ</td>
            <td style="width: 30%; text-align: right; font-weight: bold; font-size: 13pt;">
                {{ number_format($calculation['net_salary'], 2) }} BYN
            </td>
        </tr>
        </tbody>
    </table>

    <!-- Подписи -->
    <div class="signature">
        <div class="signature-line">
            Руководитель организации<br>
            ___________________ И.О. Фамилия
        </div>
        <div class="signature-line">
            Главный бухгалтер<br>
            ___________________ И.О. Фамилия
        </div>
    </div>

    <div class="footer">
        Расчетный листок сформирован {{ now()->format('d.m.Y H:i') }}
    </div>
</div>
</body>
</html>
