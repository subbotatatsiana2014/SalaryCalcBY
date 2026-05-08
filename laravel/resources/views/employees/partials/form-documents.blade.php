<h4 class="mb-4"><i class="fas fa-file-alt"></i> Документы</h4>

<div class="form-row">
    <div class="form-group">
        <label for="passport_number" class="form-label">Номер паспорта</label>
        <input type="text" id="passport_number" name="passport_number" class="form-control" value="{{ old('passport_number') }}">
    </div>

    <div class="form-group">
        <label for="passport_issued_by" class="form-label">Кем выдан</label>
        <input type="text" id="passport_issued_by" name="passport_issued_by" class="form-control" value="{{ old('passport_issued_by') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="passport_issued_date" class="form-label">Дата выдачи</label>
        <input type="date" id="passport_issued_date" name="passport_issued_date" class="form-control" value="{{ old('passport_issued_date') }}">
    </div>

    <div class="form-group">
        <label for="passport_expiry_date" class="form-label">Срок действия</label>
        <input type="date" id="passport_expiry_date" name="passport_expiry_date" class="form-control" value="{{ old('passport_expiry_date') }}">
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label for="tax_id" class="form-label">ИНН (УНП)</label>
        <input type="text" id="tax_id" name="tax_id" class="form-control" value="{{ old('tax_id') }}">
    </div>

    <div class="form-group">
        <label for="social_security_number" class="form-label">Номер соц. страхования</label>
        <input type="text" id="social_security_number" name="social_security_number" class="form-control" value="{{ old('social_security_number') }}">
    </div>
</div>

<div class="form-group">
    <label for="bank_account" class="form-label">Банковский счет для зарплаты</label>
    <input type="text" id="bank_account" name="bank_account" class="form-control" value="{{ old('bank_account') }}">
</div>

<div class="form-group">
    <label for="bank_name" class="form-label">Наименование банка</label>
    <input type="text" id="bank_name" name="bank_name" class="form-control" value="{{ old('bank_name') }}">
</div>

<div class="form-group">
    <label for="insurance_policy_number" class="form-label">Номер страхового полиса</label>
    <input type="text" id="insurance_policy_number" name="insurance_policy_number" class="form-control" value="{{ old('insurance_policy_number') }}">
</div>
