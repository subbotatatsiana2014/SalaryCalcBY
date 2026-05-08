@extends('layouts.app')

@section('title', 'История расчетов зарплаты - SalaryCalc BY')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-history"></i> История расчетов заработной платы</h3>
                <a href="{{ route('salary.calculate') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Новый расчет
                </a>
            </div>
            <div class="card-body">
                <!-- Фильтры -->
                <div class="filters-bar">
                    <form method="GET" action="{{ route('salary.history') }}" class="form-row">
                        <div class="form-group">
                            <label for="year">Год</label>
                            <select name="year" id="year" class="form-control">
                                <option value="">Все годы</option>
                                @foreach($years as $y)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="month">Месяц</label>
                            <select name="month" id="month" class="form-control">
                                <option value="">Все месяцы</option>
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ request('month') == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="department_id">Подразделение</label>
                            <select name="department_id" id="department_id" class="form-control">
                                <option value="">Все подразделения</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Статус</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">Все статусы</option>
                                @foreach($statuses as $val => $name)
                                    <option value="{{ $val }}" {{ request('status') == $val ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group align-self-end">
                            <button type="submit" class="btn btn-primary "><i class="fas fa-search"></i> Поиск</button>
                            <a href="{{ route('salary.history') }}" class="btn btn-outline"><i class="fas fa-undo"></i> Сброс</a>
                        </div>
                    </form>
                </div>

                @if($calculations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Период</th>
                                <th>Сотрудник</th>
                                <th>Подразделение</th>
                                <th>Начислено</th>
                                <th>Налоги</th>
                                <th>К выплате</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($calculations as $calc)
                                <tr>
                                    <td>{{ $calc->getMonthNameAttribute() }} {{ $calc->year }}</td>
                                    <td>{{ $calc->employee->full_name ?? '—' }} ({!! $calc->employee->position ?? '—' !!})</small></td>
                                    <td>{{ $calc->department->name ?? '—' }}</td>
                                    <td class="text-end">{{ number_format($calc->total_accrued, 2) }} BYN</td>
                                    <td class="text-end text-danger">{{ number_format($calc->income_tax + $calc->pension_fund + $calc->social_security, 2) }} BYN</td>
                                    <td class="text-end text-success"><strong>{{ number_format($calc->net_salary, 2) }} BYN</strong></td>
                                    </td>
                                    <span class="badge badge-{{ $calc->status === 'paid' ? 'success' : ($calc->status === 'approved' ? 'info' : 'warning') }}">
                                        {{ $calc->getStatusNameAttribute() }}
                                    </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('salary.results', $calc->id) }}" class="btn btn-sm btn-outline" title="Просмотр"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('salary.export', $calc->id) }}" class="btn btn-sm btn-outline-success" title="Экспорт"><i class="fas fa-file-excel"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $calculations->appends(request()->query())->links() }}
                @else
                    <div class="empty-state"><div class="empty-icon"><i class="fas fa-history"></i></div><h3>Нет расчетов</h3><p>Выполните первый расчет заработной платы</p><a href="{{ route('salary.calculate') }}" class="btn btn-primary">Новый расчет</a></div>
                @endif
            </div>
        </div>
    </div>
@endsection
