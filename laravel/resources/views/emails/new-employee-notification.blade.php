<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добро пожаловать в SalaryCalc BY</title>
    <link rel="stylesheet" href="{{ asset('css/pages/mail.css') }}?v={{ time() }}">
</head>
<body>
<div class="container">
    <div class="header-mail">
        <h1>Добро пожаловать в SalaryCalc BY!</h1>
        <p>Система управления персоналом</p>
    </div>

    <div class="content">
        <div class="welcome-message">
            <strong>Уважаемый(ая) {{ $user->name }},</strong>
            <p>Рады приветствовать вас в нашей команде! Для вас создана учетная запись в системе управления персоналом SalaryCalc BY.</p>
        </div>

        <div class="credentials">
            <h3 style="margin-top: 0; color: #495057;">Данные для входа:</h3>
            <div class="credential-item">
                <span class="credential-label">Email:</span>
                <span class="credential-value">{{ $user->email }}</span>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="button">Войти в систему</a>
        </div>

        <div class="info-box">
            <h4 style="margin-top: 0; color: #1976d2;">Важная информация:</h4>
            <p>✓ Обратитесь к администратору лично для получения пароля</p>
            <p>✓ При возникновении вопросов обращайтесь к администратору системы</p>
            <p>✓ В личном кабинете вы сможете просматривать информацию о зарплате, отпусках и больничных</p>
        </div>

        @if($user->employee && $user->employee->department)
            <div class="info-box" style="background: #e8f5e9; border-left-color: #4caf50;">
                <h4 style="margin-top: 0; color: #2e7d32;">Информация о работе:</h4>
                <p><strong>Подразделение:</strong> {{ $user->employee->department->name ?? 'Не назначено' }}</p>
                <p><strong>Должность:</strong> {{ $user->employee->position ?? 'Не указана' }}</p>
                <p><strong>Дата приема:</strong> {{ $user->employee->hire_date ? $user->employee->hire_date->format('d.m.Y') : 'Не указана' }}</p>
            </div>
        @endif
    </div>

    <div class="footer">
        <p>Это автоматическое сообщение, пожалуйста, не отвечайте на него.</p>
        <p style="color: #666; font-size: 12px; margin-top: 10px; margin-bottom: 60px">
            © {{ $currentYear }} {{ $companyName }}. Все права защищены.<br>
            По вопросам обращайтесь: <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
        </p>
    </div>
</div>
</body>
</html>
