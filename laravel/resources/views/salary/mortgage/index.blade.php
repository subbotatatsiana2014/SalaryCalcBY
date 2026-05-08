@extends('layouts.app')

@section('title', 'Управление имущественными вычетами')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-home"></i> Имущественные вычеты сотрудников</h3>
                <p class="text-muted mb-0">Управление возвратом 13% от стоимости приобретенного жилья</p>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>Сотрудник</th>
                            <th>Должность</th>
                            <th>Стоимость жилья</th>
                            <th>Макс. вычет</th>
                            <th>Использовано</th>
                            <th>Остаток</th>
                            <th>Прогресс</th>
                            <th>Действия</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($employees as $employee)
                            @php $mortgage = $employee->activeMortgageDeduction; @endphp
                            <tr>
                                <td>
                                    <strong>{{ $employee->full_name }}</strong>
                                    <br><small class="text-muted">{{ $employee->position }}</small>
                                </td>
                                <td>{{ $employee->department->name ?? '—' }}</td>
                                <td class="text-end">
                                    {{ $mortgage ? number_format($mortgage->property_cost, 2) : '—' }} BYN
                                </td>
                                <td class="text-end">
                                    {{ $mortgage ? number_format($mortgage->total_possible_deduction, 2) : '—' }} BYN
                                </td>
                                <td class="text-end text-primary">
                                    {{ $mortgage ? number_format($mortgage->used_deduction, 2) : '—' }} BYN
                                </td>
                                <td class="text-end text-success">
                                    {{ $mortgage ? number_format($mortgage->remaining_deduction, 2) : '—' }} BYN
                                </td>
                                <td>
                                    @if($mortgage && $mortgage->total_possible_deduction > 0)
                                        @php $percent = ($mortgage->used_deduction / $mortgage->total_possible_deduction) * 100; @endphp
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
                                        </div>
                                        <small>{{ round($percent, 1) }}%</small>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($mortgage)
                                        <button class="btn btn-sm btn-outline-primary edit-mortgage"
                                                data-id="{{ $employee->id }}"
                                                data-name="{{ $employee->full_name }}"
                                                data-cost="{{ $mortgage->property_cost }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-outline-success add-mortgage"
                                                data-id="{{ $employee->id }}"
                                                data-name="{{ $employee->full_name }}">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $employees->links() }}
            </div>
        </div>
    </div>

    <!-- Модальное окно -->
    <div class="modal fade" id="mortgageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-home"></i> Имущественный вычет
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Как работает?</strong><br>
                        Вы можете вернуть 13% от стоимости приобретенного жилья, но не более 13% от 78 100 BYN
                        (максимум <strong>10 153 BYN</strong>). Вычет применяется к подоходному налогу
                        ежемесячно до полного использования суммы.
                    </div>
                    <form id="mortgage-form">
                        <input type="hidden" id="employee_id" name="employee_id">
                        <div class="form-group mb-3">
                            <label for="property_cost">Стоимость недвижимости (BYN)</label>
                            <input type="number" step="0.01" class="form-control" id="property_cost" required>
                            <small class="form-text text-muted">Укажите фактическую стоимость жилья</small>
                        </div>
                        <div class="form-group mb-3">
                            <label for="start_date">Дата начала применения</label>
                            <input type="date" class="form-control" id="start_date" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="alert alert-warning" id="preview">
                            <strong>Предварительный расчет:</strong><br>
                            Максимальная сумма возврата: <span id="preview-amount">0</span> BYN
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary" id="save-mortgage">
                        <i class="fas fa-save"></i> Сохранить
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let currentEmployeeId = null;

            $('#property_cost').on('input', function() {
                const cost = parseFloat($(this).val()) || 0;
                const maxBase = Math.min(cost, 78100);
                const maxReturn = maxBase * 0.13;
                $('#preview-amount').text(maxReturn.toFixed(2) + ' BYN');
            });

            $('.add-mortgage, .edit-mortgage').click(function() {
                currentEmployeeId = $(this).data('id');
                const employeeName = $(this).data('name');
                const cost = $(this).data('cost') || 0;

                $('#employee_id').val(currentEmployeeId);
                $('#property_cost').val(cost);
                $('#preview-amount').text((Math.min(cost, 78100) * 0.13).toFixed(2) + ' BYN');

                if (cost > 0) {
                    $('#modalTitle').html('<i class="fas fa-edit"></i> Редактирование вычета - ' + employeeName);
                } else {
                    $('#modalTitle').html('<i class="fas fa-plus"></i> Добавление вычета - ' + employeeName);
                }

                $('#mortgageModal').modal('show');
            });

            $('#save-mortgage').click(function() {
                const btn = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Сохранение...');

                $.ajax({
                    url: '{{ route("salary.mortgage.save") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_id: currentEmployeeId,
                        property_cost: $('#property_cost').val(),
                        start_date: $('#start_date').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            showMessage(response.message, 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            showMessage(response.message, 'error');
                        }
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Сохранить');
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-save"></i> Сохранить');
                        let message = 'Ошибка при сохранении';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        showMessage(message, 'error');
                    }
                });
            });

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
        </script>
    @endpush
@endsection
