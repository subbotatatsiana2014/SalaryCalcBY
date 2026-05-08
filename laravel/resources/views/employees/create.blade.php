{{-- resources/views/employees/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Добавление сотрудника')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3>Добавление нового сотрудника</h3>
                        <p class="text-muted mb-0">Создание личной карточки сотрудника</p>
                    </div>
                    <div>
                        <a href="{{ route('employees.index') }}" class="btn btn-outline">
                            <i class="fas fa-arrow-left"></i> Назад
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <form id="create-employee-form" method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                    @csrf

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="tabs mb-4">
                        <button type="button" class="tab-btn active" data-tab="personal">Личные данные</button>
                        <button type="button" class="tab-btn" data-tab="work">Рабочая информация</button>
                        <button type="button" class="tab-btn" data-tab="documents">Документы</button>
                        <button type="button" class="tab-btn" data-tab="contacts">Контакты</button>
                    </div>

                    <div class="tab-content active" id="tab-personal">
                        @include('employees.partials.form-personal')
                    </div>

                    <div class="tab-content" id="tab-work">
                        @include('employees.partials.form-work', ['departments' => $departments ?? []])
                    </div>

                    <div class="tab-content" id="tab-documents">
                        @include('employees.partials.form-documents')
                    </div>

                    <div class="tab-content" id="tab-contacts">
                        @include('employees.partials.form-contacts')
                    </div>

                    <div class="modal-footer mt-4">
                        <a href="{{ route('employees.index') }}" class="btn btn-outline">Отмена</a>
                        <button type="submit" class="btn btn-primary" id="create-submit-btn">
                            <i class="fas fa-user-plus"></i> Создать личную карточку
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

    // Показать первую вкладку
    const firstTab = document.querySelector('.tab-btn.active');
    if (firstTab) {
        const tabId = firstTab.getAttribute('data-tab');
        const activeTab = document.getElementById(`tab-${tabId}`);
        if (activeTab) {
        activeTab.style.display = 'block';
    }
    } else if (document.querySelector('.tab-btn')) {
        document.querySelector('.tab-btn').click();
    }

    // ========== ЗАГРУЗКА АВАТАРА ==========
    const avatarInput = document.getElementById('avatar-input');
    const avatarPreview = document.getElementById('avatar-preview');
    const removeAvatarBtn = document.getElementById('remove-avatar');
    const employeeForm = document.getElementById('create-employee-form'); // Исправлено: form -> employeeForm

    if (avatarInput && avatarPreview) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Проверка типа файла
                if (!file.type.match('image.*')) {
                    alert('Пожалуйста, выберите изображение');
                    this.value = '';
                    return;
                }

                // Проверка размера (2MB)
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

    // Удаление аватара
    if (removeAvatarBtn) {
        removeAvatarBtn.addEventListener('click', function() {
            avatarInput.value = '';
            avatarPreview.src = 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=User';
            this.style.display = 'none';

            // Добавляем скрытое поле для отметки об удалении
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

    // Показываем кнопку удаления если есть аватар
    if (avatarPreview && avatarPreview.src && !avatarPreview.src.includes('ui-avatars.com')) {
        if (removeAvatarBtn) removeAvatarBtn.style.display = 'inline-flex';
    }

    function setupPasswordToggles() {
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.removeEventListener('click', handlePasswordToggle);
            button.addEventListener('click', handlePasswordToggle);
        });
    }

    function handlePasswordToggle(e) {
        const button = e.currentTarget;
        const wrapper = button.closest('.password-wrapper');
        const input = wrapper.querySelector('input');

        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';
            button.classList.remove('fa-eye');
            button.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            button.classList.remove('fa-eye-slash');
            button.classList.add('fa-eye');
        }
    }

    // Инициализация после загрузки страницы
    document.addEventListener('DOMContentLoaded', function() {
        setupPasswordToggles();

        // Для динамических вкладок - перенастройка при переключении
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                setTimeout(setupPasswordToggles, 100);
            });
        });
    });
</script>
@endpush
