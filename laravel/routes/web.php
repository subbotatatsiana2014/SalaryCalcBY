<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\TaxController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Главная страница и аутентификация
Route::get('/', [DashboardController::class, 'index'])->middleware(['auth'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth']);

// Маршруты аутентификации
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Сотрудники
Route::middleware(['auth', 'role:admin,hr,manager'])->group(function () {
    // Основные CRUD маршруты
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
    Route::get('/employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::post('/employees/{employee}/restore', [EmployeeController::class, 'restore'])->name('employees.restore');
    Route::get('/employees/search/ajax', [EmployeeController::class, 'search'])->name('employees.search');
});

// Расчет зарплаты
Route::middleware(['auth', 'role:admin,accountant'])->group(function () {
    Route::get('/salary', [SalaryController::class, 'index'])->name('salary.index');
    Route::get('/salary/calculate', [SalaryController::class, 'calculate'])->name('salary.calculate');
    Route::get('/salary/history', [SalaryController::class, 'history'])->name('salary.history');
    Route::get('/salary/results/{id}', [SalaryController::class, 'results'])->name('salary.results');
    Route::post('/salary/preview', [SalaryController::class, 'preview'])->name('salary.preview');
    Route::post('/salary/save', [SalaryController::class, 'saveCalculation'])->name('salary.save');
    Route::get('/salary/export/{id}', [SalaryController::class, 'export'])->name('salary.export');
    Route::get('/salary/employees/by-department', [SalaryController::class, 'getEmployees'])->name('salary.employees');
    Route::get('/salary/tax-settings', [SalaryController::class, 'taxSettings'])->name('salary.tax-settings');
    Route::put('/salary/tax-rate/{id}', [SalaryController::class, 'updateTaxRate'])->name('salary.update-tax-rate');
    Route::put('/salary/deduction-setting/{id}', [SalaryController::class, 'updateDeductionSetting'])->name('salary.update-deduction');
    Route::post('/salary/payslip', [SalaryController::class, 'getPayslipData'])->name('salary.payslip');
    Route::get('/mortgage/{employeeId}', [SalaryController::class, 'getMortgageInfo'])->name('salary.mortgage.info');
    Route::post('/mortgage', [SalaryController::class, 'saveMortgageDeduction'])->name('salary.mortgage.save');
    Route::get('/salary/mortgage/{employeeId}', [SalaryController::class, 'mortgageEdit'])->name('salary.mortgage.edit');
    Route::get('/mortgage-list', [SalaryController::class, 'mortgageList'])->name('salary.mortgage.list');
});

// Налоги и платежи
Route::middleware(['auth', 'role:admin,accountant'])->group(function () {
    Route::resource('taxes', TaxController::class);
});

// Отчеты
Route::middleware(['auth', 'role:admin,accountant'])->group(function () {
    Route::resource('reports', ReportController::class);
});

// Подразделения
Route::resource('departments', DepartmentController::class);
Route::get('/departments/{department}/employees', [DepartmentController::class, 'employees'])->name('departments.employees');
Route::get('/departments/tree', [DepartmentController::class, 'tree'])->name('departments.tree');

// Настройки (только админ)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Маршруты для сохранения
    Route::post('/settings/general', [SettingsController::class, 'saveGeneral'])->name('settings.general');
    Route::post('/settings/company', [SettingsController::class, 'saveCompany'])->name('settings.company');
    Route::post('/settings/notifications', [SettingsController::class, 'saveNotifications'])->name('settings.notifications');
    Route::post('/settings/security', [SettingsController::class, 'saveSecurity'])->name('settings.security');

    // Удаление логотипа
    Route::delete('/settings/logo', [SettingsController::class, 'deleteLogo'])->name('settings.logo.delete');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
});
