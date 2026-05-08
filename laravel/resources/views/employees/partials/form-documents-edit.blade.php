<h4 class="mb-4"><i class="fas fa-file-alt"></i> Документы</h4>

<div class="form-row">
    <div class="form-group">
        <label for="edit-passport-number" class="form-label">Номер паспорта</label>
        <input type="text" id="edit-passport-number" name="passport_number" class="form-control" value="{{ old('passport_number', $employee->documents->passport_number ?? '') }}">
    </div>

    <div class="form-group">
        <label for="edit-passport-issued-by" class="form-label">Кем выдан</label>
        <input type="text" id="edit-passport-issued-by" name="passport_issued_by" class="form-control" value="{{ old('passport_issued_by', $employee->documents->passport_issued_by ?? '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="edit-passport-issued-date" class="form-label">Дата выдачи</label>
        <input type="date" id="edit-passport-issued-date" name="passport_issued_date" class="form-control" value="{{ old('passport_issued_date', optional($employee->documents->passport_issued_date ?? null)->format('Y-m-d') ?? '') }}">
    </div>

    <div class="form-group">
        <label for="edit-passport-expiry-date" class="form-label">Срок действия</label>
        <input type="date" id="edit-passport-expiry-date" name="passport_expiry_date" class="form-control" value="{{ old('passport_expiry_date', optional($employee->documents->passport_expiry_date ?? null)->format('Y-m-d') ?? '') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="edit-tax-id" class="form-label">ИНН (УНП)</label>
        <input type="text" id="edit-tax-id" name="tax_id" class="form-control" value="{{ old('tax_id', $employee->documents->tax_id ?? '') }}">
    </div>

    <div class="form-group">
        <label for="edit-social-security-number" class="form-label">Номер соц. страхования</label>
        <input type="text" id="edit-social-security-number" name="social_security_number" class="form-control" value="{{ old('social_security_number', $employee->documents->social_security_number ?? '') }}">
    </div>
</div>

<div class="form-group">
    <label for="edit-bank-account" class="form-label">Банковский счет для зарплаты</label>
    <input type="text" id="edit-bank-account" name="bank_account" class="form-control" value="{{ old('bank_account', $employee->documents->bank_account ?? '') }}">
</div>

<div class="form-group">
    <label for="edit-bank-name" class="form-label">Наименование банка</label>
    <input type="text" id="edit-bank-name" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->documents->bank_name ?? '') }}">
</div>

<div class="form-group">
    <label for="edit-insurance-policy" class="form-label">Номер страхового полиса</label>
    <input type="text" id="edit-insurance-policy" name="insurance_policy_number" class="form-control" value="{{ old('insurance_policy_number', $employee->documents->insurance_policy_number ?? '') }}">
</div>
