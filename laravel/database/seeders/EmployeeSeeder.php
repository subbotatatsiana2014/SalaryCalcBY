<?php

namespace Database\Seeders;

use App\Models\Employee\Employee;
use App\Models\Employee\EmployeePersonalInfo;
use App\Models\Employee\EmployeeContactInfo;
use App\Models\Employee\EmployeeDocument;
use App\Models\Employee\EmployeeEmergencyContact;
use App\Models\User;
use App\Models\Department\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        // Получаем ID подразделений
        $ceoDept = Department::where('code', 'CEO')->first()->id;
        $adminDept = Department::where('code', 'ADMIN')->first()->id;
        $hrDept = Department::where('code', 'HR')->first()->id;
        $financeDept = Department::where('code', 'FIN')->first()->id;
        $techDept = Department::where('code', 'TECH')->first()->id;
        $backendDept = Department::where('code', 'BACKEND')->first()->id;
        $frontendDept = Department::where('code', 'FRONTEND')->first()->id;
        $mobileDept = Department::where('code', 'MOBILE')->first()->id;
        $devopsDept = Department::where('code', 'DEVOPS')->first()->id;
        $qaDept = Department::where('code', 'QA')->first()->id;
        $pmDept = Department::where('code', 'PM')->first()->id;
        $supportDept = Department::where('code', 'SUPPORT')->first()->id;

        // ============================================
        // РУКОВОДСТВО
        // ============================================

        // 1. Генеральный директор
        $user1 = User::firstOrCreate(
            ['email' => 'ceo_head@mail.by'],
            [
                'name' => 'Иванов Иван Иванович',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+375 (29) 111-11-11',
                'birth_date' => '1980-01-15',
                'gender' => 'male',
                'address' => 'г. Минск, ул. Ленина, 10, кв. 25',
            ]
        );

        $employee1 = Employee::firstOrCreate(
            ['user_id' => $user1->id],
            [
                'department_id' => $ceoDept,
                'position' => 'Генеральный директор',
                'position_code' => 'CEO-001',
                'salary' => 15000.00,
                'hire_date' => '2018-01-10',
                'fire_date' => null,
                'probation_end_date' => '2018-04-10',
                'employment_type' => 'full',
                'work_type' => 'office',
                'work_schedule' => '5/2, 09:00-18:00',
                'working_hours_per_week' => 40,
                'is_active' => true,
            ]
        );

        EmployeePersonalInfo::firstOrCreate(
            ['employee_id' => $employee1->id],
            [
                'last_name' => 'Иванов',
                'first_name' => 'Иван',
                'middle_name' => 'Иванович',
                'birth_place' => 'г. Минск',
                'nationality' => 'Беларусь',
                'marital_status' => 'married',
                'children_count' => 2,
            ]
        );

        EmployeeContactInfo::firstOrCreate(
            ['employee_id' => $employee1->id],
            [
                'work_phone' => '+375 (17) 123-00-01',
                'education_level' => 'higher',
                'skills' => 'Стратегическое управление, управление персоналом, финансовый менеджмент',
                'languages' => 'Русский (родной), Английский (B2)',
                'notes' => 'Основатель компании',
            ]
        );

        EmployeeDocument::firstOrCreate(
            ['employee_id' => $employee1->id],
            [
                'passport_number' => 'MP1234567',
                'passport_issued_by' => 'УВД Центрального района г. Минска',
                'passport_issued_date' => '2010-03-15',
                'passport_expiry_date' => '2030-03-15',
                'tax_id' => '123456789',
                'social_security_number' => '123-45-6789',
                'bank_account' => 'BY80OLMP30120000123456789012',
                'bank_name' => 'ОАО "Белинвестбанк"',
                'insurance_policy_number' => '0987654321',
            ]
        );

        EmployeeEmergencyContact::firstOrCreate(
            ['employee_id' => $employee1->id],
            [
                'name' => 'Иванова Мария Петровна',
                'phone' => '+375 (29) 111-11-12',
                'relation' => 'Супруга',
            ]
        );

        // 2. Финансовый директор
        $user2 = User::firstOrCreate(
            ['email' => 'cfo_head@mail.by'],
            [
                'name' => 'Петрова Елена Владимировна',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+375 (29) 222-22-22',
                'birth_date' => '1982-03-20',
                'gender' => 'female',
                'address' => 'г. Минск, ул. Красная, 5, кв. 48',
            ]
        );

        $employee2 = Employee::firstOrCreate(
            ['user_id' => $user2->id],
            [
                'department_id' => $financeDept,
                'position' => 'Финансовый директор',
                'position_code' => 'CFO-001',
                'salary' => 12000.00,
                'hire_date' => '2018-06-01',
                'fire_date' => null,
                'probation_end_date' => '2018-09-01',
                'employment_type' => 'full',
                'work_type' => 'office',
                'work_schedule' => '5/2, 09:00-18:00',
                'working_hours_per_week' => 40,
                'is_active' => true,
            ]
        );

        EmployeePersonalInfo::firstOrCreate(
            ['employee_id' => $employee2->id],
            [
                'last_name' => 'Петрова',
                'first_name' => 'Елена',
                'middle_name' => 'Владимировна',
                'birth_place' => 'г. Минск',
                'nationality' => 'Беларусь',
                'marital_status' => 'married',
                'children_count' => 1,
            ]
        );

        EmployeeContactInfo::firstOrCreate(
            ['employee_id' => $employee2->id],
            [
                'work_phone' => '+375 (17) 123-00-10',
                'education_level' => 'master',
                'skills' => 'Финансовый анализ, бюджетирование, налогообложение',
                'languages' => 'Русский (родной), Английский (C1)',
                'notes' => 'Сертифицированный бухгалтер',
            ]
        );

        EmployeeDocument::firstOrCreate(
            ['employee_id' => $employee2->id],
            [
                'passport_number' => 'MP2345678',
                'passport_issued_by' => 'УВД Октябрьского района г. Минска',
                'passport_issued_date' => '2012-05-10',
                'passport_expiry_date' => '2032-05-10',
                'tax_id' => '234567890',
                'social_security_number' => '234-56-7890',
                'bank_account' => 'BY80OLMP30120000234567890123',
                'bank_name' => 'ОАО "АСБ Беларусбанк"',
                'insurance_policy_number' => '1098765432',
            ]
        );

        EmployeeEmergencyContact::firstOrCreate(
            ['employee_id' => $employee2->id],
            [
                'name' => 'Петров Александр Игоревич',
                'phone' => '+375 (29) 222-22-23',
                'relation' => 'Супруг',
            ]
        );

        // 3. Технический директор (CTO)
        $user3 = User::firstOrCreate(
            ['email' => 'cto_head@mail.by'],
            [
                'name' => 'Сидоров Алексей Николаевич',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'phone' => '+375 (29) 333-33-33',
                'birth_date' => '1979-07-10',
                'gender' => 'male',
                'address' => 'г. Минск, ул. Сурганова, 15, кв. 72',
            ]
        );

        $employee3 = Employee::firstOrCreate(
            ['user_id' => $user3->id],
            [
                'department_id' => $techDept,
                'position' => 'Технический директор (CTO)',
                'position_code' => 'CTO-001',
                'salary' => 14000.00,
                'hire_date' => '2018-03-15',
                'fire_date' => null,
                'probation_end_date' => '2018-06-15',
                'employment_type' => 'full',
                'work_type' => 'office',
                'work_schedule' => '5/2, 09:00-18:00',
                'working_hours_per_week' => 40,
                'is_active' => true,
            ]
        );

        EmployeePersonalInfo::firstOrCreate(
            ['employee_id' => $employee3->id],
            [
                'last_name' => 'Сидоров',
                'first_name' => 'Алексей',
                'middle_name' => 'Николаевич',
                'birth_place' => 'г. Минск',
                'nationality' => 'Беларусь',
                'marital_status' => 'married',
                'children_count' => 3,
            ]
        );

        EmployeeContactInfo::firstOrCreate(
            ['employee_id' => $employee3->id],
            [
                'work_phone' => '+375 (17) 123-00-20',
                'education_level' => 'phd',
                'skills' => 'Архитектура ПО, управление разработкой, DevOps, Cloud Architecture',
                'languages' => 'Русский (родной), Английский (C2)',
                'notes' => 'Имеет патент на изобретение',
            ]
        );

        EmployeeDocument::firstOrCreate(
            ['employee_id' => $employee3->id],
            [
                'passport_number' => 'MP3456789',
                'passport_issued_by' => 'УВД Фрунзенского района г. Минска',
                'passport_issued_date' => '2014-07-20',
                'passport_expiry_date' => '2034-07-20',
                'tax_id' => '345678901',
                'social_security_number' => '345-67-8901',
                'bank_account' => 'BY80OLMP30120000345678901234',
                'bank_name' => 'ЗАО "МТБанк"',
                'insurance_policy_number' => '2109876543',
            ]
        );

        EmployeeEmergencyContact::firstOrCreate(
            ['employee_id' => $employee3->id],
            [
                'name' => 'Сидорова Анна Викторовна',
                'phone' => '+375 (29) 333-33-34',
                'relation' => 'Супруга',
            ]
        );

        // 4. HR-директор
        $user4 = User::where('email', 'hr@mail.by')->first();

        if ($user4) {
            $employee4 = Employee::firstOrCreate(
                ['user_id' => $user4->id],
                [
                    'department_id' => $hrDept,
                    'position' => 'HR-директор',
                    'position_code' => 'HRD-001',
                    'salary' => 11000.00,
                    'hire_date' => '2018-09-01',
                    'fire_date' => null,
                    'probation_end_date' => '2018-12-01',
                    'employment_type' => 'full',
                    'work_type' => 'office',
                    'work_schedule' => '5/2, 09:00-18:00',
                    'working_hours_per_week' => 40,
                    'is_active' => true,
                ]
            );

            EmployeePersonalInfo::firstOrCreate(
                ['employee_id' => $employee4->id],
                [
                    'last_name' => 'Козлова',
                    'first_name' => 'Татьяна',
                    'middle_name' => 'Дмитриевна',
                    'birth_place' => 'г. Гомель',
                    'nationality' => 'Беларусь',
                    'marital_status' => 'divorced',
                    'children_count' => 1,
                ]
            );

            EmployeeContactInfo::firstOrCreate(
                ['employee_id' => $employee4->id],
                [
                    'work_phone' => '+375 (17) 123-00-30',
                    'education_level' => 'higher',
                    'skills' => 'Управление персоналом, HR стратегии, рекрутинг, оценка персонала',
                    'languages' => 'Русский (родной), Английский (B2)',
                    'notes' => '',
                ]
            );

            EmployeeDocument::firstOrCreate(
                ['employee_id' => $employee4->id],
                [
                    'passport_number' => 'MP4567890',
                    'passport_issued_by' => 'УВД Центрального района г. Гомеля',
                    'passport_issued_date' => '2015-09-15',
                    'passport_expiry_date' => '2035-09-15',
                    'tax_id' => '456789012',
                    'social_security_number' => '456-78-9012',
                    'bank_account' => 'BY80OLMP30120000456789012345',
                    'bank_name' => 'ОАО "БПС-Сбербанк"',
                    'insurance_policy_number' => '3210987654',
                ]
            );

            EmployeeEmergencyContact::firstOrCreate(
                ['employee_id' => $employee4->id],
                [
                    'name' => 'Козлов Дмитрий Петрович',
                    'phone' => '+375 (29) 444-44-45',
                    'relation' => 'Брат',
                ]
            );
        }

        // ============================================
        // BACKEND-РАЗРАБОТЧИКИ (10 человек)
        // ============================================

        $backendNames = [
            ['Николаев', 'Андрей', 'Сергеевич', 'backend1@mail.by', '+375 (29) 511-11-11', 'Senior Backend Developer', 'SEN-BE-001', 4500, '2019-02-01', 'male'],
            ['Волков', 'Дмитрий', 'Алексеевич', 'backend2@mail.by', '+375 (29) 522-22-22', 'Backend Developer', 'MID-BE-002', 3200, '2020-03-15', 'male'],
            ['Морозов', 'Павел', 'Игоревич', 'backend3@mail.by', '+375 (29) 533-33-33', 'Backend Developer', 'JUN-BE-003', 2200, '2021-06-01', 'male'],
            ['Соколов', 'Илья', 'Владимирович', 'backend4@mail.by', '+375 (29) 544-44-44', 'Backend Developer', 'MID-BE-004', 3400, '2020-09-10', 'male'],
            ['Михайлов', 'Кирилл', 'Андреевич', 'backend5@mail.by', '+375 (29) 555-55-55', 'Senior Backend Developer', 'SEN-BE-005', 4800, '2018-11-20', 'male'],
            ['Федоров', 'Антон', 'Дмитриевич', 'backend6@mail.by', '+375 (29) 566-66-66', 'Backend Developer', 'MID-BE-006', 3300, '2021-02-14', 'male'],
            ['Алексеев', 'Роман', 'Иванович', 'backend7@mail.by', '+375 (29) 577-77-77', 'Junior Backend Developer', 'JUN-BE-007', 2000, '2022-04-01', 'male'],
            ['Егоров', 'Никита', 'Александрович', 'backend8@mail.by', '+375 (29) 588-88-88', 'Backend Developer', 'MID-BE-008', 3100, '2020-07-15', 'male'],
            ['Семенов', 'Артем', 'Витальевич', 'backend9@mail.by', '+375 (29) 599-99-99', 'Senior Backend Developer', 'SEN-BE-009', 5000, '2019-05-20', 'male'],
            ['Тарасов', 'Денис', 'Сергеевич', 'backend10@mail.by', '+375 (44) 511-11-11', 'Backend Developer', 'MID-BE-010', 3600, '2021-08-01', 'male'],
        ];

        foreach ($backendNames as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data[3]],
                [
                    'name' => $data[0] . ' ' . $data[1] . ' ' . $data[2],
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'phone' => $data[4],
                    'birth_date' => Carbon::create(1985 + $i, rand(1, 12), rand(1, 28)),
                    'gender' => $data[9],
                    'address' => 'г. Минск, ул. ' . chr(65 + $i) . ', ' . (10 + $i) . ', кв. ' . (20 + $i),
                ]
            );

            $employee = Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id' => $backendDept,
                    'position' => $data[5],
                    'position_code' => $data[6],
                    'salary' => $data[7],
                    'hire_date' => $data[8],
                    'fire_date' => null,
                    'probation_end_date' => Carbon::parse($data[8])->addMonths(3),
                    'employment_type' => 'full',
                    'work_type' => 'remote',
                    'work_schedule' => '5/2, 09:00-18:00',
                    'working_hours_per_week' => 40,
                    'is_active' => true,
                ]
            );

            EmployeePersonalInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'last_name' => $data[0],
                    'first_name' => $data[1],
                    'middle_name' => $data[2],
                    'birth_place' => 'г. Минск',
                    'nationality' => 'Беларусь',
                    'marital_status' => rand(0, 1) ? 'married' : 'single',
                    'children_count' => rand(0, 2),
                ]
            );

            EmployeeContactInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'work_phone' => '+375 (17) 123-4' . (50 + $i),
                    'education_level' => 'higher',
                    'skills' => 'PHP, Laravel, MySQL, Redis, Docker, Git',
                    'languages' => 'Русский (родной), Английский (B1/B2)',
                    'notes' => '',
                ]
            );

            EmployeeDocument::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'passport_number' => 'MP' . (500 + $i) . rand(1000, 9999),
                    'passport_issued_by' => 'УВД ' . rand(1, 4) . '-го района г. Минска',
                    'passport_issued_date' => Carbon::create(2015 + $i, 1, 1),
                    'passport_expiry_date' => Carbon::create(2035 + $i, 1, 1),
                    'tax_id' => (100 + $i) . rand(100000, 999999),
                ]
            );

            EmployeeEmergencyContact::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'name' => $data[0] . ' ' . ($data[9] == 'male' ? 'Сергеевич' : 'Ивановна'),
                    'phone' => '+375 (29) 5' . (10 + $i) . '-' . rand(1000, 9999),
                    'relation' => 'Родитель',
                ]
            );
        }

        // ============================================
        // FRONTEND-РАЗРАБОТЧИКИ (6 человек)
        // ============================================

        $frontendNames = [
            ['Новиков', 'Игорь', 'Петрович', 'frontend1@mail.by', '+375 (29) 611-11-11', 'Senior Frontend Developer', 4500, '2019-03-01', 'male'],
            ['Григорьев', 'Александр', 'Юрьевич', 'frontend2@mail.by', '+375 (29) 622-22-22', 'Frontend Developer', 3200, '2020-05-15', 'male'],
            ['Кузьмин', 'Максим', 'Васильевич', 'frontend3@mail.by', '+375 (29) 633-33-33', 'Frontend Developer', 3400, '2020-08-20', 'male'],
            ['Андреев', 'Сергей', 'Николаевич', 'frontend4@mail.by', '+375 (29) 644-44-44', 'Junior Frontend Developer', 2200, '2022-01-10', 'male'],
            ['Павлов', 'Евгений', 'Алексеевич', 'frontend5@mail.by', '+375 (29) 655-55-55', 'Frontend Developer', 3300, '2021-03-15', 'male'],
            ['Орлов', 'Владимир', 'Дмитриевич', 'frontend6@mail.by', '+375 (44) 611-11-11', 'Senior Frontend Developer', 4800, '2018-09-01', 'male'],
        ];

        foreach ($frontendNames as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data[3]],
                [
                    'name' => $data[0] . ' ' . $data[1] . ' ' . $data[2],
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'phone' => $data[4],
                    'birth_date' => Carbon::create(1988 + $i, rand(1, 12), rand(1, 28)),
                    'gender' => 'male',
                    'address' => 'г. Минск, ул. Цветочная, ' . (5 + $i) . ', кв. ' . (10 + $i),
                ]
            );

            $employee = Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id' => $frontendDept,
                    'position' => $data[5],
                    'position_code' => ($i < 2 ? 'SEN-FE-00' : ($i < 4 ? 'MID-FE-00' : 'JUN-FE-00')) . ($i + 1),
                    'salary' => $data[6],
                    'hire_date' => $data[7],
                    'fire_date' => null,
                    'probation_end_date' => Carbon::parse($data[7])->addMonths(3),
                    'employment_type' => 'full',
                    'work_type' => 'hybrid',
                    'work_schedule' => '5/2, 09:00-18:00',
                    'working_hours_per_week' => 40,
                    'is_active' => true,
                ]
            );

            EmployeePersonalInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'last_name' => $data[0],
                    'first_name' => $data[1],
                    'middle_name' => $data[2],
                    'birth_place' => 'г. Минск',
                    'nationality' => 'Беларусь',
                    'marital_status' => 'married',
                    'children_count' => rand(0, 2),
                ]
            );

            EmployeeContactInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'work_phone' => '+375 (17) 123-8' . (10 + $i),
                    'education_level' => 'higher',
                    'skills' => 'Vue.js, React, JavaScript, TypeScript, Tailwind CSS',
                    'languages' => 'Русский (родной), Английский (B1)',
                    'notes' => '',
                ]
            );

            EmployeeDocument::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'passport_number' => 'MP' . (600 + $i) . rand(1000, 9999),
                    'passport_issued_by' => 'УВД ' . rand(1, 4) . '-го района г. Минска',
                    'passport_issued_date' => Carbon::create(2014 + $i, 6, 1),
                    'passport_expiry_date' => Carbon::create(2034 + $i, 6, 1),
                    'tax_id' => (200 + $i) . rand(100000, 999999),
                ]
            );

            EmployeeEmergencyContact::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'name' => $data[0] . 'а ' . $data[1],
                    'phone' => '+375 (29) 6' . (10 + $i) . '-' . rand(1000, 9999),
                    'relation' => 'Супруга',
                ]
            );
        }

        // ============================================
        // QA ИНЖЕНЕРЫ (4 человека)
        // ============================================

        $qaNames = [
            ['Соловьева', 'Анастасия', 'Андреевна', 'qa1@mail.by', '+375 (29) 711-11-11', 'Lead QA Engineer', 3800, '2019-04-01', 'female'],
            ['Васильева', 'Екатерина', 'Павловна', 'qa2@mail.by', '+375 (29) 722-22-22', 'QA Engineer', 2800, '2020-06-15', 'female'],
            ['Зайцева', 'Ольга', 'Сергеевна', 'qa3@mail.by', '+375 (29) 733-33-33', 'QA Engineer', 2600, '2021-01-20', 'female'],
            ['Тимофеева', 'Ирина', 'Дмитриевна', 'qa4@mail.by', '+375 (44) 711-11-11', 'Junior QA Engineer', 1800, '2022-05-10', 'female'],
        ];

        foreach ($qaNames as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data[3]],
                [
                    'name' => $data[0] . ' ' . $data[1] . ' ' . $data[2],
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'phone' => $data[4],
                    'birth_date' => Carbon::create(1990 + $i, rand(1, 12), rand(1, 28)),
                    'gender' => 'female',
                    'address' => 'г. Минск, ул. Московская, ' . (15 + $i) . ', кв. ' . (30 + $i),
                ]
            );

            $employee = Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id' => $qaDept,
                    'position' => $data[5],
                    'position_code' => ($i == 0 ? 'LEAD-QA-001' : ($i < 3 ? 'MID-QA-00' : 'JUN-QA-00')) . ($i + 1),
                    'salary' => $data[6],
                    'hire_date' => $data[7],
                    'fire_date' => null,
                    'probation_end_date' => Carbon::parse($data[7])->addMonths(3),
                    'employment_type' => 'full',
                    'work_type' => 'office',
                    'work_schedule' => '5/2, 09:00-18:00',
                    'working_hours_per_week' => 40,
                    'is_active' => true,
                ]
            );

            EmployeePersonalInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'last_name' => $data[0],
                    'first_name' => $data[1],
                    'middle_name' => $data[2],
                    'birth_place' => 'г. Минск',
                    'nationality' => 'Беларусь',
                    'marital_status' => 'married',
                    'children_count' => rand(0, 1),
                ]
            );

            EmployeeContactInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'work_phone' => '+375 (17) 123-7' . (10 + $i),
                    'education_level' => 'higher',
                    'skills' => 'Ручное тестирование, автоматизация (Selenium), Postman, Jira',
                    'languages' => 'Русский (родной), Английский (B1)',
                    'notes' => '',
                ]
            );

            EmployeeDocument::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'passport_number' => 'MP' . (700 + $i) . rand(1000, 9999),
                    'passport_issued_by' => 'УВД ' . rand(1, 4) . '-го района г. Минска',
                    'passport_issued_date' => Carbon::create(2016 + $i, 3, 1),
                    'passport_expiry_date' => Carbon::create(2036 + $i, 3, 1),
                    'tax_id' => (300 + $i) . rand(100000, 999999),
                ]
            );

            EmployeeEmergencyContact::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'name' => $data[0] . ' ' . $data[1],
                    'phone' => '+375 (29) 7' . (10 + $i) . '-' . rand(1000, 9999),
                    'relation' => 'Мать',
                ]
            );
        }

        // ============================================
        // DEVOPS ИНЖЕНЕРЫ (3 человека)
        // ============================================

        $devopsNames = [
            ['Медведев', 'Константин', 'Александрович', 'devops1@mail.by', '+375 (29) 811-11-11', 'DevOps Engineer', 4200, '2019-07-01', 'male'],
            ['Степанов', 'Вячеслав', 'Олегович', 'devops2@mail.by', '+375 (29) 822-22-22', 'DevOps Engineer', 3600, '2020-09-15', 'male'],
            ['Никитин', 'Григорий', 'Викторович', 'devops3@mail.by', '+375 (44) 811-11-11', 'Junior DevOps', 2500, '2021-11-20', 'male'],
        ];

        foreach ($devopsNames as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data[3]],
                [
                    'name' => $data[0] . ' ' . $data[1] . ' ' . $data[2],
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'phone' => $data[4],
                    'birth_date' => Carbon::create(1987 + $i, rand(1, 12), rand(1, 28)),
                    'gender' => 'male',
                    'address' => 'г. Минск, ул. Белорусская, ' . (25 + $i) . ', кв. ' . (40 + $i),
                ]
            );

            $employee = Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id' => $devopsDept,
                    'position' => $data[5],
                    'position_code' => ($i < 2 ? 'SEN-DEV-00' : 'JUN-DEV-00') . ($i + 1),
                    'salary' => $data[6],
                    'hire_date' => $data[7],
                    'fire_date' => null,
                    'probation_end_date' => Carbon::parse($data[7])->addMonths(3),
                    'employment_type' => 'full',
                    'work_type' => 'remote',
                    'work_schedule' => '5/2, 09:00-18:00',
                    'working_hours_per_week' => 40,
                    'is_active' => true,
                ]
            );

            EmployeePersonalInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'last_name' => $data[0],
                    'first_name' => $data[1],
                    'middle_name' => $data[2],
                    'birth_place' => 'г. Минск',
                    'nationality' => 'Беларусь',
                    'marital_status' => 'married',
                    'children_count' => rand(0, 2),
                ]
            );

            EmployeeContactInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'work_phone' => '+375 (17) 123-9' . (10 + $i),
                    'education_level' => 'higher',
                    'skills' => 'Docker, Kubernetes, AWS, GitLab CI, Terraform, Ansible',
                    'languages' => 'Русский (родной), Английский (B2)',
                    'notes' => '',
                ]
            );

            EmployeeDocument::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'passport_number' => 'MP' . (800 + $i) . rand(1000, 9999),
                    'passport_issued_by' => 'УВД ' . rand(1, 4) . '-го района г. Минска',
                    'passport_issued_date' => Carbon::create(2017 + $i, 8, 1),
                    'passport_expiry_date' => Carbon::create(2037 + $i, 8, 1),
                    'tax_id' => (400 + $i) . rand(100000, 999999),
                ]
            );

            EmployeeEmergencyContact::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'name' => $data[0] . 'а ' . $data[1],
                    'phone' => '+375 (29) 8' . (10 + $i) . '-' . rand(1000, 9999),
                    'relation' => 'Супруга',
                ]
            );
        }

        // ============================================
        // ТЕХНИЧЕСКАЯ ПОДДЕРЖКА (5 человек)
        // ============================================

        $supportNames = [
            ['Борисов', 'Денис', 'Андреевич', 'support1@mail.by', '+375 (29) 911-11-11', 'Support Team Lead', 2800, '2019-10-01', 'male'],
            ['Карпов', 'Роман', 'Иванович', 'support2@mail.by', '+375 (29) 922-22-22', 'Support Engineer', 1800, '2020-12-15', 'male'],
            ['Макаров', 'Виталий', 'Петрович', 'support3@mail.by', '+375 (29) 933-33-33', 'Support Engineer', 1700, '2021-07-01', 'male'],
            ['Фролова', 'Ирина', 'Алексеевна', 'support4@mail.by', '+375 (44) 911-11-11', 'Support Engineer', 1900, '2021-09-20', 'female'],
            ['Давыдова', 'Елена', 'Сергеевна', 'support5@mail.by', '+375 (44) 922-22-22', 'Junior Support', 1400, '2022-03-10', 'female'],
        ];

        foreach ($supportNames as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data[3]],
                [
                    'name' => $data[0] . ' ' . $data[1] . ' ' . $data[2],
                    'password' => Hash::make('password'),
                    'role' => 'employee',
                    'phone' => $data[4],
                    'birth_date' => Carbon::create(1992 + $i, rand(1, 12), rand(1, 28)),
                    'gender' => $i < 3 ? 'male' : 'female',
                    'address' => 'г. Минск, ул. Кальварийская, ' . (35 + $i) . ', кв. ' . (50 + $i),
                ]
            );

            $employee = Employee::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'department_id' => $supportDept,
                    'position' => $data[5],
                    'position_code' => ($i == 0 ? 'LEAD-SUP-001' : ($i < 4 ? 'MID-SUP-00' : 'JUN-SUP-001')) . ($i + 1),
                    'salary' => $data[6],
                    'hire_date' => $data[7],
                    'fire_date' => null,
                    'probation_end_date' => Carbon::parse($data[7])->addMonths(3),
                    'employment_type' => 'full',
                    'work_type' => 'hybrid',
                    'work_schedule' => $i < 3 ? '5/2, 09:00-18:00' : '5/2, 12:00-21:00',
                    'working_hours_per_week' => 40,
                    'is_active' => true,
                ]
            );

            EmployeePersonalInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'last_name' => $data[0],
                    'first_name' => $data[1],
                    'middle_name' => $data[2],
                    'birth_place' => 'г. Минск',
                    'nationality' => 'Беларусь',
                    'marital_status' => 'single',
                    'children_count' => 0,
                ]
            );

            EmployeeContactInfo::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'work_phone' => '+375 (17) 123-6' . (10 + $i),
                    'education_level' => $i < 2 ? 'higher' : 'secondary_special',
                    'skills' => 'Работа с клиентами, решение проблем, знание продукта, Jira',
                    'languages' => 'Русский (родной), Английский (A2/B1)',
                    'notes' => '',
                ]
            );

            EmployeeDocument::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'passport_number' => 'MP' . (900 + $i) . rand(1000, 9999),
                    'passport_issued_by' => 'УВД ' . rand(1, 4) . '-го района г. Минска',
                    'passport_issued_date' => Carbon::create(2018 + $i, 10, 1),
                    'passport_expiry_date' => Carbon::create(2038 + $i, 10, 1),
                    'tax_id' => (500 + $i) . rand(100000, 999999),
                ]
            );

            EmployeeEmergencyContact::firstOrCreate(
                ['employee_id' => $employee->id],
                [
                    'name' => $data[0] . ' ' . ($i < 3 ? 'Александровна' : 'Петрович'),
                    'phone' => '+375 (29) 9' . (10 + $i) . '-' . rand(1000, 9999),
                    'relation' => $i < 3 ? 'Мать' : 'Отец',
                ]
            );
        }
    }
}
