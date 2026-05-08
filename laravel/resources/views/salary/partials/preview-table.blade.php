<div class="salary-preview">
    <div class="card">
        <div class="card-header">
            <h4><i class="fas fa-chart-bar"></i> Результаты расчета за {{ $period }}</h4>
            <div class="calculation-actions">
                <button type="button" id="save-calculation" class="btn btn-success"><i class="fas fa-save"></i> Сохранить</button>
                <button type="button" id="export-excel" class="btn btn-outline"><i class="fas fa-file-excel"></i> Экспорт</button>
                <button type="button" id="print-preview" class="btn btn-outline"><i class="fas fa-print"></i> Печать</button>
            </div>
        </div>
        <div class="card-body">
            <!-- Сводка -->
            <div class="summary-cards">
                <div class="summary-card"><div class="summary-value">{{ $summary['total_employees'] }}</div><div class="summary-label">Сотрудников</div></div>
                <div class="summary-card"><div class="summary-value">{{ number_format($summary['total_accrued'], 2) }} BYN</div><div class="summary-label">Всего начислено</div></div>
                <div class="summary-card"><div class="summary-value">{{ number_format($summary['total_income_tax'], 2) }} BYN</div><div class="summary-label">Подоходный налог</div></div>
                <div class="summary-card"><div class="summary-value">{{ number_format($summary['total_net_salary'], 2) }} BYN</div><div class="summary-label">К выплате</div></div>
            </div>

            <!-- Таблица -->
            <div class="table-responsive">
                <table class="table table-bordered" id="preview-table">
                    <thead>
                    <tr><th>Сотрудник</th><th>Должность</th><th>Подразделение</th><th>Оклад</th><th>Премия</th><th>Начислено</th><th>Вычеты</th><th>Налог(13%)</th><th>ФСЗН(1%)</th><th>Соцстрах</th><th>Удержано</th><th>К выплате</th></tr>
                    </thead>
                    <tbody>
                    @foreach($calculations as $calc)
                        <tr>
                            <td>{{ $calc['employee_name'] }}</td><td>{{ $calc['position'] }}</td><td>{{ $calc['department_name'] }}</td>
                            <td class="text-end">{{ number_format($calc['base_salary'], 2) }}</td>
                            <td class="text-end">{{ number_format($calc['bonus'], 2) }}</td>
                            <td class="text-end"><strong>{{ number_format($calc['total_accrued'], 2) }}</strong></td>
                            <td class="text-end text-success">{{ number_format($calc['total_tax_deductions'], 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($calc['income_tax'], 2) }}</td>
                            <td class="text-end">{{ number_format($calc['pension_fund'], 2) }}</td>
                            <td class="text-end">{{ number_format($calc['social_security'], 2) }}</td>
                            <td class="text-end">{{ number_format($calc['total_deductions'], 2) }}</td>
                            <td class="text-end text-success"><strong>{{ number_format($calc['net_salary'], 2) }}</strong></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let calculations = @json($calculations);
        const year = '{{ $year }}';
        const month = '{{ $month }}';
        const bonusPercentage = '{{ $bonusPercentage ?? 10 }}';
        const includeTradeUnion = '{{ $includeTradeUnion ?? false }}';

        $('#save-calculation').click(function() {
            const btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Сохранение...');

            $.ajax({
                url: '{{ route("salary.save") }}',
                type: 'POST',
                data: { _token: '{{ csrf_token() }}', year: year, month: month, calculations: calculations },
                success: function(response) {
                    if (response.success) {
                        showMessage(response.message, 'success');
                        setTimeout(() => window.location.href = '{{ route("salary.history") }}', 1500);
                    } else { showMessage(response.message, 'error'); }
                    btn.prop('disabled', false).html('<i class="fas fa-save"></i> Сохранить');
                },
                error: function() { showMessage('Ошибка при сохранении', 'error'); btn.prop('disabled', false).html('<i class="fas fa-save"></i> Сохранить'); }
            });
        });

        $('#export-excel').click(function() {
            let csv = [['Сотрудник','Должность','Подразделение','Оклад','Премия','Начислено','Вычеты','Налог(13%)','ФСЗН(1%)','Соцстрах','Удержано','К выплате']];
            @foreach($calculations as $calc)
            csv.push(['{{ $calc['employee_name'] }}','{{ $calc['position'] }}','{{ $calc['department_name'] }}',
                '{{ number_format($calc['base_salary'], 2) }}','{{ number_format($calc['bonus'], 2) }}','{{ number_format($calc['total_accrued'], 2) }}',
                '{{ number_format($calc['total_tax_deductions'], 2) }}','{{ number_format($calc['income_tax'], 2) }}',
                '{{ number_format($calc['pension_fund'], 2) }}','{{ number_format($calc['social_security'], 2) }}',
                '{{ number_format($calc['total_deductions'], 2) }}','{{ number_format($calc['net_salary'], 2) }}']);
            @endforeach
            let csvContent = csv.map(row => row.join(';')).join('\n');
            const blob = new Blob(['\uFEFF' + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a'); link.href = URL.createObjectURL(blob); link.download = 'salary_calculation_{{ $period }}.csv'; link.click();
            showMessage('Экспорт завершен', 'success');
        });

        $('#print-preview').click(function() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>Расчет зарплаты за {{ $period }}</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"><style>body{font-family:Arial,sans-serif;margin:20px;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#f2f2f2;}.summary-cards{display:flex;gap:15px;margin-bottom:20px;}.summary-card{background:#f0f0f0;padding:15px;border-radius:5px;text-align:center;flex:1;}.text-end{text-align:right;}</style></head><body>');
            printWindow.document.write(document.querySelector('.salary-preview').innerHTML);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        });

        function showMessage(message, type) {
            const alertClass = type === 'success' ? 'alert-success' : (type === 'error' ? 'alert-danger' : 'alert-info');
            const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
            const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top:20px;right:20px;z-index:9999;min-width:300px;">
            <i class="fas ${icon}"></i> ${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
            $('body').append(alertHtml);
            setTimeout(() => $('.alert:last').fadeOut(500, function() { $(this).remove(); }), 3000);
        }
    });
</script>
