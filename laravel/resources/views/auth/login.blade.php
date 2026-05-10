@extends('layouts.auth')

@section('title', 'SalaryCalc BY - Вход в систему')

@section('content')
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email адрес</label>
            <input type="email"
                   id="email"
                   name="email"
                   class="form-control"
                   value="{{ old('email') }}"
                   required
                   autofocus
                   placeholder="Введите ваш email">
        </div>

        <div class="form-group">
            <label for="password">Пароль</label>
            <input type="password"
                   id="password"
                   name="password"
                   class="form-control"
                   required
                   placeholder="Введите ваш пароль">
        </div>

        <div class="form-group" style="display: flex; align-items: center; justify-content: space-between;">
            <label style="margin: 0;">
                <input type="checkbox" name="remember">
                Запомнить меня
            </label>

            @if(Route::has('password.request'))
                <a href="{{ route('password.request') }}" style="font-size: 14px;">
                    Забыли пароль?
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary" style="display: flex; margin: 60px auto">
            <i class="fas fa-sign-in-alt"></i>
            Войти в систему
        </button>

        <div class="auth-footer">
            <p style="text-align: center; margin: 35px 0;">
                <a href="{{ route('register') }}" style="color: #3b82f6; text-decoration: none;">
                    <i class="fas fa-user-plus"></i> Создать новый аккаунт
                </a>
            </p>
        </div>
    </form>
@endsection
