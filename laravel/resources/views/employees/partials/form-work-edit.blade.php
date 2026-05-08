<h4 class="mb-4"><i class="fas fa-briefcase"></i> Рабочая информация</h4>

<div class="form-row">
    <div class="form-group">
        <label for="department" class="form-label">Подразделение</label>
        <select id="department" name="department_id" class="form-control">
            <option value="">Не выбрано</option>
            @foreach($departments ?? [] as $department)
                <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id ?? '') == $department->id ? 'selected' : '' }}>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="position" class="form-label">Должность *</label>
        <input type="text" id="position" name="position" class="form-control" value="{{ old('position', $employee->position ?? '') }}" required>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="position-code" class="form-label">Код должности</label>
        <input type="text" id="position-code" name="position_code" class="form-control" value="{{ old('position_code', $employee->position_code ?? '') }}">
    </div>

    <div class="form-group">
        <label for="salary" class="form-label">Оклад (BYN) *</label>
        <div class="input-with-icon">
            <i class="fas fa-money-bill-wave"></i>
            <input type="number" id="salary" name="salary" class="form-control" step="0.01" min="0" value="{{ old('salary', $employee->salary ?? 0) }}" required>
        </div>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="hire-date" class="form-label">Дата приема *</label>
        <input type="date" id="hire-date" name="hire_date" class="form-control" value="{{ old('hire_date', optional($employee->hire_date)->format('Y-m-d') ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="probation-end-date" class="form-label">Окончание испытательного срока</label>
        <input type="date" id="probation-end-date" name="probation_end_date" class="form-control" value="{{ old('probation_end_date', optional($employee->probation_end_date)->format('Y-m-d') ?? '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="employment-type" class="form-label">Тип занятости *</label>
        <select id="employment-type" name="employment_type" class="form-control" required>
            <option value="full" {{ old('employment_type', $employee->employment_type ?? '') == 'full' ? 'selected' : '' }}>Полная занятость</option>
            <option value="part" {{ old('employment_type', $employee->employment_type ?? '') == 'part' ? 'selected' : '' }}>Частичная занятость</option>
            <option value="contract" {{ old('employment_type', $employee->employment_type ?? '') == 'contract' ? 'selected' : '' }}>Договор подряда</option>
            <option value="temporary" {{ old('employment_type', $employee->employment_type ?? '') == 'temporary' ? 'selected' : '' }}>Временная</option>
            <option value="remote" {{ old('employment_type', $employee->employment_type ?? '') == 'remote' ? 'selected' : '' }}>Удаленная работа</option>
        </select>
    </div>

    <div class="form-group">
        <label for="work-type" class="form-label">Тип работы</label>
        <select id="work-type" name="work_type" class="form-control">
            <option value="office" {{ old('work_type', $employee->work_type ?? '') == 'office' ? 'selected' : '' }}>Офис</option>
            <option value="hybrid" {{ old('work_type', $employee->work_type ?? '') == 'hybrid' ? 'selected' : '' }}>Гибридный</option>
            <option value="remote" {{ old('work_type', $employee->work_type ?? '') == 'remote' ? 'selected' : '' }}>Удаленно</option>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="work-schedule" class="form-label">График работы</label>
        <input type="text" id="work-schedule" name="work_schedule" class="form-control" value="{{ old('work_schedule', $employee->work_schedule ?? '') }}">
    </div>

    <div class="form-group">
        <label for="working-hours" class="form-label">Рабочих часов в неделю</label>
        <input type="number" id="working-hours" name="working_hours_per_week" class="form-control" min="1" max="168" value="{{ old('working_hours_per_week', $employee->working_hours_per_week ?? 40) }}">
    </div>
</div>

<div class="form-group">
    <label class="form-check-label">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" class="form-check-input" value="1" {{ old('is_active', $employee->is_active ?? true) ? 'checked' : '' }}>
        Активный сотрудник
    </label>
</div>
