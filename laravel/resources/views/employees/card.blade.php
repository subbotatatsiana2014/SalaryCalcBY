@extends('layouts.app')

@section('title', 'Личная карточка сотрудника - ' . $employee->full_name)

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Личная карточка сотрудника</h3>
                        <p class="text-muted mb-0">ID: {{ $employee->id }}</p>
                    </div>
                    <div>
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Редактировать
                        </a>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Заголовок с основной информацией -->
                <div class="employee-header mb-5">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="employee-avatar-large">
                                <img src="https://i.pravatar.cc/100?u={{ $employee->user->email }}" alt="Аватар">
                            </div>
                        </div>
                        <div class="col">
                            <h2 class="mb-1">{{ $employee->full_name }}</h2>
                            <div class="text-muted mb-2">{{ $employee->position }}</div>
                            <div class="d-flex flex-wrap gap-3">
                            <span class="badge badge-{{ $employee->is_active ? 'success' : 'danger' }}">
                                {{ $employee->is_active ? 'Активен' : 'Неактивен' }}
                            </span>
                                @if($employee->department)
                                    <span class="badge" style="background: {{ $employee->department->color }}; color: white;">
                                {{ $employee->department->name }}
                            </span>
                                @endif
                                <span class="text-muted">
                                <i class="fas fa-calendar"></i> Принят: {{ $employee->hire_date->format('d.m.Y') }}
                            </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Вкладки -->
                <div class="tabs mb-4">
                    <button class="tab-btn active" data-tab="personal">Личные данные</button>
                    <button class="tab-btn" data-tab="work">Рабочая информация</button>
                    <button class="tab-btn" data-tab="documents">Документы</button>
                    <button class="tab-btn" data-tab="contacts">Контакты</button>
                </div>

                <!-- Содержимое вкладок -->
                <div class="tab-content active" id="tab-personal">
                    <!-- ... содержимое личных данных ... -->
                </div>

                <!-- Другие вкладки ... -->
            </div>
        </div>
    </div>
@endsection
