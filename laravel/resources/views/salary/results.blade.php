@extends('layouts.app')

@section('title', 'Результаты расчета зарплаты - SalaryCalc BY')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div><h3><i class="fas fa-chart-bar"></i> Результаты расчета за {{ $calculation->getMonthNameAttribute() }} {{ $calculation->year }}</h3></div>
                    <div>
                        <a href="{{ route('salary.export', $calculation->id) }}" class="btn btn-success"><i class="fas fa-file-excel"></i> Экспорт</a>
                        <a href="{{ route('salary.history') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Назад</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Сводка -->
                <div class="summary-cards">
                    <div class="summary-card"><div class="summary-value">{{ $periodCalculations->count() }}</div><div class="summary-label">Сотрудников</div></div>
                    <div class="summary-card"><div class="summary-value">{{ number_format($summary['total_accrued'], 2) }} BYN</div><div class="summary-label">Всего начислено</div></div>
                    <div class="summary-card"><div class="summary-value">{{ number_format($summary['total_income_tax'], 2) }} BYN</div><div class="summary-label">Подоходный налог</div></div>
                    <div class="summary-card"><div class="summary-value">{{ number_format($summary['total_net_salary'], 2) }} BYN</div><div class="summary-label">К выплате</div></div>
                </div>

                <!-- Таблица расчетов -->
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr><th>Сотрудник</th><th>Должность</th><th>Оклад</th><th>Премия</th><th>Начислено</th><th>Налог(13%)</th><th>ФСЗН(1%)</th><th>Соцстрах</th><th>Удержано</th><th>К выплате</th></tr>
                        </thead>
                        <tbody>
                        @foreach($periodCalculations as $calc)
                            <tr>
                                <td>{{ $calc->employee->full_name }}<br><small>{{ $calc->employee->position }}</small></td>
                                <td>{{ $calc->employee->position }}</td>
                                <td class="text-end">{{ number_format($calc->base_salary, 2) }}</td>
                                <td class="text-end">{{ number_format($calc->bonus, 2) }}</td>
                                <td class="text-end"><strong>{{ number_format($calc->total_accrued, 2) }}</strong></td>
                                <td class="text-end text-danger">{{ number_format($calc->income_tax, 2) }}</td>
                                <td class="text-end">{{ number_format($calc->pension_fund, 2) }}</td>
                                <td class="text-end">{{ number_format($calc->social_security, 2) }}</td>
                                <td class="text-end">{{ number_format($calc->total_deductions, 2) }}</td>
                                <td class="text-end text-success"><strong>{{ number_format($calc->net_salary, 2) }}</strong></td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr><th colspan="4" class="text-end">ИТОГО:</th>
                            <th class="text-end">{{ number_format($summary['total_accrued'], 2) }}</th>
                            <th class="text-end">{{ number_format($summary['total_income_tax'], 2) }}</th>
                            <th class="text-end">{{ number_format($summary['total_pension_fund'], 2) }}</th>
                            <th class="text-end">{{ number_format($summary['total_social_security'], 2) }}</th>
                            <th class="text-end">{{ number_format($summary['total_deductions'], 2) }}</th>
                            <th class="text-end">{{ number_format($summary['total_net_salary'], 2) }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6"><div class="info-card"><div class="info-card-header"><i class="fas fa-chart-pie"></i><h4>Налоги и отчисления</h4></div><div class="info-card-content">
                                <p>Подоходный налог (13%): <strong>{{ number_format($summary['total_income_tax'], 2) }} BYN</strong></p>
                                <p>ФСЗН (1%): <strong>{{ number_format($summary['total_pension_fund'], 2) }} BYN</strong></p>
                                <p>Социальное страхование (0.6%): <strong>{{ number_format($summary['total_social_security'], 2) }} BYN</strong></p>
                                <p>Профсоюзные взносы: <strong>{{ number_format($summary['total_trade_union'], 2) }} BYN</strong></p>
                            </div></div></div>
                    <div class="col-md-6"><div class="info-card"><div class="info-card-header"><i class="fas fa-print"></i><h4>Действия</h4></div><div class="info-card-content">
                                <button onclick="window.print()" class="btn btn-outline w-100 mb-2"><i class="fas fa-print"></i> Распечатать отчет</button>
                                <a href="{{ route('salary.export', $calculation->id) }}" class="btn btn-outline-success w-100"><i class="fas fa-file-excel"></i> Экспорт в Excel</a>
                            </div></div></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .summary-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .summary-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px; padding: 20px; text-align: center; }
        .summary-value { font-size: 28px; font-weight: 700; }
        .summary-label { font-size: 12px; opacity: 0.8; margin-top: 5px; }
        @media print { .summary-cards .summary-card { background: #f0f0f0; color: black; } .action-buttons, .btn, .card-header a { display: none; } }
    </style>
@endpush
