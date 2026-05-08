@extends('layouts.app')

@section('title', 'Просмотр пользователя - SalaryCalc BY')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user"></i> Просмотр пользователя</h3>
                <div>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Редактировать
                    </a>
                    <a href="{{ route('users.index') }}" class="btn btn-outline">
                        <i class="fas fa-arrow-left"></i> Назад
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="user-profile-header">
                    <div class="user-avatar-large">
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    </div>
                    <div class="user-profile-info">
                        <h2>{{ $user->name }}</h2>
                        <p><i class="fas fa-envelope"></i> {{ $user->email }}</p>
                        <p><i class="fas fa-tag"></i> Роль: <strong>{{ \App\Models\User::getAvailableRoles()[$user->role] ?? $user->role }}</strong></p>
                    </div>
                </div>

                <div class="user-details-grid">
                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-phone"></i>
                            <h4>Контакты</h4>
                        </div>
                        <div class="info-card-content">
                            <p><strong>Телефон:</strong> {{ $user->phone ?? 'Не указан' }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-calendar"></i>
                            <h4>Личная информация</h4>
                        </div>
                        <div class="info-card-content">
                            <p><strong>Дата рождения:</strong> {{ $user->birth_date ? $user->birth_date->format('d.m.Y') : 'Не указана' }}</p>
                            <p><strong>Пол:</strong> {{ $user->gender === 'male' ? 'Мужской' : ($user->gender === 'female' ? 'Женский' : 'Не указан') }}</p>
                            <p><strong>Адрес:</strong> {{ $user->address ?? 'Не указан' }}</p>
                        </div>
                    </div>

                    <div class="info-card">
                        <div class="info-card-header">
                            <i class="fas fa-clock"></i>
                            <h4>Системная информация</h4>
                        </div>
                        <div class="info-card-content">
                            <p><strong>Дата регистрации:</strong> {{ $user->created_at ? $user->created_at->format('d.m.Y H:i') : '—' }}</p>
                            <p><strong>Последний вход:</strong> {{ $user->last_login_at ? $user->last_login_at->format('d.m.Y H:i') : '—' }}</p>
                        </div>
                    </div>

                    @if($user->employee)
                        <div class="info-card">
                            <div class="info-card-header">
                                <i class="fas fa-briefcase"></i>
                                <h4>Рабочая информация</h4>
                            </div>
                            <div class="info-card-content">
                                <p><strong>Должность:</strong> {{ $user->employee->position ?? '—' }}</p>
                                <p><strong>Подразделение:</strong> {{ $user->employee->department->name ?? '—' }}</p>
                                <p><strong>Дата приема:</strong> {{ $user->employee->hire_date ? $user->employee->hire_date->format('d.m.Y') : '—' }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
