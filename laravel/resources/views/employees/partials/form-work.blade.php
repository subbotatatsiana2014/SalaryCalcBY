<h4 class="mb-4"><i class="fas fa-briefcase"></i> Рабочая информация</h4>

<div class="form-row">
    <div class="form-group">
        <label for="department_id" class="form-label">Подразделение</label>
        <select id="department_id" name="department_id" class="form-control">
            <option value="">Не выбрано</option>
            @foreach($departments ?? [] as $department)
                <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="position" class="form-label">Должность *</label>
        <input type="text" id="position" name="position" class="form-control @error('position') is-invalid @enderror"
               value="{{ old('position') }}" required>
        @error('position')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="position_code" class="form-label">Код должности</label>
        <input type="text" id="position_code" name="position_code" class="form-control" value="{{ old('position_code') }}">
    </div>

    <div class="form-group">
        <label for="salary" class="form-label">Оклад (BYN) *</label>
        <div class="input-with-icon">
            <i class="fas fa-money-bill-wave"></i>
            <input type="number" id="salary" name="salary" class="form-control @error('salary') is-invalid @enderror"
                   step="0.01" min="0" value="{{ old('salary') }}" required>
        </div>
        @error('salary')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="hire_date" class="form-label">Дата приема *</label>
        <input type="date" id="hire_date" name="hire_date" class="form-control @error('hire_date') is-invalid @enderror"
               value="{{ old('hire_date', date('Y-m-d')) }}" required>
        @error('hire_date')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="probation_end_date" class="form-label">Окончание испытательного срока</label>
        <input type="date" id="probation_end_date" name="probation_end_date" class="form-control" value="{{ old('probation_end_date') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="employment_type" class="form-label">Тип занятости *</label>
        <select id="employment_type" name="employment_type" class="form-control" required>
            <option value="full" {{ old('employment_type', 'full') == 'full' ? 'selected' : '' }}>Полная занятость</option>
            <option value="part" {{ old('employment_type') == 'part' ? 'selected' : '' }}>Частичная занятость</option>
            <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Договор подряда</option>
            <option value="temporary" {{ old('employment_type') == 'temporary' ? 'selected' : '' }}>Временная</option>
            <option value="remote" {{ old('employment_type') == 'remote' ? 'selected' : '' }}>Удаленная работа</option>
        </select>
    </div>

    <div class="form-group">
        <label for="work_type" class="form-label">Тип работы</label>
        <select id="work_type" name="work_type" class="form-control">
            <option value="office" {{ old('work_type', 'office') == 'office' ? 'selected' : '' }}>Офис</option>
            <option value="hybrid" {{ old('work_type') == 'hybrid' ? 'selected' : '' }}>Гибридный</option>
            <option value="remote" {{ old('work_type') == 'remote' ? 'selected' : '' }}>Удаленно</option>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="work_schedule" class="form-label">График работы</label>
        <input type="text" id="work_schedule" name="work_schedule" class="form-control" value="{{ old('work_schedule') }}">
    </div>

    <div class="form-group">
        <label for="working_hours_per_week" class="form-label">Рабочих часов в неделю</label>
        <input type="number" id="working_hours_per_week" name="working_hours_per_week" class="form-control" min="1" max="168"
               value="{{ old('working_hours_per_week', 40) }}">
    </div>
</div>

<div class="form-group">
    <label class="form-check-label">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
        <span class="checkmark"></span>
        Активный сотрудник
    </label>
</div>
