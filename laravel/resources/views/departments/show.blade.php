@extends('layouts.app')

@section('title', $department->name)

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>{{ $department->name }}</h3>
                        <p class="text-muted mb-0">Код: {{ $department->code }}</p>
                    </div>
                    <div>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Основная информация</h4>
                        <table class="info-table">
                            <tr>
                                <th>Название:</th>
                                <td>{{ $department->name }}</td>
                            </tr>
                            <tr>
                                <th>Код:</th>
                                <td><span class="badge" style="background: {{ $department->color }}">{{ $department->code }}</span></td>
                            </tr>
                            <tr>
                                <th>Руководитель:</th>
                                <td>
                                    @if($department->manager)
                                        {{ $department->manager->name }}
                                        <br><small class="text-muted">{{ $department->manager->email }}</small>
                                    @else
                                        <span class="text-muted">Не назначен</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Родительское подразделение:</th>
                                <td>
                                    @if($department->parent)
                                        <a href="{{ route('departments.show', $department->parent->id) }}">
                                            {{ $department->parent->name }}
                                        </a>
                                    @else
                                        <span class="text-muted">Корневое подразделение</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Статус:</th>
                                <td>
                                <span class="badge badge-{{ $department->is_active ? 'success' : 'danger' }}">
                                    {{ $department->is_active ? 'Активно' : 'Неактивно' }}
                                </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Описание:</th>
                                <td>{{ $department->description ?? 'Нет описания' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h4>Финансовые показатели</h4>
                        <table class="info-table">
                            <tr>
                                <th>Бюджет:</th>
                                <td class="budget">{{ number_format($department->budget, 2) }} BYN</td>
                            </tr>
                            <tr>
                                <th>Фонд оплаты труда:</th>
                                <td>{{ number_format($department->salary_fund ?? 0, 2) }} BYN</td>
                            </tr>
                            <tr>
                                <th>Использование бюджета:</th>
                                <td>
                                    @php
                                        $usage = ($department->salary_fund ?? 0) / max($department->budget, 1) * 100;
                                    @endphp
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: {{ min($usage, 100) }}%"></div>
                                    </div>
                                    <span>{{ round($usage, 1) }}%</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Сотрудников:</th>
                                <td>{{ $department->employees_count ?? 0 }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if($department->children && $department->children->count() > 0)
                    <div class="mt-4">
                        <h4>Дочерние подразделения</h4>
                        <div class="department-list">
                            @foreach($department->children as $child)
                                <div class="department-item">
                                    <div class="department-color" style="background: {{ $child->color }}"></div>
                                    <a href="{{ route('departments.show', $child->id) }}" class="department-name">
                                        {{ $child->name }}
                                    </a>
                                    <span class="department-code">({{ $child->code }})</span>
                                    <span class="badge badge-{{ $child->is_active ? 'success' : 'danger' }}">
                            {{ $child->is_active ? 'Активно' : 'Неактивно' }}
                        </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(($department->employees_count ?? 0) > 0)
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Сотрудники ({{ $department->employees_count }})</h4>
                            <a href="{{ route('departments.employees', $department->id) }}" class="btn btn-sm btn-outline">
                                Все сотрудники <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="employees-grid">
                            @foreach($department->employees->take(5) as $employee)
                                <div class="employee-card">
                                    <img src="https://i.pravatar.cc/50?u={{ $employee->user->email }}" alt="{{ $employee->user->name }}">
                                    <div class="employee-info">
                                        <div class="employee-name">{{ $employee->user->name }}</div>
                                        <div class="employee-position">{{ $employee->position }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function editDepartment(id) {
                window.location.href = `/departments/${id}/edit`;
            }
        </script>
    @endpush
@endsection
