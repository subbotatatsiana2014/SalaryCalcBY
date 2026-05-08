<div class="header">
    <button class="mobile-menu-btn">
        <i class="fas fa-bars"></i>
    </button>

    <div class="header-content">
        <h2 id="page-title">
            @yield('title', 'SalaryCalc BY')
        </h2>

        <div class="header-actions">
            <!-- Уведомления -->
            <div class="notification-dropdown">
                <button class="header-btn" id="notification-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-count">3</span>
                </button>
                <div class="dropdown-content notification-content">
                    <div class="notification-header">
                        <h4>Уведомления</h4>
                        <div class="badge-container">
                            <span class="badge-text">3 новых</span>
                            <span class="badge"></span>
                        </div>

                    </div>
                    <div class="notification-list">
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fas fa-users text-primary"></i>
                            </div>
                            <div class="notification-text">
                                <p>Новый сотрудник добавлен в систему</p>
                                <small>2 минуты назад</small>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fas fa-calculator text-success"></i>
                            </div>
                            <div class="notification-text">
                                <p>Расчет зарплаты за ноябрь завершен</p>
                                <small>1 час назад</small>
                            </div>
                        </div>
                        <div class="notification-item">
                            <div class="notification-icon">
                                <i class="fas fa-file-invoice-dollar text-warning"></i>
                            </div>
                            <div class="notification-text">
                                <p>Срок уплаты налогов через 3 дня</p>
                                <small>Вчера</small>
                            </div>
                        </div>
                    </div>
                    <div class="notification-footer">
                        <a href="#">Все уведомления</a>
                    </div>
                </div>
            </div>

            <!-- Профиль пользователя -->
            <div class="user-dropdown">
                <button class="user-btn" id="user-btn">
                    <div class="user-info">
                        <img src="{{ auth()->user()->avatar_url }}" alt="User">
                        <div class="user-details">
                            <div class="user-name">{{ auth()->user()->name }}</div>
                            <small class="user-role">{{ auth()->user()->role_name }}</small>
                        </div>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </button>
                <div class="dropdown-content user-content">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <small class="user-role">{{ auth()->user()->role_name }}</small>
                    <div class="user-menu-items">
                        <a href="#" class="user-menu-item">
                            <i class="fas fa-user"></i>
                            <span>Мой профиль</span>
                        </a>
                        <a href="#" class="user-menu-item">
                            <i class="fas fa-cog"></i>
                            <span>Настройки</span>
                        </a>
                        <a href="#" class="user-menu-item">
                            <i class="fas fa-question-circle"></i>
                            <span>Помощь</span>
                        </a>
                    </div>
                    <div class="user-menu-footer">
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Выйти</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
