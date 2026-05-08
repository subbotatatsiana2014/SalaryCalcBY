@extends('layouts.app')

@section('title', 'Сотрудники подразделения - ' . $department->name)

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Сотрудники подразделения: {{ $department->name }}</h3>
                        <p class="text-muted mb-0">
                            Код: {{ $department->code }} |
                            Всего сотрудников: {{ $department->employees->count() }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('departments.index', $department->id) }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                @if($department->employees->count() > 0)
                    <div class="table-responsive">
                        <table class="employees-table">
                            <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th>Должность</th>
                                <th>Дата приема</th>
                                <th>Зарплата</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($department->employees as $employee)
                                <tr>
                                    <td>
                                        <div class="employee-info">
                                            <div class="employee-avatar">
                                                <img src="https://i.pravatar.cc/40?u={{ $employee->user->email }}" alt="{{ $employee->user->name }}">
                                            </div>
                                            <div class="employee-details">
                                                <div class="employee-name">{{ $employee->user->name }}</div>
                                                <div class="employee-email">{{ $employee->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->position }}</td>
                                    <td>{{ $employee->hire_date ? $employee->hire_date->format('d.m.Y') : 'Не указана' }}</td>
                                    <td class="salary">{{ number_format($employee->salary, 2) }} BYN</td>
                                    <td>
                                    <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }}">
                                        {{ $employee->is_active ? 'Активен' : 'Уволен' }}
                                    </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-sm btn-outline">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Нет сотрудников</h3>
                        <p>В этом подразделении пока нет сотрудников</p>
                        <a href="{{ route('employees.create') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Добавить сотрудника
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
