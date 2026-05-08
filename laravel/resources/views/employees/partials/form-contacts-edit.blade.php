<h4 class="mb-4"><i class="fas fa-address-book"></i> Контакты и дополнительные данные</h4>

<div class="form-row">
    <div class="form-group">
        <label for="edit-email" class="form-label">Email (логин) *</label>
        <input type="email" id="edit-email" name="email" class="form-control" value="{{ old('email', $employee->user->email ?? '') }}" required>
        <small class="form-text text-muted">Будет использоваться для входа в систему</small>
    </div>

    <div class="form-group">
        <label for="edit-phone" class="form-label">Телефон</label>
        <div class="input-with-icon">
            <i class="fas fa-phone"></i>
            <input type="tel" id="edit-phone" name="phone" class="form-control" value="{{ old('phone', $employee->user->phone ?? '') }}">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="edit-address" class="form-label">Адрес проживания</label>
    <input type="text" id="edit-address" name="address" class="form-control" value="{{ old('address', $employee->user->address ?? '') }}">
</div>

<div class="form-group">
    <label for="edit-work-phone" class="form-label">Рабочий телефон</label>
    <div class="input-with-icon">
        <i class="fas fa-phone-alt"></i>
        <input type="tel" id="edit-work-phone" name="work_phone" class="form-control" value="{{ old('work_phone', $employee->contactInfo->work_phone ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label for="edit-education" class="form-label">Образование</label>
    <select id="edit-education" name="education_level" class="form-control">
        <option value="">Не указано</option>
        <option value="secondary" {{ old('education_level', $employee->contactInfo->education_level ?? '') == 'secondary' ? 'selected' : '' }}>Среднее</option>
        <option value="secondary_special" {{ old('education_level', $employee->contactInfo->education_level ?? '') == 'secondary_special' ? 'selected' : '' }}>Среднее специальное</option>
        <option value="incomplete_higher" {{ old('education_level', $employee->contactInfo->education_level ?? '') == 'incomplete_higher' ? 'selected' : '' }}>Неоконченное высшее</option>
        <option value="higher" {{ old('education_level', $employee->contactInfo->education_level ?? '') == 'higher' ? 'selected' : '' }}>Высшее</option>
        <option value="master" {{ old('education_level', $employee->contactInfo->education_level ?? '') == 'master' ? 'selected' : '' }}>Магистр</option>
        <option value="phd" {{ old('education_level', $employee->contactInfo->education_level ?? '') == 'phd' ? 'selected' : '' }}>Кандидат наук</option>
        <option value="doctor" {{ old('education_level', $employee->contactInfo->education_level ?? '') == 'doctor' ? 'selected' : '' }}>Доктор наук</option>
    </select>
</div>

<div class="form-group">
    <label for="edit-skills" class="form-label">Ключевые навыки</label>
    <textarea id="edit-skills" name="skills" class="form-control" rows="3" placeholder="Навыки через запятую">{{ old('skills', $employee->contactInfo->skills ?? '') }}</textarea>
</div>

<div class="form-group">
    <label for="edit-languages" class="form-label">Языки</label>
    <textarea id="edit-languages" name="languages" class="form-control" rows="3" placeholder="Русский - родной&#10;Английский - средний">{{ old('languages', $employee->contactInfo->languages ?? '') }}</textarea>
</div>

<h5 class="mt-4 mb-3">Контактное лицо на экстренный случай</h5>

<div class="form-row">
    <div class="form-group">
        <label for="edit-emergency-name" class="form-label">ФИО контактного лица</label>
        <input type="text" id="edit-emergency-name" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $employee->emergencyContacts->name ?? '') }}">
    </div>

    <div class="form-group">
        <label for="edit-emergency-phone" class="form-label">Телефон</label>
        <input type="tel" id="edit-emergency-phone" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $employee->emergencyContacts->phone ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label for="edit-emergency-relation" class="form-label">Степень родства</label>
    <input type="text" id="edit-emergency-relation" name="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation', $employee->emergencyContacts->relation ?? '') }}">
</div>

<div class="form-group">
    <label for="edit-notes" class="form-label">Примечания</label>
    <textarea id="edit-notes" name="notes" class="form-control" rows="3" placeholder="Дополнительная информация о сотруднике...">{{ old('notes', $employee->contactInfo->notes ?? '') }}</textarea>
</div>
