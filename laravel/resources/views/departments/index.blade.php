@extends('layouts.app')

@section('title', 'Подразделения - SalaryCalc BY')

@section('content')
    <div class="page" id="departments">
        <div class="card">
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

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card-header">
                <h3>Управление подразделениями</h3>
                <a href="{{ route('departments.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Добавить подразделение
                </a>
            </div>
        </div>

        <!-- Карточки статистики -->
        @include('components.stats-cards')

        <div class="card">
            <div class="card-header">
                <h3>Список подразделений</h3>
                <div class="table-controls">
                    <div class="search-box">
                        <input type="text" placeholder="Поиск подразделений..." id="search-input">
                        <i class="fas fa-search"></i>
                    </div>
                    <div class="filters">
                        <select id="status-filter">
                            <option value="">Все статусы</option>
                            <option value="active">Активные</option>
                            <option value="inactive">Неактивные</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($departments->count() > 0)
                    <div class="table-responsive">
                        <table class="departments-table">
                            <thead>
                            <tr>
                                <th>Название</th>
                                <th>Код</th>
                                <th>Руководитель</th>
                                <th>Сотрудников</th>
                                <th>Бюджет</th>
                                <th>ФОТ</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($departments as $department)
                                <tr data-department-id="{{ $department->id }}" data-status="{{ $department->is_active ? 'active' : 'inactive' }}">
                                    <td>
                                        <div class="department-info">
                                            <div class="department-color" style="background: {{ $department->color ?? '#3B82F6' }}"></div>
                                            <div>
                                                <div class="department-name">{{ $department->name }}</div>
                                                @if($department->description)
                                                    <small class="department-description">{{ Str::limit($department->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td><code class="department-code">{{ $department->code }}</code></td>
                                    <td>
                                        @if($department->manager)
                                            <div class="user-info-small">
                                                <img src="https://i.pravatar.cc/30?u={{ $department->manager->email }}" alt="Manager" loading="lazy">
                                                <span>{{ $department->manager->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Не назначен</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="employee-count">
                                            <span class="count">{{ $department->employees_count ?? 0 }}</span>
                                            <small>сотр.</small>
                                        </div>
                                    </td>
                                    <td><div class="budget-amount">{{ number_format($department->budget, 2) }} BYN</div></td>
                                    <td><div class="salary-amount">{{ number_format($department->salary_fund ?? 0, 2) }} BYN</div></td>
                                    <td>
                                        <div class="status-indicator">
                                            <span class="status-dot status-{{ $department->is_active ? 'active' : 'inactive' }}"></span>
                                            <span class="status-text">{{ $department->is_active ? 'Активно' : 'Неактивно' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="{{ route('departments.show', $department->id) }}" class="btn btn-outline btn-sm" title="Просмотр">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-outline btn-sm" title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('departments.employees', $department->id) }}" class="btn btn-outline btn-sm" title="Сотрудники">
                                                <i class="fas fa-users"></i>
                                            </a>
                                            @if(auth()->user()->role === 'admin')
                                                <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Вы уверены, что хотите удалить это подразделение?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Удалить">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($departments, 'hasPages') && $departments->hasPages())
                        <div class="pagination">{{ $departments->links('pagination::bootstrap-5') }}</div>
                    @endif

                @else
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-building"></i></div>
                        <h3>Нет подразделений</h3>
                        <p>Добавьте первое подразделение для организации структуры компании</p>
                        <a href="{{ route('departments.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Добавить подразделение
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-sitemap"></i> Организационная структура</h3>
                <div class="tree-controls">
                    <button type="button" class="btn btn-outline btn-sm" onclick="expandAll()">
                        <i class="fas fa-expand-alt"></i> Развернуть всё
                    </button>
                    <button type="button" class="btn btn-outline btn-sm" onclick="collapseAll()">
                        <i class="fas fa-compress-alt"></i> Свернуть всё
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="org-chart">
                    @forelse($treeDepartments as $rootDepartment)
                        @include('departments.partials.tree-node', ['department' => $rootDepartment, 'level' => 0])
                    @empty
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-sitemap"></i></div>
                            <h3>Нет подразделений</h3>
                            <p>Добавьте первое подразделение для отображения структуры</p>
                            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Добавить подразделение
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Поиск и фильтрация
        const searchInput = document.getElementById('search-input');
        const statusFilter = document.getElementById('status-filter');

        function applyFilters() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const statusValue = statusFilter?.value || '';
            const rows = document.querySelectorAll('.departments-table tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const rowStatus = row.getAttribute('data-status');
                let show = true;

                if (searchTerm && !text.includes(searchTerm)) show = false;
                if (statusValue === 'active' && rowStatus !== 'active') show = false;
                if (statusValue === 'inactive' && rowStatus !== 'inactive') show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (statusFilter) statusFilter.addEventListener('change', applyFilters);

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

        function toggleTreeNode(departmentId) {
            const childrenContainer = document.getElementById(`node-children-${departmentId}`);
            const toggleIcon = document.getElementById(`toggle-icon-${departmentId}`);

            if (childrenContainer) {
                if (childrenContainer.style.display === 'none' || !childrenContainer.style.display) {
                    childrenContainer.style.display = 'block';
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-chevron-right');
                        toggleIcon.classList.add('fa-chevron-down');
                    }
                } else {
                    childrenContainer.style.display = 'none';
                    if (toggleIcon) {
                        toggleIcon.classList.remove('fa-chevron-down');
                        toggleIcon.classList.add('fa-chevron-right');
                    }
                }
            }
        }

        function expandAll() {
            document.querySelectorAll('.node-children').forEach(el => {
                el.style.display = 'block';
            });
            document.querySelectorAll('[id^="toggle-icon-"]').forEach(icon => {
                icon.classList.remove('fa-chevron-right');
                icon.classList.add('fa-chevron-down');
            });
        }

        function collapseAll() {
            document.querySelectorAll('.node-children').forEach(el => {
                el.style.display = 'none';
            });
            document.querySelectorAll('[id^="toggle-icon-"]').forEach(icon => {
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-right');
            });
        }

        function editDepartment(departmentId) {
            window.location.href = `/departments/${departmentId}/edit`;
        }

        // Инициализация дерева - показываем первый уровень, остальные свернуты
        document.addEventListener('DOMContentLoaded', function() {
            // Сворачиваем все дочерние узлы кроме первого уровня
            document.querySelectorAll('.node-children').forEach(el => {
                // Проверяем, не является ли это первым уровнем
                const parentNode = el.closest('.tree-node');
                const grandParent = parentNode?.parentElement?.closest('.tree-node');
                if (grandParent) {
                    el.style.display = 'none';
                    // Меняем иконку у родителя
                    const parentId = parentNode?.getAttribute('data-department-id');
                    if (parentId) {
                        const icon = document.getElementById(`toggle-icon-${parentId}`);
                        if (icon) {
                            icon.classList.remove('fa-chevron-down');
                            icon.classList.add('fa-chevron-right');
                        }
                    }
                } else {
                    el.style.display = 'block';
                }
            });
        });
    </script>
@endpush
