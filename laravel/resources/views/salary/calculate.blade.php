@extends('layouts.app')

@section('title', 'Калькулятор зарплаты - SalaryCalc BY')

@section('content')
    <div class="page" id="salary-calculate">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fas fa-calculator"></i> Калькулятор заработной платы</h3>
                        <p class="text-muted mb-0">Выберите период и параметры для расчета зарплаты</p>
                    </div>
                    <div>
                        <a href="{{ route('salary.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Панель параметров -->
                <div class="calculation-params">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="year">Год <span class="text-danger">*</span></label>
                            <select id="year" name="year" class="form-control">
                                @for($y = date('Y')-2; $y <= date('Y')+1; $y++)
                                    <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="month">Месяц <span class="text-danger">*</span></label>
                            <select id="month" name="month" class="form-control">
                                @for($m = 1; $m <= 12; $m++)
                                    @php $monthName = ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'][$m-1]; @endphp
                                    <option value="{{ $m }}" {{ $m == $currentMonth ? 'selected' : '' }}>{{ $monthName }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="department_id">Подразделение</label>
                            <select id="department_id" name="department_id" class="form-control">
                                <option value="">Все подразделения</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="employee_id">Сотрудник</label>
                            <select id="employee_id" name="employee_id" class="form-control">
                                <option value="">Все сотрудники</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Настройки расчета -->
                <div class="calculation-settings">
                    <div class="settings-header">
                        <h5 class="mb-0"><i class="fas fa-sliders-h"></i> Настройки расчета</h5>
                    </div>

                    <div class="settings-body">
                        <!-- Информация об окладе -->
                        <div class="salary-info-card">
                            <div class="salary-info-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="salary-info-content">
                                <label class="info-label">Оклад сотрудника</label>
                                <div class="info-value">
                                    <input type="text" id="employee_salary" class="form-control form-control-lg" readonly disabled>
                                    <span class="currency">BYN</span>
                                </div>
                                <small class="info-hint">Оклад загружается после выбора сотрудника</small>
                                <input type="hidden" id="base_salary" name="base_salary" value="0">
                            </div>
                        </div>

                        <!-- Рабочее время -->
                        <div class="settings-section">
                            <h6 class="section-subtitle"><i class="fas fa-clock"></i> Рабочее время</h6>
                            <div class="form-grid grid-cols-4">
                                <div class="form-group">
                                    <label for="base_days"><i class="fas fa-calendar-alt"></i> Норма дней</label>
                                    <div class="input-group">
                                        <input type="number" id="base_days" class="form-control" value="0" min="0" step="1">
                                        <span class="input-group-text">дн.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="base_hours"><i class="fas fa-hourglass-half"></i> Норма часов</label>
                                    <div class="input-group">
                                        <input type="number" id="base_hours" class="form-control" value="0" min="0" step="0.5">
                                        <span class="input-group-text">ч.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fact_days"><i class="fas fa-check-circle"></i> Факт. дней</label>
                                    <div class="input-group">
                                        <input type="number" id="fact_days" class="form-control" value="0" min="0" step="1">
                                        <span class="input-group-text">дн.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="fact_hours"><i class="fas fa-clock"></i> Факт. часов</label>
                                    <div class="input-group">
                                        <input type="number" id="fact_hours" class="form-control" value="0" min="0" step="0.5">
                                        <span class="input-group-text">ч.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Начисления -->
                        <div class="settings-section">
                            <h6 class="section-subtitle"><i class="fas fa-coins"></i> Начисления</h6>
                            <div class="form-grid grid-3">
                                <div class="form-group">
                                    <label for="bonus_percentage"><i class="fas fa-percent"></i> Премия</label>
                                    <div class="input-group">
                                        <input type="number" id="bonus_percentage" class="form-control" value="0" step="1">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="overtime_hours"><i class="fas fa-hourglass-start"></i> Сверхурочные</label>
                                    <div class="input-group">
                                        <input type="number" id="overtime_hours" class="form-control" value="0" min="0" step="0.5">
                                        <span class="input-group-text">ч.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="other_accruals"><i class="fas fa-plus-circle"></i> Другие начисления</label>
                                    <div class="input-group">
                                        <input type="number" id="other_accruals" class="form-control" value="0" min="0">
                                        <span class="input-group-text">BYN</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Отсутствия -->
                        <div class="settings-section">
                            <h6 class="section-subtitle"><i class="fas fa-user-clock"></i> Отсутствия</h6>
                            <div class="form-grid grid-3">
                                <div class="form-group">
                                    <label for="sick_days"><i class="fas fa-thermometer-half"></i> Больничные дни</label>
                                    <div class="input-group">
                                        <input type="number" id="sick_days" class="form-control" value="0" min="0" step="1">
                                        <span class="input-group-text">дн.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="vacation_days"><i class="fas fa-umbrella-beach"></i> Отпускные дни</label>
                                    <div class="input-group">
                                        <input type="number" id="vacation_days" class="form-control" value="0" min="0" step="1">
                                        <span class="input-group-text">дн.</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="own_expense_days"><i class="fas fa-umbrella-beach"></i>Дни за свой счет</label>
                                    <div class="input-group">
                                        <input type="number" id="own_expense_days" class="form-control" value="0" min="0" step="0.5">
                                        <span class="input-group-text">дн.</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Дополнительные опции -->
                        <div class="settings-options">
                        <!-- Профсоюзные взносы -->
                            <div class="form-check form-switch" style="margin-bottom: 5px">
                                <input type="checkbox" id="include_trade_union" class="form-check-input" value="1">
                                <label class="form-check-label" for="include_trade_union">
                                    <i class="fas fa-handshake"></i> Учитывать профсоюзные взносы (1%)
                                </label>
                            </div>
                        <!-- Имущественный вычет -->
                            <div class="form-check form-switch">
                                <input type="checkbox" id="use_mortgage" class="form-check-input" value="1">
                                <label class="form-check-label" for="use_mortgage">
                                    <i class="fas fa-percent"></i> Учитывать имущественный вычет
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="form-actions">
                    <button type="button" id="print-slip" class="btn btn-outline-info" style="display: none;">
                        <i class="fas fa-print"></i> Печать расчетного листа
                    </button>
                    <button type="button" id="reset-settings" class="btn btn-outline-secondary">
                        <i class="fas fa-undo-alt"></i> Сбросить настройки
                    </button>
                    <button type="button" id="calculate-btn" class="btn btn-primary">
                        <i class="fas fa-chart-line"></i> Рассчитать
                    </button>
                </div>
            </div>
        </div>

        <!-- Результаты расчета -->
        <div id="preview-container" class="mt-4" style="display: none;">
            <div id="preview-content">
                <div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Загрузка...</p></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let currentYear = $('#year').val();
            let currentMonth = $('#month').val();
            let currentDepartment = $('#department_id').val();

            // Загрузка сотрудников при выборе подразделения
            $('#department_id').change(function() {
                const departmentId = $(this).val();
                const employeeSelect = $('#employee_id');

                $.ajax({
                    url: '{{ route("salary.employees") }}',
                    type: 'GET',
                    data: { department_id: departmentId },
                    success: function(response) {
                        if (response.success) {
                            employeeSelect.html('<option value="">Все сотрудники</option>');
                            $.each(response.employees, function(i, emp) {
                                employeeSelect.append('<option value="' + emp.id + '" data-salary="' + emp.salary + '">' + emp.name + ' - ' + emp.position + '</option>');
                            });
                            employeeSelect.prop('disabled', false);
                        } else {
                            employeeSelect.html('<option value="">Ошибка загрузки</option>');
                        }
                    },
                    error: function() {
                        employeeSelect.html('<option value="">Ошибка загрузки</option>');
                    }
                });
            });

            // При загрузке страницы загружаем всех сотрудников
            $('#department_id').trigger('change');

            // Показ оклада при выборе сотрудника и показ кнопки печати
            $('#employee_id').change(function() {
                const employeeId = $(this).val();
                if (employeeId) {
                    $('#print-slip').show();
                    const selectedOption = $(this).find('option:selected');
                    const salary = selectedOption.data('salary');
                    if (salary) {
                        $('#employee_salary').val(Number(salary).toFixed(2));
                        $('#base_salary').val(salary);
                    }
                } else {
                    $('#print-slip').hide();
                    $('#employee_salary').val('');
                    $('#base_salary').val(0);
                }
            });

            // Сброс настроек
            $('#reset-settings').click(function() {
                $('#base_days').val(0);
                $('#base_hours').val(0);
                $('#fact_days').val(0);
                $('#fact_hours').val(0);
                $('#bonus_percentage').val(10);
                $('#overtime_hours').val(0);
                $('#sick_days').val(0);
                $('#vacation_days').val(0);
                $('#other_accruals').val(0);
                $('#own_expense_days').val(0);
                $('#include_trade_union').prop('checked', false);
                showMessage('Настройки сброшены', 'info');
            });

            // Расчет
            $('#calculate-btn').click(function() {
                if (!$('#year').val() || !$('#month').val()) {
                    showMessage('Выберите год и месяц', 'error');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Расчет...');
                $('#preview-container').show();

                $.ajax({
                    url: '{{ route("salary.preview") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        year: $('#year').val(),
                        month: $('#month').val(),
                        department_id: $('#department_id').val(),
                        employee_id: $('#employee_id').val(),
                        base_salary: $('#base_salary').val(),
                        base_days: $('#base_days').val(),
                        base_hours: $('#base_hours').val(),
                        fact_days: $('#fact_days').val(),
                        fact_hours: $('#fact_hours').val(),
                        bonus_percentage: $('#bonus_percentage').val(),
                        overtime_hours: $('#overtime_hours').val(),
                        sick_days: $('#sick_days').val(),
                        vacation_days: $('#vacation_days').val(),
                        other_accruals: $('#other_accruals').val(),
                        include_trade_union: $('#include_trade_union').is(':checked') ? 1 : 0
                    },
                    success: function(response) {
                        $('#preview-content').html(response);
                        btn.prop('disabled', false).html('<i class="fas fa-chart-line"></i> Рассчитать');
                        $('html, body').animate({ scrollTop: $('#preview-container').offset().top - 100 }, 500);
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-chart-line"></i> Рассчитать');
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = '';
                            $.each(xhr.responseJSON.errors, function(k, v) { errors += v[0] + '\n'; });
                            showMessage(errors, 'error');
                        } else {
                            showMessage('Ошибка при расчете', 'error');
                        }
                        $('#preview-container').hide();
                    }
                });
            });

            // Загрузка информации о имущественном вычете при выборе сотрудника
            $('#employee_id').change(function() {
                const employeeId = $(this).val();
                if (employeeId) {
                    // Загружаем информацию о вычете
                    $.ajax({
                        url: '/salary/mortgage/' + employeeId,
                        type: 'GET',
                        success: function(response) {
                            if (response.success && response.mortgage_info) {
                                const info = response.mortgage_info;
                                $('#mortgage-info-block').show();
                                $('#mortgage-text').html(`
                        Доступен имущественный вычет: осталось ${numberFormat(info.remaining_deduction)} BYN из ${numberFormat(info.total_possible_deduction)} BYN
                    `);
                            } else {
                                $('#mortgage-info-block').hide();
                            }
                        },
                        error: function() {
                            $('#mortgage-info-block').hide();
                        }
                    });
                } else {
                    $('#mortgage-info-block').hide();
                }
            });

            function numberFormat(value) {
                return Number(value).toLocaleString('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            // Печать расчетного листа
            $('#print-slip').click(function() {
                const employeeId = $('#employee_id').val();
                if (!employeeId) {
                    showMessage('Для печати расчетного листа выберите сотрудника', 'error');
                    return;
                }

                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Подготовка...');

                $.ajax({
                    url: '{{ route("salary.payslip") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: employeeId,
                        year: $('#year').val(),
                        month: $('#month').val(),
                        base_salary: $('#base_salary').val(),
                        bonus_percentage: $('#bonus_percentage').val(),
                        overtime_hours: $('#overtime_hours').val(),
                        sick_days: $('#sick_days').val(),
                        vacation_days: $('#vacation_days').val(),
                        other_accruals: $('#other_accruals').val(),
                        include_trade_union: $('#include_trade_union').is(':checked') ? 1 : 0,
                        // Добавляем данные о рабочем времени
                        norm_days: $('#base_days').val(),
                        norm_hours: $('#base_hours').val(),
                        actual_days: $('#fact_days').val(),
                        actual_hours: $('#fact_hours').val()
                    },
                    success: function(response) {
                        const printWindow = window.open('', '_blank');
                        printWindow.document.write(response);
                        printWindow.document.close();
                        btn.prop('disabled', false).html('<i class="fas fa-print"></i> Печать расчетного листа');
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-print"></i> Печать расчетного листа');
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            let errors = '';
                            $.each(xhr.responseJSON.errors, function(k, v) { errors += v[0] + '\n'; });
                            showMessage(errors, 'error');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            showMessage(xhr.responseJSON.message, 'error');
                        } else {
                            showMessage('Ошибка при подготовке расчетного листа', 'error');
                        }
                    }
                });
            });

            function showMessage(message, type) {
                const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
                const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
                const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top:20px;right:20px;z-index:9999;min-width:300px;">
            <i class="fas ${icon}"></i> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
                $('body').append(alertHtml);
                setTimeout(() => $('.alert:last').fadeOut(500, function() { $(this).remove(); }), 3000);
            }
        });
    </script>
@endpush
