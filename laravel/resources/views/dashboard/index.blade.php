@extends('layouts.app')

@section('title', 'Главная панель - SalaryCalc BY')

@section('content')
    <div class="page active" id="dashboard">
        <!-- Приветствие -->
        <div class="welcome-card">
            <div class="welcome-content">
                <h1>Добро пожаловать, {{ auth()->user()->name }}!</h1>
                <p>Ваша роль: <strong>{{ auth()->user()->role_name }}</strong></p>
                <p class="welcome-text">Система управления предприятием готова к работе</p>
            </div>
            <div class="welcome-icon">
                <i class="fas fa-calculator"></i>
            </div>
        </div>

        @include('components.stats-cards')

        <!-- Контент в зависимости от роли -->
        @switch(auth()->user()->role)
            @case('admin')
                @include('dashboard.admin')
                @break
{{--            @case('accountant')--}}
{{--                @include('dashboard.accountant')--}}
{{--                @break--}}
{{--            @case('hr')--}}
{{--                @include('dashboard.hr')--}}
{{--                @break--}}
{{--            @case('manager')--}}
{{--                @include('dashboard.manager')--}}
{{--                @break--}}
        @endswitch
    </div>
@endsection
