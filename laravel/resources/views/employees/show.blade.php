@extends('layouts.app')

@section('title', 'Просмотр сотрудника - ' . ($employee->full_name ?? $employee->user->name))

@section('content')
    <div class="page">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Просмотр сотрудника</h3>
                        <p class="text-muted mb-0">ID: {{ $employee->id }}</p>
                    </div>
                    <div>
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Заголовок с основной информацией -->
                <div class="employee-header mb-5">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="employee-avatar-large">
                                <img src="{{ $employee->avatar_url }}" alt="Аватар">
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-1">{{ $employee->personalInfo->last_name ?? '' }} {{ $employee->personalInfo->first_name ?? '' }} {{ $employee->personalInfo->middle_name ?? '' }}</h2>
                            <div class="text-muted mb-2">{{ $employee->position }}</div>
                            <div class="d-flex flex-wrap gap-3">
                                <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }}">
                                    {{ $employee->is_active ? 'Активен' : 'Неактивен' }}
                                </span>
                                @if($employee->department)
                                    <span class="badge" style="background: {{ $employee->department->color ?? '#3B82F6' }}; color: white;">
                                        {{ $employee->department->name }}
                                    </span>
                                @endif
                                <span class="text-muted">
                                    <i class="fas fa-calendar"></i> Принят: {{ $employee->hire_date ? $employee->hire_date->format('d.m.Y') : 'Не указана' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Вкладки -->
                <div class="tabs mb-4">
                    <button class="tab-btn active" data-tab="personal">Личные данные</button>
                    <button class="tab-btn" data-tab="work">Рабочая информация</button>
                    <button class="tab-btn" data-tab="documents">Документы</button>
                    <button class="tab-btn" data-tab="contacts">Контакты</button>
                </div>

                <!-- Вкладка: Личные данные -->
                <div class="tab-content active" id="tab-personal">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="info-table">
                                <tr><th>ФИО:</th><td>{{ $employee->personalInfo->last_name ?? '' }} {{ $employee->personalInfo->first_name ?? '' }} {{ $employee->personalInfo->middle_name ?? '' }}</td></tr>
                                <tr><th>Дата рождения:</th><td>{{ $employee->user->birth_date ? $employee->user->birth_date->format('d.m.Y') : 'Не указана' }}</td></tr>
                                <tr><th>Пол:</th><td>{{ $employee->user->gender === 'male' ? 'Мужской' : ($employee->user->gender === 'female' ? 'Женский' : 'Не указан') }}</td></tr>
                                <tr><th>Гражданство:</th><td>{{ $employee->personalInfo->nationality ?? 'Не указано' }}</td></tr>
                                <tr><th>Семейное положение:</th><td>{{ $employee->personalInfo->marital_status ?? 'Не указано' }}</td></tr>
                                <tr><th>Количество детей:</th><td>{{ $employee->personalInfo->children_count ?? 0 }}</td></tr>
                                <tr><th>Место рождения:</th><td>{{ $employee->personalInfo->birth_place ?? 'Не указано' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="info-table">
                                <tr><th>Email:</th><td>{{ $employee->user->email }}</td></tr>
                                <tr><th>Телефон:</th><td>{{ $employee->user->phone ?? 'Не указан' }}</td></tr>
                                <tr><th>Адрес:</th><td>{{ $employee->user->address ?? 'Не указан' }}</td></tr>
                                <tr><th>Образование:</th><td>
                                        @php
                                            $educationMap = [
                                                'secondary' => 'Среднее',
                                                'secondary_special' => 'Среднее специальное',
                                                'incomplete_higher' => 'Неоконченное высшее',
                                                'higher' => 'Высшее',
                                                'master' => 'Магистр',
                                                'phd' => 'Кандидат наук',
                                                'doctor' => 'Доктор наук'
                                            ];
                                        @endphp
                                        {{ $educationMap[$employee->contactInfo->education_level ?? ''] ?? 'Не указано' }}
                                    </td></tr>
                                <tr><th>Навыки:</th><td>{{ $employee->contactInfo->skills ?? 'Не указаны' }}</td></tr>
                                <tr><th>Языки:</th><td>{{ $employee->contactInfo->languages ?? 'Не указаны' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Вкладка: Рабочая информация -->
                <div class="tab-content" id="tab-work">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="info-table">
                                <tr><th>Подразделение:</th><td>{{ $employee->department->name ?? 'Не назначено' }}</td></tr>
                                <tr><th>Должность:</th><td>{{ $employee->position }}</td></tr>
                                <tr><th>Код должности:</th><td>{{ $employee->position_code ?? 'Не указан' }}</td></tr>
                                <tr><th>Оклад:</th><td class="salary">{{ number_format($employee->salary, 2) }} BYN</td></tr>
                                <tr><th>Тип занятости:</th><td>
                                        @switch($employee->employment_type)
                                            @case('full') Полная занятость @break
                                            @case('part') Частичная занятость @break
                                            @case('contract') Договор подряда @break
                                            @case('temporary') Временная @break
                                            @case('remote') Удаленная работа @break
                                            @default {{ $employee->employment_type }}
                                        @endswitch
                                    </td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="info-table">
                                <tr><th>Дата приема:</th><td>{{ $employee->hire_date ? $employee->hire_date->format('d.m.Y') : 'Не указана' }}</td></tr>
                                <tr><th>Дата увольнения:</th><td>{{ $employee->fire_date ? $employee->fire_date->format('d.m.Y') : 'Не уволен' }}</td></tr>
                                <tr><th>Испытательный срок до:</th><td>{{ $employee->probation_end_date ? $employee->probation_end_date->format('d.m.Y') : 'Не установлен' }}</td></tr>
                                <tr><th>График работы:</th><td>{{ $employee->work_schedule ?? '5/2, 9:00-18:00' }}</td></tr>
                                <tr><th>Рабочих часов в неделю:</th><td>{{ $employee->working_hours_per_week ?? 40 }}</td></tr>
                                <tr><th>Тип работы:</th><td>
                                        @switch($employee->work_type)
                                            @case('office') Офис @break
                                            @case('hybrid') Гибридный @break
                                            @case('remote') Удаленно @break
                                            @default Не указан
                                        @endswitch
                                    </td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <!-- Имущественный вычет -->
                            <div class="info-card" id="mortgage-card">
                                <div class="info-card-header">
                                    <i class="fas fa-home"></i>
                                    <h4>Имущественный вычет (на недвижимость)</h4>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#mortgageModal">
                                        <i class="fas fa-edit"></i> Настроить
                                    </button>
                                </div>
                                <div class="info-card-content" id="mortgage-info">
                                    <div class="text-center py-3">
                                        <i class="fas fa-spinner fa-spin"></i> Загрузка...
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Вкладка: Документы -->
                <div class="tab-content" id="tab-documents">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="info-table">
                                <tr><th>Паспорт:</th><td>{{ $employee->documents->passport_number ?? 'Не указан' }}</td></tr>
                                <tr><th>Кем выдан:</th><td>{{ $employee->documents->passport_issued_by ?? 'Не указано' }}</td></tr>
                                <tr><th>Дата выдачи:</th><td>{{ isset($employee->documents->passport_issued_date) ? \Carbon\Carbon::parse($employee->documents->passport_issued_date)->format('d.m.Y') : 'Не указана' }}</td></tr>
                                <tr><th>Срок действия:</th><td>{{ isset($employee->documents->passport_expiry_date) ? \Carbon\Carbon::parse($employee->documents->passport_expiry_date)->format('d.m.Y') : 'Не указан' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="info-table">
                                <tr><th>ИНН (УНП):</th><td>{{ $employee->documents->tax_id ?? 'Не указан' }}</td></tr>
                                <tr><th>Номер соц. страхования:</th><td>{{ $employee->documents->social_security_number ?? 'Не указан' }}</td></tr>
                                <tr><th>Банковский счет:</th><td>{{ $employee->documents->bank_account ?? 'Не указан' }}</td></tr>
                                <tr><th>Банк:</th><td>{{ $employee->documents->bank_name ?? 'Не указан' }}</td></tr>
                                <tr><th>Страховой полис:</th><td>{{ $employee->documents->insurance_policy_number ?? 'Не указан' }}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Вкладка: Контакты -->
                <div class="tab-content" id="tab-contacts">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Основные контакты</h5>
                            <table class="info-table">
                                <tr><th>Email:</th><td>{{ $employee->user->email }}</td></tr>
                                <tr><th>Телефон:</th><td>{{ $employee->user->phone ?? 'Не указан' }}</td></tr>
                                <tr><th>Рабочий телефон:</th><td>{{ $employee->contactInfo->work_phone ?? 'Не указан' }}</td></tr>
                                <tr><th>Адрес:</th><td>{{ $employee->user->address ?? 'Не указан' }}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Контактное лицо на экстренный случай</h5>
                            <table class="info-table">
                                <tr><th>ФИО:</th><td>{{ $employee->emergencyContacts->name ?? 'Не указано' }}</td></tr>
                                <tr><th>Телефон:</th><td>{{ $employee->emergencyContacts->phone ?? 'Не указан' }}</td></tr>
                                <tr><th>Степень родства:</th><td>{{ $employee->emergencyContacts->relation ?? 'Не указана' }}</td></tr>
                            </table>
                        </div>
                    </div>

                    @if($employee->contactInfo && $employee->contactInfo->notes)
                        <div class="mt-4">
                            <h5>Примечания</h5>
                            <div class="notes-box">
                                {{ $employee->contactInfo->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="mortgageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-home"></i> Имущественный вычет</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Имущественный вычет позволяет вернуть 13% от стоимости приобретенного жилья,
                        но не более 13% от 78 100 BYN (максимум 10 153 BYN).
                    </div>

                    <form id="mortgage-form">
                        <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                        <div class="form-group mb-3">
                            <label for="property_cost">Стоимость недвижимости (BYN)</label>
                            <input type="number" step="0.01" class="form-control" id="property_cost" name="property_cost"
                                   placeholder="Например: 100000" required>
                            <small class="form-text text-muted">Укажите стоимость приобретенного жилья</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="start_date">Дата начала применения вычета</label>
                            <input type="date" class="form-control" id="start_date" name="start_date"
                                   value="{{ date('Y-m-d') }}">
                            <small class="form-text text-muted">С какого месяца начинать применять вычет</small>
                        </div>

                        <div class="alert alert-warning" id="calc-preview" style="display: none;">
                            <strong>Предварительный расчет:</strong><br>
                            Стоимость: <span id="preview_cost">0</span> BYN<br>
                            Макс. сумма вычета: <span id="preview_max">0</span> BYN (13%)<br>
                            <span class="text-success">Вы сможете вернуть до <span id="preview_return">0</span> BYN</span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="save-mortgage-btn">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Инициализация вкладок
            document.querySelectorAll('.tab-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const tabId = this.getAttribute('data-tab');
                    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById(`tab-${tabId}`).classList.add('active');
                });
            });
            loadMortgageInfo();

            // Расчет при вводе стоимости
            $('#property_cost').on('input', function() {
                const cost = parseFloat($(this).val()) || 0;
                const maxBase = Math.min(cost, 78100);
                const maxReturn = maxBase * 0.13;

                $('#preview_cost').text(cost.toFixed(2));
                $('#preview_max').text(maxBase.toFixed(2));
                $('#preview_return').text(maxReturn.toFixed(2));
                $('#calc-preview').show();
            });

            // Сохранение вычета
            $('#save-mortgage-btn').click(function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Сохранение...');

                $.ajax({
                    url: '{{ route("salary.mortgage.save") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: {{ $employee->id }},
                        property_cost: $('#property_cost').val(),
                        start_date: $('#start_date').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            $('#mortgageModal').modal('hide');
                            loadMortgageInfo();
                        } else {
                            showMessage(response.message, 'error');
                        }
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Сохранить');
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Сохранить');
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = '';
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                errors += v[0] + '\n';
                            });
                            showMessage(errors, 'error');
                        } else {
                            showMessage('Ошибка при сохранении', 'error');
                        }
                    }
                });
            });

            function loadMortgageInfo() {
                $.ajax({
                    url: '{{ route("salary.mortgage.info", $employee->id) }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.mortgage_info) {
                            const info = response.mortgage_info;
                            const percent = (info.used_deduction / info.total_possible_deduction * 100).toFixed(1);

                            $('#mortgage-info').html(`
                            <div class="mortgage-status active">
                                <p><strong><i class="fas fa-check-circle text-success"></i> Вычет активен</strong></p>
                                <p><strong>Стоимость недвижимости:</strong> ${numberFormat(info.property_cost)} BYN</p>
                                <p><strong>Максимальный вычет:</strong> ${numberFormat(info.total_possible_deduction)} BYN</p>
                                <p><strong>Использовано:</strong> ${numberFormat(info.used_deduction)} BYN (${percent}%)</p>
                                <p><strong>Осталось:</strong> <span class="text-success">${numberFormat(info.remaining_deduction)} BYN</span></p>
                                <p><strong>Дата начала:</strong> ${info.start_date}</p>
                                <div class="progress mt-2">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: ${percent}%"></div>
                                </div>
                            </div>
                        `);
                        } else {
                            $('#mortgage-info').html(`
                            <div class="mortgage-status inactive">
                                <p class="text-muted"><i class="fas fa-info-circle"></i> Имущественный вычет не настроен</p>
                                <p class="small text-muted">Настройте вычет, чтобы вернуть 13% от стоимости жилья</p>
                            </div>
                        `);
                        }
                    },
                    error: function() {
                        $('#mortgage-info').html(`
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> Ошибка загрузки данных
                        </div>
                    `);
                    }
                });
            }

            function numberFormat(value) {
                return Number(value).toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            function showMessage(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top:20px;right:20px;z-index:9999;min-width:300px;">
                <i class="fas ${icon}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
                $('body').append(alertHtml);
                setTimeout(() => $('.alert:last').fadeOut(500, function() { $(this).remove(); }), 3000);
            }
        });
    </script>
    <style>
        .mortgage-status {
            padding: 15px;
            border-radius: 8px;
        }
        .mortgage-status.active {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
        }
        .mortgage-status.inactive {
            background: #f5f5f5;
            border-left: 4px solid #9e9e9e;
        }
        .progress {
            height: 8px;
            border-radius: 4px;
        }
    </style>
@endpush
