@extends('layouts.app')

@section('title', 'Сотрудники - SalaryCalc BY')

@section('content')
    <div class="page" id="employees">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3>Управление сотрудниками</h3>
                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Добавить сотрудника
                </a>
            </div>
        </div>

        @include('components.stats-cards')

        <div class="card">
            <div class="card-header">
                <h3>Список сотрудников</h3>
                <div class="table-controls">
                    <div class="search-box">
                        <input type="text" id="search-input" placeholder="Поиск сотрудников...">
                        <i class="fas fa-search"></i>
                    </div>

                    <div class="filter-group">
                        <select id="department-filter" class="form-control">
                            <option value="">Все подразделения</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <select id="status-filter" class="form-control">
                            <option value="">Все статусы</option>
                            <option value="1">Активные</option>
                            <option value="0">Уволенные</option>
                        </select>
                    </div>

                    <button type="button" id="reset-filters" class="btn btn-outline btn-sm">
                        <i class="fas fa-undo"></i> Сбросить
                    </button>
                </div>
            </div>
            <div class="card-body">
                @if($employees->count() > 0)
                    <div class="table-responsive">
                        <table class="employees-table">
                            <thead>
                            <tr>
                                <th>Сотрудник</th>
                                <th>Должность</th>
                                <th>Подразделение</th>
                                <th>Дата приема</th>
                                <th>Зарплата</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($employees as $employee)
                                <tr class="{{ !$employee->is_active ? 'inactive' : '' }}">
                                    <td>
                                        <div class="employee-info">
                                            <div class="employee-avatar">
                                                @if($employee->avatar_url && !str_contains($employee->avatar_url, 'ui-avatars.com'))
                                                    <img src="{{ $employee->avatar_url }}" alt="{{ $employee->full_name }}">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        {{ mb_substr($employee->full_name, 0, 1) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="employee-details">
                                                <div class="employee-name">{{ $employee->full_name }}</div>
                                                <div class="employee-email">{{ $employee->user?->email ?? 'Email не указан' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $employee->position ?? 'Не указана' }}</td>
                                    <td>{{ $employee->department?->name ?? 'Не назначено' }}</td>
                                    <td>{{ $employee->hire_date?->format('d.m.Y') ?? 'Не указана' }}</td>
                                    <td class="salary-amount">{{ number_format($employee->salary ?? 0, 2) }} BYN</td>
                                    <td>
                                        <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }}">
                                            {{ $employee->is_active ? 'Активен' : 'Неактивен' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-outline btn-sm" title="Просмотр">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-outline btn-sm" title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('salary.mortgage.edit', $employee->id) }}" class="btn btn-sm btn-outline-primary" title="Имущественный вычет">
                                                <i class="fas fa-home"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($employees->hasPages())
                        <div class="pagination">
                            {{ $employees->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Нет сотрудников</h3>
                        <p>Добавьте первого сотрудника в систему</p>
                        <a href="{{ route('employees.create') }}" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i> Добавить сотрудника
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Поиск и фильтрация сотрудников
        const searchInput = document.getElementById('search-input');
        const departmentFilter = document.getElementById('department-filter');
        const statusFilter = document.getElementById('status-filter');
        const resetBtn = document.getElementById('reset-filters');

        function filterEmployees() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const departmentId = departmentFilter?.value || '';
            const statusValue = statusFilter?.value || '';

            const rows = document.querySelectorAll('.employees-table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const department = row.querySelector('td:nth-child(3)')?.textContent || '';
                const statusBadge = row.querySelector('.badge')?.textContent || '';
                const isActive = statusBadge === 'Активен';

                let show = true;

                // Поиск по тексту
                if (searchTerm && !text.includes(searchTerm)) show = false;

                if (show && departmentId && department !== departmentFilter.options[departmentFilter.selectedIndex]?.text) {
                    show = false;
                }

                if (show && statusValue !== '') {
                    if (statusValue === '0' && !isActive) show = false;
                    if (statusValue === '1' && isActive) show = false;
                }

                row.style.display = show ? '' : 'none';
            });
        }

        function resetFilters() {
            if (searchInput) searchInput.value = '';
            if (departmentFilter) departmentFilter.value = '';
            if (statusFilter) statusFilter.value = '';
            filterEmployees();
        }

        if (searchInput) searchInput.addEventListener('input', filterEmployees);
        if (departmentFilter) departmentFilter.addEventListener('change', filterEmployees);
        if (statusFilter) statusFilter.addEventListener('change', filterEmployees);
        if (resetBtn) resetBtn.addEventListener('click', resetFilters);

        // Автоматическое скрытие алертов
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }, 5000);
            });
        });
    </script>
@endpush
