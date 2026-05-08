@extends('layouts.app')

@section('title', 'Редактирование пользователя - SalaryCalc BY')

@section('content')
    <div class="page">
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-user-edit"></i> Редактирование пользователя</h3>
                <a href="{{ route('users.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">ФИО *</label>
                            <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Оставьте пароль пустым, если не хотите его менять.
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Новый пароль</label>
                            <div class="input-with-icon password-wrapper">
                                <input type="password" id="password" name="password" class="form-control" required>
                                <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;"></i>
                            </div>
                            @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation">Подтверждение пароля</label>
                            <div class="input-with-icon password-wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="role">Роль *</label>
                            <select id="role" name="role" class="form-control @error('role') is-invalid @enderror" required>
                                <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Администратор</option>
                                <option value="accountant" {{ old('role', $user->role) == 'accountant' ? 'selected' : '' }}>Бухгалтер</option>
                                <option value="hr" {{ old('role', $user->role) == 'hr' ? 'selected' : '' }}>Кадровик</option>
                                <option value="manager" {{ old('role', $user->role) == 'manager' ? 'selected' : '' }}>Начальник</option>
                                <option value="employee" {{ old('role', $user->role) == 'employee' ? 'selected' : '' }}>Сотрудник</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="phone">Телефон</label>
                            <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="birth_date">Дата рождения</label>
                            <input type="date" id="birth_date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', optional($user->birth_date)->format('Y-m-d')) }}">
                            @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gender">Пол</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="">Не указан</option>
                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Мужской</option>
                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Женский</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Адрес</label>
                        <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('users.index') }}" class="btn btn-outline">Отмена</a>
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
        function togglePasswordVisibility(inputId, icon) {
            console.log('Input ID:', inputId);
            console.log('Icon element:', icon);

            const passwordInput = document.getElementById(inputId);
            console.log('Password input:', passwordInput);

            if (!passwordInput) {
                console.error('Element not found! Check ID:', inputId);
                return;
            }

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                console.log('Password visible');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                console.log('Password hidden');
            }
        }
    </script>
@endpush
