@extends('layouts.app')

@section('title', 'Настройки системы - SalaryCalc BY')

@section('content')
    <div class="page" id="settings-page">
        <!-- Flash сообщения -->
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
                <h3>Настройки системы</h3>
                <p class="text-muted mb-0">Управление параметрами и конфигурацией системы</p>
            </div>
            <div class="card-body">
                <!-- Вкладки настроек -->
                <div class="settings-tabs">
                    <button type="button" class="settings-tab-btn active" data-tab="general">
                        <i class="fas fa-cog"></i> Основные
                    </button>
                    <button type="button" class="settings-tab-btn" data-tab="company">
                        <i class="fas fa-building"></i> Компания
                    </button>
                    <button type="button" class="settings-tab-btn" data-tab="notifications">
                        <i class="fas fa-bell"></i> Уведомления
                    </button>
                    <button type="button" class="settings-tab-btn" data-tab="security">
                        <i class="fas fa-shield-alt"></i> Безопасность
                    </button>
                    <button type="button" class="settings-tab-btn" data-tab="backup">
                        <i class="fas fa-database"></i> Резервное копирование
                    </button>
                </div>

                <!-- Вкладка: Основные настройки -->
                <div class="settings-tab-content active" id="tab-general">
                    <form method="POST" action="{{ route('settings.general') }}" class="settings-form">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="app_name">Название приложения</label>
                                <input type="text" id="app_name" name="app_name" class="form-control"
                                       value="{{ old('app_name', $settings['APP_NAME'] ?? config('app.name', 'SalaryCalc BY')) }}">
                                <small class="form-text text-muted">Отображается в заголовке страницы</small>
                            </div>
                            <div class="form-group">
                                <label for="app_url">URL приложения</label>
                                <input type="url" id="app_url" name="app_url" class="form-control"
                                       value="{{ old('app_url', $settings['APP_URL'] ?? config('app.url', 'http://localhost')) }}">
                                <small class="form-text text-muted">Базовый URL вашего приложения</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="app_timezone">Часовой пояс</label>
                                <select id="app_timezone" name="app_timezone" class="form-control">
                                    <option value="UTC" {{ ($settings['APP_TIMEZONE'] ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="Europe/Minsk" {{ ($settings['APP_TIMEZONE'] ?? 'UTC') == 'Europe/Minsk' ? 'selected' : '' }}>Europe/Minsk (Минск)</option>
                                    <option value="Europe/Moscow" {{ ($settings['APP_TIMEZONE'] ?? 'UTC') == 'Europe/Moscow' ? 'selected' : '' }}>Europe/Moscow (Москва)</option>
                                    <option value="Asia/Almaty" {{ ($settings['APP_TIMEZONE'] ?? 'UTC') == 'Asia/Almaty' ? 'selected' : '' }}>Asia/Almaty (Алматы)</option>
                                    <option value="Asia/Bishkek" {{ ($settings['APP_TIMEZONE'] ?? 'UTC') == 'Asia/Bishkek' ? 'selected' : '' }}>Asia/Bishkek (Бишкек)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="app_locale">Язык интерфейса</label>
                                <select id="app_locale" name="app_locale" class="form-control">
                                    <option value="ru" {{ ($settings['APP_LOCALE'] ?? 'ru') == 'ru' ? 'selected' : '' }}>Русский</option>
                                    <option value="en" {{ ($settings['APP_LOCALE'] ?? 'ru') == 'en' ? 'selected' : '' }}>English</option>
                                    <option value="be" {{ ($settings['APP_LOCALE'] ?? 'ru') == 'be' ? 'selected' : '' }}>Беларуская</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить настройки
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Вкладка: Компания -->
                <div class="settings-tab-content" id="tab-company">
                    <form method="POST" action="{{ route('settings.company') }}" class="settings-form" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="company_name">Название компании</label>
                                <input type="text" id="company_name" name="company_name" class="form-control"
                                       value="{{ old('company_name', $settings['COMPANY_NAME'] ?? 'SalaryCalc BY') }}">
                            </div>
                            <div class="form-group">
                                <label for="company_logo">Логотип компании</label>
                                <div class="logo-upload">
                                    @php
                                        $logoRaw = $settings['COMPANY_LOGO'] ?? '';
                                        $logoRaw = trim($logoRaw, '"\'');
                                        $hasLogo = !empty($logoRaw) && $logoRaw !== '' && file_exists(public_path($logoRaw));
                                    @endphp
                                    @if($hasLogo)
                                        <img id="logo-preview" src="{{ asset($logoRaw) }}" alt="Логотип" class="logo-preview">
                                    @else
                                        <div id="logo-preview" class="logo-preview-placeholder">
                                            <i class="fas fa-building fa-3x"></i>
                                        </div>
                                    @endif
                                    <div class="mt-2">
                                        <label class="btn btn-outline btn-sm">
                                            Выбрать файл
                                            <input type="file" name="company_logo" id="company_logo" accept="image/*" style="display: none;">
                                        </label>
                                        @if($hasLogo)
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteLogo()">
                                                <i class="fas fa-trash"></i> Удалить логотип
                                            </button>
                                        @endif
                                    </div>
                                    <small class="form-text text-muted">Поддерживаемые форматы: JPG, PNG. Максимальный размер: 2MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="company_email">Email компании</label>
                                <input type="email" id="company_email" name="company_email" class="form-control"
                                       value="{{ old('company_email', $settings['COMPANY_EMAIL'] ?? 'info@salarycalc.by') }}">
                            </div>
                            <div class="form-group">
                                <label for="company_phone">Телефон компании</label>
                                <input type="tel" id="company_phone" name="company_phone" class="form-control"
                                       value="{{ old('company_phone', $settings['COMPANY_PHONE'] ?? '+375 (17) 123-45-67') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_address">Адрес компании</label>
                            <textarea id="company_address" name="company_address" class="form-control" rows="3">{{ old('company_address', $settings['COMPANY_ADDRESS'] ?? 'г. Минск, ул. Компьютерная, 15, офис 101') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="company_tax_id">УНП компании</label>
                            <input type="text" id="company_tax_id" name="company_tax_id" class="form-control"
                                   value="{{ old('company_tax_id', $settings['COMPANY_TAX_ID'] ?? '123456789') }}">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить настройки
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Вкладка: Уведомления -->
                <div class="settings-tab-content" id="tab-notifications">
                    <form method="POST" action="{{ route('settings.notifications') }}" class="settings-form">
                        @csrf
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="notify_new_employee" class="form-check-input" value="1"
                                    {{ ($settings['NOTIFY_NEW_EMPLOYEE'] ?? 'true') == 'true' ? 'checked' : '' }}>
                                <span>Уведомлять при добавлении нового сотрудника</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="notify_salary_calc" class="form-check-input" value="1"
                                    {{ ($settings['NOTIFY_SALARY_CALC'] ?? 'true') == 'true' ? 'checked' : '' }}>
                                <span>Уведомлять о завершении расчета зарплаты</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="notify_tax_deadline" class="form-check-input" value="1"
                                    {{ ($settings['NOTIFY_TAX_DEADLINE'] ?? 'true') == 'true' ? 'checked' : '' }}>
                                <span>Уведомлять о сроках уплаты налогов</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="notify_contract_expiry" class="form-check-input" value="1"
                                    {{ ($settings['NOTIFY_CONTRACT_EXPIRY'] ?? 'false') == 'true' ? 'checked' : '' }}>
                                <span>Уведомлять об истечении срока контрактов</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="notification_email">Email для уведомлений</label>
                            <input type="email" id="notification_email" name="notification_email" class="form-control"
                                   value="{{ old('notification_email', $settings['NOTIFICATION_EMAIL'] ?? 'admin@mail.by') }}">
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить настройки
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Вкладка: Безопасность -->
                <div class="settings-tab-content" id="tab-security">
                    <form method="POST" action="{{ route('settings.security') }}" class="settings-form">
                        @csrf
                        <div class="form-group">
                            <label for="session_lifetime">Время жизни сессии (минут)</label>
                            <input type="number" id="session_lifetime" name="session_lifetime" class="form-control"
                                   value="{{ old('session_lifetime', $settings['SESSION_LIFETIME'] ?? '120') }}" min="1" max="1440">
                        </div>

                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="session_encrypt" class="form-check-input" value="1"
                                    {{ ($settings['SESSION_ENCRYPT'] ?? 'false') == 'true' ? 'checked' : '' }}>
                                <span>Шифровать данные сессии</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="password_min_length">Минимальная длина пароля</label>
                            <select id="password_min_length" name="password_min_length" class="form-control">
                                <option value="6" {{ ($settings['PASSWORD_MIN_LENGTH'] ?? '8') == '6' ? 'selected' : '' }}>6 символов</option>
                                <option value="8" {{ ($settings['PASSWORD_MIN_LENGTH'] ?? '8') == '8' ? 'selected' : '' }}>8 символов</option>
                                <option value="10" {{ ($settings['PASSWORD_MIN_LENGTH'] ?? '8') == '10' ? 'selected' : '' }}>10 символов</option>
                                <option value="12" {{ ($settings['PASSWORD_MIN_LENGTH'] ?? '8') == '12' ? 'selected' : '' }}>12 символов</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-check-label">
                                <input type="checkbox" name="require_strong_password" class="form-check-input" value="1"
                                    {{ ($settings['REQUIRE_STRONG_PASSWORD'] ?? 'true') == 'true' ? 'checked' : '' }}>
                                <span>Требовать сложный пароль (цифры, буквы, спецсимволы)</span>
                            </label>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Сохранить настройки
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Вкладка: Резервное копирование -->
                <div class="settings-tab-content" id="tab-backup">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Информация:</strong> Регулярное резервное копирование помогает защитить ваши данные.
                    </div>

                    <div class="form-actions" style="margin-top: 20px;">
                        <form method="POST" action="{{ route('settings.backup.create') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-database"></i> Создать резервную копию сейчас
                            </button>
                        </form>
                    </div>

                    <div class="mt-4">
                        <h5>Последние резервные копии</h5>
                        <div class="table-responsive">
                            <table class="table table-hover" id="backup-table">
                                <thead>
                                <tr>
                                    <th>Дата создания</th>
                                    <th>Размер</th>
                                    <th>Тип</th>
                                    <th>Действия</th>
                                </tr>
                                </thead>
                                <tbody id="backup-list">
                                <tr><td colspan="4" class="text-center text-muted">Загрузка...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/pages/settings.js') }}"></script>
@endpush
