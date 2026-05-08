@extends('layouts.app')

@section('title', 'Расчет заработной платы - SalaryCalc BY')

@section('content')
    <div class="page">
        <!-- Статистика -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-card-icon"><i class="fas fa-calculator"></i></div>
                <div class="stat-card-title">Всего расчетов</div>
                <div class="stat-card-value">{{ $stats['total_calculations'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-card-title">Всего выплачено</div>
                <div class="stat-card-value">{{ number_format($stats['total_paid'], 2) }} BYN</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                <div class="stat-card-title">Удержано налогов</div>
                <div class="stat-card-value">{{ number_format($stats['total_tax'], 2) }} BYN</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon"><i class="fas fa-chart-line"></i></div>
                <div class="stat-card-title">Средняя ЗП</div>
                <div class="stat-card-value">{{ number_format($stats['avg_salary'], 2) }} BYN</div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-calculator"></i> Расчет заработной платы</h3>
                <a href="{{ route('salary.calculate') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Новый расчет
                </a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-card">
                            <div class="info-card-header">
                                <i class="fas fa-history"></i>
                                <h4>Последние расчеты</h4>
                            </div>
                            <div class="info-card-content">
                                @if($lastCalculations->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                            <tr><th>Период</th><th>Сотрудников</th><th>Сумма</th><th></th></tr>
                                            </thead>
                                            <tbody>
                                            @foreach($lastCalculations->groupBy(function($item) { return $item->year . '-' . $item->month; }) as $period => $group)
                                                <tr>
                                                    <td>{{ $group->first()->getMonthNameAttribute() }} {{ $period }}</td>
                                                    <td>{{ $group->count() }}</td>
                                                    <td>{{ number_format($group->sum('net_salary'), 2) }} BYN</td>
                                                    <td>
                                                        <a href="{{ route('salary.history') }}?year={{ explode('-', $period)[0] }}&month={{ explode('-', $period)[1] }}"
                                                           class="btn btn-sm btn-outline">Подробнее</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted text-center">Нет выполненных расчетов</p>
                                @endif
                                <div class="text-center mt-3">
                                    <a href="{{ route('salary.history') }}" class="btn btn-outline">Вся история</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

