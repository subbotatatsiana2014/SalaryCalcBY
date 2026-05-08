<h4 class="mb-4"><i class="fas fa-address-book"></i> Контакты и дополнительные данные</h4>

<div class="form-group">
    <label for="address" class="form-label">Адрес проживания</label>
    <input type="text" id="address" name="address" class="form-control" value="{{ old('address') }}">
</div>

<div class="form-row">
    <div class="form-group">
        <label for="phone" class="form-label">Телефон</label>
        <div class="input-with-icon">
            <i class="fas fa-phone"></i>
            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone') }}">
        </div>
    </div>

    <div class="form-group">
        <label for="work_phone" class="form-label">Рабочий телефон</label>
        <div class="input-with-icon">
            <i class="fas fa-phone-alt"></i>
            <input type="tel" id="work_phone" name="work_phone" class="form-control" value="{{ old('work_phone') }}">
        </div>
    </div>
</div>

<div class="form-group">
    <label for="education_level" class="form-label">Образование</label>
    <select id="education_level" name="education_level" class="form-control">
        <option value="">Не указано</option>
        <option value="secondary" {{ old('education_level') == 'secondary' ? 'selected' : '' }}>Среднее</option>
        <option value="secondary_special" {{ old('education_level') == 'secondary_special' ? 'selected' : '' }}>Среднее специальное</option>
        <option value="incomplete_higher" {{ old('education_level') == 'incomplete_higher' ? 'selected' : '' }}>Неоконченное высшее</option>
        <option value="higher" {{ old('education_level') == 'higher' ? 'selected' : '' }}>Высшее</option>
        <option value="master" {{ old('education_level') == 'master' ? 'selected' : '' }}>Магистр</option>
        <option value="phd" {{ old('education_level') == 'phd' ? 'selected' : '' }}>Кандидат наук</option>
        <option value="doctor" {{ old('education_level') == 'doctor' ? 'selected' : '' }}>Доктор наук</option>
    </select>
</div>

<div class="form-group">
    <label for="skills" class="form-label">Ключевые навыки</label>
    <textarea id="skills" name="skills" class="form-control" rows="3" placeholder="Навыки через запятую">{{ old('skills') }}</textarea>
</div>

<div class="form-group">
    <label for="languages" class="form-label">Языки</label>
    <textarea id="languages" name="languages" class="form-control" rows="3" placeholder="Русский - родной&#10;Английский - средний">{{ old('languages') }}</textarea>
</div>

<h5 class="mt-4 mb-3">Контактное лицо на экстренный случай</h5>

<div class="form-row">
    <div class="form-group">
        <label for="emergency_contact_name" class="form-label">ФИО контактного лица</label>
        <input type="text" id="emergency_contact_name" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name') }}">
    </div>

    <div class="form-group">
        <label for="emergency_contact_phone" class="form-label">Телефон</label>
        <input type="tel" id="emergency_contact_phone" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone') }}">
    </div>
</div>

<div class="form-group">
    <label for="emergency_contact_relation" class="form-label">Степень родства</label>
    <input type="text" id="emergency_contact_relation" name="emergency_contact_relation" class="form-control" value="{{ old('emergency_contact_relation') }}">
</div>

<div class="form-group">
    <label for="notes" class="form-label">Примечания</label>
    <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Дополнительная информация о сотруднике...">{{ old('notes') }}</textarea>
</div>
