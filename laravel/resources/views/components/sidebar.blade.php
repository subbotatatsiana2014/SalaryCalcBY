<div class="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-calculator"></i>
            <div class="logo-text">
                <span class="logo-title">SalaryCalc</span>
                <span class="logo-subtitle">BY</span>
            </div>
        </div>
        <button class="sidebar-close">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="sidebar-content">
        <ul class="nav-links">
            <li>
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <div class="nav-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <span class="nav-text">Главная</span>
                </a>
            </li>

            <!-- Для всех ролей кроме начальника -->
            @if(auth()->user()->role !== 'manager')
                <li>
                    <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <div class="nav-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="nav-text">Сотрудники</span>
                    </a>
                </li>
            @endif

            <!-- Для бухгалтера и админа -->
            @if(in_array(auth()->user()->role, ['accountant', 'admin']))
                <li>
                    <a href="{{ route('salary.index') }}" class="nav-link {{ request()->routeIs('salary.*') ? 'active' : '' }}">
                        <div class="nav-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <span class="nav-text">Расчет зарплаты</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('taxes.index') }}" class="nav-link {{ request()->routeIs('taxes.*') ? 'active' : '' }}">
                        <div class="nav-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <span class="nav-text">Налоги и платежи</span>
                    </a>
                </li>
            @endif

            <!-- Для админа, бухгалтера и начальника -->
            @if(in_array(auth()->user()->role, ['admin', 'accountant', 'manager']))
                <li>
                    <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <div class="nav-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <span class="nav-text">Отчеты</span>
                    </a>
                </li>
            @endif

            <!-- Только для админа -->
            @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('departments.index') }}" class="nav-link {{ request()->routeIs('departments.*') ? 'active' : '' }}">
                        <div class="nav-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="nav-text">Подразделения</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <div class="nav-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <span class="nav-text">Настройки</span>
                    </a>
                </li>
            @endif
        </ul>

        <div class="user-card">
            <img src="{{ auth()->user()->avatar_url }}" alt="User">
            <div class="sidebar-user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->role_name }}</div>
            </div>
        </div>

        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Выход</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        </div>
    </div>
</div>
