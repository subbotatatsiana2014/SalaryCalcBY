@extends('layouts.app')

@section('title', 'Имущественный вычет - ' . $employee->full_name)

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3><i class="fas fa-home"></i> Имущественный вычет</h3>
                        <p class="text-muted mb-0">
                            Сотрудник: <strong>{{ $employee->full_name }}</strong> |
                            Должность: {{ $employee->position }} |
                            Подразделение: {{ $employee->department->name ?? '—' }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад к сотрудникам
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8 mx-auto">
                        <!-- Информационная карточка -->
                        <div class="info-card mb-4">
                            <div class="info-card-header">
                                <i class="fas fa-info-circle"></i>
                                <h4>Что такое имущественный вычет?</h4>
                            </div>
                            <div class="info-card-content">
                                <p>Имущественный вычет позволяет вернуть <strong>13%</strong> от стоимости приобретенного жилья, но не более <strong>13% от 78 100 BYN</strong> (максимум <strong>10 153 BYN</strong>).</p>
                                <p>Вычет применяется к подоходному налогу ежемесячно до полного использования суммы.</p>
                                <hr>
                                <p class="small text-muted mb-0">
                                    <i class="fas fa-calculator"></i> Формула: Стоимость × 13% = Сумма вычета, но не более 10 153 BYN
                                </p>
                            </div>
                        </div>

                        <!-- Форма редактирования вычета -->
                        <div class="info-card">
                            <div class="info-card-header">
                                <i class="fas fa-edit"></i>
                                <h4>Настройка вычета</h4>
                            </div>
                            <div class="info-card-content">
                                <form id="mortgage-form" method="POST" action="{{ route('salary.mortgage.save') }}">
                                    @csrf
                                    <input type="hidden" name="employee_id" value="{{ $employee->id }}">

                                    <div class="form-group mb-4">
                                        <label for="property_cost">Стоимость недвижимости (BYN)</label>
                                        <input type="number"
                                               step="0.01"
                                               class="form-control form-control-lg @error('property_cost') is-invalid @enderror"
                                               id="property_cost"
                                               name="property_cost"
                                               value="{{ old('property_cost', $mortgageInfo['property_cost'] ?? '') }}"
                                               placeholder="Например: 100000"
                                               required>
                                        <small class="form-text text-muted">Укажите фактическую стоимость приобретенного жилья</small>
                                        @error('property_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-4">
                                        <label for="start_date">Дата начала применения вычета</label>
                                        <input type="date"
                                               class="form-control @error('start_date') is-invalid @enderror"
                                               id="start_date"
                                               name="start_date"
                                               value="{{ old('start_date', $mortgageInfo['start_date'] ?? date('Y-m-d')) }}">
                                        <small class="form-text text-muted">С какого месяца начинать применять вычет к зарплате</small>
                                        @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Калькулятор в реальном времени -->
                                    <div class="alert alert-info" id="calculator-preview" style="display: none;">
                                        <strong><i class="fas fa-calculator"></i> Предварительный расчет:</strong><br>
                                        Стоимость: <span id="preview-cost">0</span> BYN<br>
                                        Ограничение: 78 100 BYN<br>
                                        <strong class="text-success">Максимальная сумма возврата: <span id="preview-amount">0</span> BYN</strong>
                                    </div>

                                    <!-- Текущий статус вычета -->
                                    @if($mortgageInfo && ($mortgageInfo['property_cost'] ?? 0) > 0)
                                        <div class="alert alert-secondary mt-3">
                                            <strong><i class="fas fa-chart-line"></i> Текущий статус вычета:</strong><br>
                                            <div class="row mt-2">
                                                <div class="col-sm-6">
                                                    Стоимость жилья: <strong>{{ number_format($mortgageInfo['property_cost'], 2) }} BYN</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    Максимальный вычет: <strong>{{ number_format($mortgageInfo['total_possible_deduction'], 2) }} BYN</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    Использовано: <strong class="text-primary">{{ number_format($mortgageInfo['used_deduction'], 2) }} BYN</strong>
                                                </div>
                                                <div class="col-sm-6">
                                                    <strong class="text-success">Осталось: {{ number_format($mortgageInfo['remaining_deduction'], 2) }} BYN</strong>
                                                </div>
                                            </div>
                                            @if(($mortgageInfo['total_possible_deduction'] ?? 0) > 0)
                                                @php $percent = ($mortgageInfo['used_deduction'] / $mortgageInfo['total_possible_deduction']) * 100; @endphp
                                                <div class="progress mt-2" style="height: 8px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
                                                </div>
                                                <small class="text-muted">{{ round($percent, 1) }}% использовано</small>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="form-actions mt-4 d-flex justify-content-between">
                                        <button type="submit" class="btn btn-success btn-lg" id="save-btn">
                                            <i class="fas fa-save"></i> Сохранить вычет
                                        </button>

                                        @if($mortgageInfo && ($mortgageInfo['property_cost'] ?? 0) > 0)
                                            <button type="button" class="btn btn-danger btn-lg" id="clear-btn">
                                                <i class="fas fa-trash"></i> Очистить вычет
                                            </button>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                const costInput = $('#property_cost');
                const previewDiv = $('#calculator-preview');
                const previewCost = $('#preview-cost');
                const previewAmount = $('#preview-amount');

                // Функция обновления калькулятора
                function updateCalculator() {
                    let cost = parseFloat(costInput.val()) || 0;
                    const maxBase = Math.min(cost, 78100);
                    const maxReturn = maxBase * 0.13;

                    previewCost.text(cost.toFixed(2));
                    previewAmount.text(maxReturn.toFixed(2) + ' BYN');

                    if (cost > 0) {
                        previewDiv.show();
                    } else {
                        previewDiv.hide();
                    }
                }

                // Обновляем при вводе
                costInput.on('input', updateCalculator);

                // Вызываем при загрузке, если есть значение
                if (costInput.val() > 0) {
                    updateCalculator();
                }

                // Очистка вычета через AJAX
                $('#clear-btn').click(function() {
                    if (!confirm('Вы уверены, что хотите полностью удалить имущественный вычет?\n\nВнимание: это действие нельзя отменить!')) {
                        return;
                    }

                    const btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Очистка...');

                    $.ajax({
                        url: '{{ route("salary.mortgage.save") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            employee_id: {{ $employee->id }},
                            property_cost: 0,
                            start_date: $('#start_date').val()
                        },
                        success: function(response) {
                            if (response.success) {
                                // Показываем сообщение и перезагружаем
                                showMessage(response.message, 'success');
                                setTimeout(() => window.location.reload(), 1500);
                            } else {
                                showMessage(response.message, 'error');
                                btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Очистить вычет');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).html('<i class="fas fa-trash"></i> Очистить вычет');
                            let message = 'Ошибка при очистке вычета';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            showMessage(message, 'error');
                        }
                    });
                });

                // Функция показа сообщений
                function showMessage(message, type) {
                    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
                    const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top:20px;right:20px;z-index:9999;min-width:300px;z-index:9999;">
                <i class="fas ${icon}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>`;
                    $('body').append(alertHtml);
                    setTimeout(() => $('.alert:last').fadeOut(500, function() { $(this).remove(); }), 3000);
                }

                // Если есть flash сообщение из сессии
                @if(session('success'))
                showMessage('{{ session('success') }}', 'success');
                @endif
                @if(session('error'))
                showMessage('{{ session('error') }}', 'error');
                @endif
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .progress {
                border-radius: 10px;
                background-color: #e9ecef;
            }
            .progress-bar {
                border-radius: 10px;
                transition: width 0.3s ease;
            }
            .btn-lg {
                padding: 10px 24px;
                font-size: 16px;
            }
        </style>
    @endpush
@endsection
