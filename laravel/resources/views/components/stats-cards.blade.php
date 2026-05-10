<div class="stats-cards grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <div class="stat-card card-employees">
            <div class="stat-card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-content">
                <h3 class="stat-card-title">Всего сотрудников</h3>
                <div class="stat-card-value">{{ $stats['total_employees'] ?? 0 }}</div>
            </div>
        </div>

        <div class="stat-card card-salary">
            <div class="stat-card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-card-content">
                <h3 class="stat-card-title">Фонд оплаты труда</h3>
                <div class="stat-card-value">{{ $stats['salary_fund'] }} BYN</div>
            </div>
        </div>

        <div class="stat-card card-taxes">
            <div class="stat-card-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-card-content">
                <h3 class="stat-card-title">Налоги к уплате</h3>
                <div class="stat-card-value">{{ $stats['taxes_due'] }} BYN</div>
            </div>
        </div>

        <div class="stat-card card-departments">
            <div class="stat-card-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-card-content">
                <h3 class="stat-card-title">Подразделений</h3>
                <div class="stat-card-value">{{ $stats['total_departments'] ?? 0 }}</div>
            </div>
        </div>
</div>
