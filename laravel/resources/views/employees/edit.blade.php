@extends('layouts.app')

@section('title', 'Редактирование сотрудника - ' . ($employee->full_name ?? $employee->user->name))

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Редактирование сотрудника</h3>
                        <p class="text-muted mb-0">ID: {{ $employee->id }}</p>
                    </div>
                    <div>
                        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-outline">
                            <i class="fas fa-eye"></i> Просмотр
                        </a>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="edit-employee-form" method="POST" action="{{ route('employees.update', $employee->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="tabs mb-4">
                        <button type="button" class="tab-btn active" data-tab="personal">Личные данные</button>
                        <button type="button" class="tab-btn" data-tab="work">Рабочая информация</button>
                        <button type="button" class="tab-btn" data-tab="documents">Документы</button>
                        <button type="button" class="tab-btn" data-tab="contacts">Контакты</button>
                    </div>

                    <!-- Вкладка: Личные данные -->
                    <div class="tab-content active" id="tab-personal">
                        @include('employees.partials.form-personal-edit', ['employee' => $employee])
                    </div>

                    <!-- Вкладка: Рабочая информация -->
                    <div class="tab-content" id="tab-work">
                        @include('employees.partials.form-work-edit', ['employee' => $employee, 'departments' => $departments ?? []])
                    </div>

                    <!-- Вкладка: Документы -->
                    <div class="tab-content" id="tab-documents">
                        @include('employees.partials.form-documents-edit', ['employee' => $employee])
                    </div>

                    <!-- Вкладка: Контакты -->
                    <div class="tab-content" id="tab-contacts">
                        @include('employees.partials.form-contacts-edit', ['employee' => $employee])
                    </div>

                    <div class="modal-footer mt-4">
                        <a href="{{ route('employees.index') }}" class="btn btn-outline">Отмена</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Инициализация вкладок
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');

                document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                    content.style.display = 'none';
                });

                this.classList.add('active');
                const activeTab = document.getElementById(`tab-${tabId}`);
                if (activeTab) {
                    activeTab.classList.add('active');
                    activeTab.style.display = 'block';
                }
            });
        });

        // Загрузка аватара
        const avatarInput = document.getElementById('avatar-input');
        const avatarPreview = document.getElementById('avatar-preview');
        const removeAvatarBtn = document.getElementById('remove-avatar');
        const employeeForm = document.getElementById('edit-employee-form');

        if (avatarInput && avatarPreview) {
            avatarInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    if (!file.type.match('image.*')) {
                        alert('Пожалуйста, выберите изображение');
                        this.value = '';
                        return;
                    }
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Размер файла не должен превышать 2MB');
                        this.value = '';
                        return;
                    }
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        avatarPreview.src = event.target.result;
                        if (removeAvatarBtn) removeAvatarBtn.style.display = 'inline-flex';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (removeAvatarBtn) {
            removeAvatarBtn.addEventListener('click', function() {
                avatarInput.value = '';
                avatarPreview.src = 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name={{ urlencode($employee->full_name ?? 'User') }}';
                this.style.display = 'none';

                let removeField = document.getElementById('remove_avatar');
                if (!removeField && employeeForm) {
                    removeField = document.createElement('input');
                    removeField.type = 'hidden';
                    removeField.name = 'remove_avatar';
                    removeField.id = 'remove_avatar';
                    removeField.value = '1';
                    employeeForm.appendChild(removeField);
                }
            });
        }

        if (avatarPreview && avatarPreview.src && !avatarPreview.src.includes('ui-avatars.com')) {
            if (removeAvatarBtn) removeAvatarBtn.style.display = 'inline-flex';
        }
    </script>
@endpush
