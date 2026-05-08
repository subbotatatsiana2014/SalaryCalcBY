@extends('layouts.app')

@section('title', 'Редактирование подразделения - SalaryCalc BY')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fas fa-edit"></i> Редактирование подразделения</h3>
                        <p class="text-muted mb-0">ID: {{ $department->id }}</p>
                    </div>
                    <div>
                        <a href="{{ route('departments.show', $department->id) }}" class="btn btn-outline">
                            <i class="fas fa-eye"></i> Просмотр
                        </a>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="edit-department-form" method="POST" action="{{ route('departments.update', $department->id) }}">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="name" class="form-label">Название подразделения *</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $department->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="code" class="form-label">Код подразделения *</label>
                            <input type="text" id="code" name="code" class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code', $department->code) }}" required>
                            <small class="form-text text-muted">Уникальный код для идентификации</small>
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="color" class="form-label">Цвет подразделения</label>
                            <div class="color-picker-container">
                                <input type="color" id="color" name="color" class="form-control-color"
                                       value="{{ old('color', $department->color ?? '#3B82F6') }}">
                                <div class="color-preview" id="color-preview"
                                     style="background: {{ $department->color ?? '#3B82F6' }}"></div>
                            </div>
                            <small class="form-text text-muted">Используется для визуального выделения</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Описание</label>
                        <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description', $department->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="budget" class="form-label">Бюджет (BYN) *</label>
                            <div class="input-with-icon">
                                <i class="fas fa-money-bill-wave"></i>
                                <input type="number" id="budget" name="budget" class="form-control @error('budget') is-invalid @enderror"
                                       step="0.01" min="0" value="{{ old('budget', $department->budget) }}" required>
                            </div>
                            @error('budget')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="manager_id" class="form-label">Руководитель</label>
                            <select id="manager_id" name="manager_id" class="form-control @error('manager_id') is-invalid @enderror">
                                <option value="">Не назначен</option>
                                @foreach($managers ?? [] as $manager)
                                    <option value="{{ $manager->id }}" {{ old('manager_id', $department->manager_id) == $manager->id ? 'selected' : '' }}>
                                        {{ $manager->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('manager_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="parent_id" class="form-label">Родительское подразделение</label>
                            <select id="parent_id" name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                <option value="">Корневое подразделение</option>
                                @foreach($departments ?? [] as $dept)
                                    @if($dept->id != $department->id)
                                        <option value="{{ $dept->id }}" {{ old('parent_id', $department->parent_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }} ({{ $dept->code }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                            @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1"
                                    {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                Активное подразделение
                            </label>
                            <small class="form-text text-muted d-block">Неактивные подразделения скрыты в некоторых отчетах</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Статистика подразделения</label>
                        <div class="stats-summary">
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <span class="stat-label">Сотрудников:</span>
                                    <span class="stat-value">{{ $department->employees_count ?? $department->employees()->count() }}</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Фонд оплаты труда:</span>
                                    <span class="stat-value">{{ number_format($department->salary_fund ?? $department->employees()->sum('salary'), 2) }} BYN</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Бюджет:</span>
                                    <span class="stat-value">{{ number_format($department->budget, 2) }} BYN</span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Использование бюджета:</span>
                                    <span class="stat-value">
                                    @php
                                        $usage = ($department->salary_fund ?? 0) / max($department->budget, 1) * 100;
                                    @endphp
                                        {{ round($usage, 1) }}%
                                </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Информация:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Поля, отмеченные *, обязательны для заполнения</li>
                            <li>Код подразделения должен быть уникальным</li>
                            <li>Нельзя выбрать родительским подразделением самого себя</li>
                        </ul>
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('departments.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Отмена
                        </a>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Превью цвета
        const colorInput = document.getElementById('color');
        const colorPreview = document.getElementById('color-preview');

        if (colorInput && colorPreview) {
            colorInput.addEventListener('change', function() {
                colorPreview.style.background = this.value;
            });
        }

        // Валидация формы перед отправкой
        const form = document.getElementById('edit-department-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const name = document.getElementById('name');
                const code = document.getElementById('code');
                const budget = document.getElementById('budget');
                let hasError = false;

                // Очищаем предыдущие ошибки
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                document.querySelectorAll('.invalid-feedback').forEach(el => {
                    el.remove();
                });

                if (!name.value.trim()) {
                    name.classList.add('is-invalid');
                    showError(name, 'Название подразделения обязательно');
                    hasError = true;
                }

                if (!code.value.trim()) {
                    code.classList.add('is-invalid');
                    showError(code, 'Код подразделения обязателен');
                    hasError = true;
                }

                if (!budget.value || parseFloat(budget.value) < 0) {
                    budget.classList.add('is-invalid');
                    showError(budget, 'Бюджет должен быть положительным числом');
                    hasError = true;
                }

                // Проверка на циклическую зависимость
                const parentId = document.getElementById('parent_id').value;
                const departmentId = {{ $department->id }};

                if (parentId == departmentId) {
                    const parentSelect = document.getElementById('parent_id');
                    parentSelect.classList.add('is-invalid');
                    showError(parentSelect, 'Подразделение не может быть родителем самого себя');
                    hasError = true;
                }

                if (hasError) {
                    e.preventDefault();
                    // Прокрутка к первой ошибке
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
            });
        }

        function showError(element, message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.style.display = 'block';
            errorDiv.textContent = message;
            element.parentNode.appendChild(errorDiv);
        }
    </script>
@endpush
