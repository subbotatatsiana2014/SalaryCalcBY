@extends('layouts.app')

@section('title', 'Добавление подразделения - SalaryCalc BY')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Добавление нового подразделения</h3>
                        <p class="text-muted mb-0">Создание подразделения в структуре компании</p>
                    </div>
                    <div>
                        <a href="{{ route('departments.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="create-department-form" method="POST" action="{{ route('departments.store') }}">
                    @csrf

                    <div class="tabs mb-4">
                        <button type="button" class="tab-btn active" data-tab="main">Основная информация</button>
                        <button type="button" class="tab-btn" data-tab="financial">Финансовые показатели</button>
                        <button type="button" class="tab-btn" data-tab="management">Управление</button>
                        <button type="button" class="tab-btn" data-tab="additional">Дополнительно</button>
                    </div>

                    <!-- Вкладка: Основная информация -->
                    <div class="tab-content active" id="tab-main">
                        <h4 class="mb-4"><i class="fas fa-building"></i> Основная информация</h4>

                        <div class="form-group">
                            <label for="name" class="form-label">Название подразделения *</label>
                            <input type="text" id="name" name="name" class="form-control" required placeholder="Например: Отдел разработки">
                            <small class="form-text text-muted">Полное наименование подразделения</small>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="code" class="form-label">Код подразделения *</label>
                                <input type="text" id="code" name="code" class="form-control" required placeholder="Например: DEV001">
                                <small class="form-text text-muted">Уникальный код для идентификации</small>
                            </div>

                            <div class="form-group">
                                <label for="color" class="form-label">Цвет подразделения</label>
                                <div class="color-picker-container">
                                    <input type="color" id="color" name="color" class="form-control-color" value="#3B82F6">
                                    <div class="color-preview" id="color-preview" style="background: #3B82F6;"></div>
                                </div>
                                <small class="form-text text-muted">Используется для визуального выделения</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Описание</label>
                            <textarea id="description" name="description" class="form-control" rows="4" placeholder="Описание подразделения, его функций и задач..."></textarea>
                        </div>
                    </div>

                    <!-- Вкладка: Финансовые показатели -->
                    <div class="tab-content" id="tab-financial">
                        <h4 class="mb-4"><i class="fas fa-chart-line"></i> Финансовые показатели</h4>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="budget" class="form-label">Бюджет (BYN) *</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-money-bill-wave"></i>
                                    <input type="number" id="budget" name="budget" class="form-control" step="0.01" min="0" required placeholder="0.00">
                                </div>
                                <small class="form-text text-muted">Годовой бюджет подразделения</small>
                            </div>

                            <div class="form-group">
                                <label for="salary_fund" class="form-label">Фонд оплаты труда (BYN)</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-calculator"></i>
                                    <input type="number" id="salary_fund" name="salary_fund" class="form-control" step="0.01" min="0" placeholder="0.00">
                                </div>
                                <small class="form-text text-muted">Месячный фонд оплаты труда (заполнится автоматически)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Вкладка: Управление -->
                    <div class="tab-content" id="tab-management">
                        <h4 class="mb-4"><i class="fas fa-users-cog"></i> Управление</h4>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="manager_id" class="form-label">Руководитель подразделения</label>
                                <select id="manager_id" name="manager_id" class="form-control">
                                    <option value="">Не назначен</option>
                                    @foreach($managers ?? [] as $manager)
                                        <option value="{{ $manager->id }}">{{ $manager->name }} ({{ $manager->email }})</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Ответственный за подразделение</small>
                            </div>

                            <div class="form-group">
                                <label for="parent_id" class="form-label">Родительское подразделение</label>
                                <select id="parent_id" name="parent_id" class="form-control">
                                    <option value="">Корневое подразделение</option>
                                    @foreach($departments ?? [] as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }} ({{ $department->code }})</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">К какому подразделению относится</small>
                            </div>
                        </div>
                    </div>

                    <!-- Вкладка: Дополнительно -->
                    <div class="tab-content" id="tab-additional">
                        <h4 class="mb-4"><i class="fas fa-cog"></i> Дополнительные настройки</h4>

                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" id="is_active" name="is_active" class="form-check-input" value="1" checked>
                                <span class="checkmark"></span>
                                Активное подразделение
                            </label>
                            <small class="form-text text-muted d-block mt-1">Неактивные подразделения не отображаются в некоторых разделах</small>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle"></i>
                            <strong>Информация:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Поля, отмеченные *, обязательны для заполнения</li>
                                <li>Код подразделения должен быть уникальным</li>
                                <li>После создания подразделения можно будет добавлять сотрудников</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-actions mt-4">
                        <a href="{{ route('departments.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Отмена
                        </a>
                        <button type="submit" class="btn btn-primary" id="create-submit-btn">
                            <i class="fas fa-plus"></i> Создать подразделение
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Инициализация вкладок
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');

                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                    content.style.display = 'none';
                });

                this.classList.add('active');
                const activeTab = document.getElementById(`tab-${tabId}`);
                if (activeTab) {
                    activeTab.classList.add('active');
                    activeTab.style.display = 'block';
                }
            });
        });

        // Показать первую вкладку
        const firstTab = document.querySelector('.tab-btn.active');
        if (firstTab) {
            const tabId = firstTab.getAttribute('data-tab');
            const activeTab = document.getElementById(`tab-${tabId}`);
            if (activeTab) {
                activeTab.style.display = 'block';
            }
        }

        // Превью цвета
        const colorInput = document.getElementById('color');
        const colorPreview = document.getElementById('color-preview');

        if (colorInput && colorPreview) {
            colorInput.addEventListener('change', function() {
                colorPreview.style.background = this.value;
            });
        }

        // Валидация формы
        const form = document.getElementById('create-department-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const name = document.getElementById('name');
                const code = document.getElementById('code');
                const budget = document.getElementById('budget');
                let hasError = false;

                if (!name.value.trim()) {
                    name.classList.add('is-invalid');
                    hasError = true;
                } else {
                    name.classList.remove('is-invalid');
                }

                if (!code.value.trim()) {
                    code.classList.add('is-invalid');
                    hasError = true;
                } else {
                    code.classList.remove('is-invalid');
                }

                if (!budget.value || parseFloat(budget.value) < 0) {
                    budget.classList.add('is-invalid');
                    hasError = true;
                } else {
                    budget.classList.remove('is-invalid');
                }

                if (hasError) {
                    e.preventDefault();
                    alert('Пожалуйста, заполните все обязательные поля');
                }
            });
        }
    </script>
@endpush

@push('styles')
    <style>
        .color-picker-container {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .color-preview {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: background 0.2s ease;
        }

        .form-control-color {
            width: 60px;
            height: 40px;
            padding: 2px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            cursor: pointer;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .is-invalid {
            border-color: #ef4444 !important;
        }

        .invalid-feedback {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }

        .alert-info ul {
            padding-left: 20px;
        }

        .alert-info li {
            margin-bottom: 4px;
        }
    </style>
@endpush
