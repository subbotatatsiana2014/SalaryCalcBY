<h4 class="mb-4"><i class="fas fa-user-circle"></i> Личные данные</h4>

<div class="form-group">
    <label for="avatar" class="form-label">Фото сотрудника</label>
    <div class="avatar-upload">
        <div class="avatar-preview">
            <img id="avatar-preview"
                 src="https://ui-avatars.com/api/?background=3b82f6&color=fff&name=User"
                 alt="Аватар"
                 class="avatar-image">
        </div>
        <div class="mt-2">
            <label class="btn btn-outline btn-sm">
                <i class="fas fa-upload"></i> Выбрать фото
                <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display: none;">
            </label>
            <button type="button" class="btn btn-outline-danger btn-sm" id="remove-avatar" style="display: none;">
                <i class="fas fa-trash"></i> Удалить
            </button>
        </div>
        <small class="form-text text-muted">Поддерживаемые форматы: JPG, PNG, GIF. Максимальный размер: 2MB</small>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="last_name" class="form-label">Фамилия *</label>
        <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
               value="{{ old('last_name') }}" required>
        @error('last_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="first_name" class="form-label">Имя *</label>
        <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
               value="{{ old('first_name') }}" required>
        @error('first_name')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="middle_name" class="form-label">Отчество</label>
        <input type="text" id="middle_name" name="middle_name" class="form-control" value="{{ old('middle_name') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="gender" class="form-label">Пол *</label>
        <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror" required>
            <option value="">Выберите пол</option>
            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Мужской</option>
            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Женский</option>
        </select>
        @error('gender')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="birth_date" class="form-label">Дата рождения *</label>
        <input type="date" id="birth_date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
               value="{{ old('birth_date') }}" required>
        @error('birth_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="birth_place" class="form-label">Место рождения</label>
        <input type="text" id="birth_place" name="birth_place" class="form-control" value="{{ old('birth_place') }}">
    </div>

    <div class="form-group">
        <label for="nationality" class="form-label">Гражданство</label>
        <input type="text" id="nationality" name="nationality" class="form-control" value="{{ old('nationality', 'Беларусь') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="marital_status" class="form-label">Семейное положение</label>
        <select id="marital_status" name="marital_status" class="form-control">
            <option value="">Не указано</option>
            <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Холост/Не замужем</option>
            <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Женат/Замужем</option>
            <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Разведен(а)</option>
            <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Вдовец/Вдова</option>
        </select>
    </div>

    <div class="form-group">
        <label for="children_count" class="form-label">Количество детей</label>
        <input type="number" id="children_count" name="children_count" class="form-control" min="0" value="{{ old('children_count', 0) }}">
    </div>
</div>

<div class="form-group">
    <label for="email" class="form-label">Email (логин) *</label>
    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
           value="{{ old('email') }}" required>
    <small class="form-text text-muted">Будет использоваться для входа в систему</small>
    @error('email')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-row">
    <div class="form-group">
        <label for="password" class="form-label">Пароль *</label>
        <div class="input-with-icon password-wrapper">
            <input type="password" id="password" name="password" class="form-control" required>
            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;"></i>
        </div>
        <small class="form-text text-muted">Минимум 8 символов</small>
        @error('password')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Подтверждение пароля *</label>
        <div class="input-with-icon password-wrapper">
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            <i class="fas fa-eye toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10;"></i>
        </div>
    </div>
</div>
