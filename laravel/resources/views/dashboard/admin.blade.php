<!-- Системная статистика -->
<div class="card">
    <div class="card-header">
        <h3>Обзор системы</h3>
        <a href="{{ route('reports.index') }}" class="btn btn-primary">Подробные отчеты</a>
    </div>
    <div class="card-body">
        <div class="admin-stats-grid">
            <a href="{{ route('users.index') }}" class="admin-stat-link">
                <div class="admin-stat">
                    <div class="admin-stat-value">{{ $users->count() }}</div>
                    <div class="admin-stat-label">
                        <i class="fas fa-users"></i> Пользователей
                    </div>
                </div>
            </a>
            <a href="{{ route('departments.index') }}" class="admin-stat-link">
                <div class="admin-stat">
                    <div class="admin-stat-value">{{ $departments->count() }}</div>
                    <div class="admin-stat-label">
                        <i class="fas fa-building"></i> Подразделений
                    </div>
                </div>
            </a>
            <a href="{{ route('employees.index') }}" class="admin-stat-link">
                <div class="admin-stat">
                    <div class="admin-stat-value">{{ $employees->count() }}</div>
                    <div class="admin-stat-label">
                        <i class="fas fa-user-tie"></i> Сотрудников
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Последние действия -->
<div class="card">
    <div class="card-header">
        <h3>Последние действия в системе</h3>
    </div>
    <div class="card-body">
        <table>
            <thead>
            <tr>
                <th>Пользователь</th>
                <th>Действие</th>
                <th>Время</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Администратор</td>
                <td>Добавлен новый сотрудник</td>
                <td>14.11.2024 10:30</td>
            </tr>
            <tr>
                <td>Бухгалтер</td>
                <td>Выполнен расчет зарплаты</td>
                <td>14.11.2024 09:15</td>
            </tr>
            <tr>
                <td>Кадровик</td>
                <td>Обновлены данные сотрудника</td>
                <td>13.11.2024 16:45</td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
