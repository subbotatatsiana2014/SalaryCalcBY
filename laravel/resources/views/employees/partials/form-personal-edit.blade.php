<h4 class="mb-4"><i class="fas fa-user-circle"></i> Личные данные</h4>

<div class="form-group">
    <label for="avatar" class="form-label">Фото сотрудника</label>
    <div class="avatar-upload">
        <div class="avatar-preview">
            <img id="avatar-preview"
                 src="{{ $employee->avatar_url }}"
                 alt="Аватар"
                 class="avatar-image">
        </div>
        <div class="mt-2">
            <label class="btn btn-outline btn-sm">
                <i class="fas fa-upload"></i> Выбрать фото
                <input type="file" name="avatar" id="avatar-input" accept="image/*" style="display: none;">
            </label>
            <button type="button" class="btn btn-outline-danger btn-sm" id="remove-avatar" style="{{ $employee->avatar ? 'display: inline-flex' : 'display: none' }}">
                <i class="fas fa-trash"></i> Удалить
            </button>
        </div>
        <small class="form-text text-muted">Поддерживаемые форматы: JPG, PNG, GIF. Максимальный размер: 2MB</small>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="edit-last-name" class="form-label">Фамилия *</label>
        <input type="text" id="edit-last-name" name="last_name" class="form-control" value="{{ old('last_name', $employee->personalInfo->last_name ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="edit-first-name" class="form-label">Имя *</label>
        <input type="text" id="edit-first-name" name="first_name" class="form-control" value="{{ old('first_name', $employee->personalInfo->first_name ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="edit-middle-name" class="form-label">Отчество</label>
        <input type="text" id="edit-middle-name" name="middle_name" class="form-control" value="{{ old('middle_name', $employee->personalInfo->middle_name ?? '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="edit-gender" class="form-label">Пол *</label>
        <select id="edit-gender" name="gender" class="form-control" required>
            <option value="">Выберите пол</option>
            <option value="male" {{ old('gender', $employee->user->gender ?? '') == 'male' ? 'selected' : '' }}>Мужской</option>
            <option value="female" {{ old('gender', $employee->user->gender ?? '') == 'female' ? 'selected' : '' }}>Женский</option>
        </select>
    </div>

    <div class="form-group">
        <label for="edit-birth-date" class="form-label">Дата рождения *</label>
        <input type="date" id="edit-birth-date" name="birth_date" class="form-control" value="{{ old('birth_date', optional($employee->user->birth_date)->format('Y-m-d') ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="edit-birth-place" class="form-label">Место рождения</label>
        <input type="text" id="edit-birth-place" name="birth_place" class="form-control" value="{{ old('birth_place', $employee->personalInfo->birth_place ?? '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="edit-nationality" class="form-label">Гражданство</label>
        <input type="text" id="edit-nationality" name="nationality" class="form-control" value="{{ old('nationality', $employee->personalInfo->nationality ?? 'Беларусь') }}">
    </div>

    <div class="form-group">
        <label for="edit-marital-status" class="form-label">Семейное положение</label>
        <select id="edit-marital-status" name="marital_status" class="form-control">
            <option value="">Не указано</option>
            <option value="single" {{ old('marital_status', $employee->personalInfo->marital_status ?? '') == 'single' ? 'selected' : '' }}>Холост/Не замужем</option>
            <option value="married" {{ old('marital_status', $employee->personalInfo->marital_status ?? '') == 'married' ? 'selected' : '' }}>Женат/Замужем</option>
            <option value="divorced" {{ old('marital_status', $employee->personalInfo->marital_status ?? '') == 'divorced' ? 'selected' : '' }}>Разведен(а)</option>
            <option value="widowed" {{ old('marital_status', $employee->personalInfo->marital_status ?? '') == 'widowed' ? 'selected' : '' }}>Вдовец/Вдова</option>
        </select>
    </div>

    <div class="form-group">
        <label for="edit-children-count" class="form-label">Количество детей</label>
        <input type="number" id="edit-children-count" name="children_count" class="form-control" min="0" value="{{ old('children_count', $employee->personalInfo->children_count ?? 0) }}">
    </div>
</div>

