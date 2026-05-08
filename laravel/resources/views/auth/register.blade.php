@extends('layouts.auth')

@section('title', 'SalaryCalc BY - Регистрация')

@section('content')
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">ФИО</label>
            <input type="text"
                   id="name"
                   name="name"
                   class="form-control"
                   value="{{ old('name') }}"
                   required
                   autofocus
                   placeholder="Введите ваше полное имя">
        </div>

        <div class="form-group">
            <label for="email">Email адрес</label>
            <input type="email"
                   id="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   required
                   placeholder="Введите ваш email">
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control"
                   required
                   placeholder="Придумайте пароль">
        </div>

        <div class="form-group">
            <label for="password_confirmation">Подтверждение пароля</label>
            <input type="password"
                   id="password_confirmation"
                   name="password_confirmation"
                   class="form-control"
                   required
                   placeholder="Повторите пароль">
        </div>

        <div class="form-group">
            <label for="role">Роль в системе</label>
            <select id="role" name="role" class="form-control" required>
                <option value="">Выберите роль</option>
                <option value="admin">Администратор</option>
                <option value="accountant">Бухгалтер</option>
                <option value="hr">Кадровик</option>
                <option value="manager">Начальник подразделения</option>
            </select>
            <small style="color: #666;">Роль определяет доступ к функциям системы</small>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-user-plus"></i>
            Зарегистрироваться
        </button>

        <div class="auth-links">
            Уже есть аккаунт? <a href="{{ route('login') }}">Войдите</a>
        </div>
    </form>
@endsection
