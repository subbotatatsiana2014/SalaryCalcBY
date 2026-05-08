<?php

namespace Database\Seeders;

use App\Models\Department\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. РУКОВОДСТВО
        $ceoDepartment = Department::create([
            'name' => 'Руководство компании',
            'code' => 'CEO',
            'description' => 'Высшее руководство компании, стратегическое управление',
            'color' => '#1E3A8A',
            'manager_id' => null,
            'parent_id' => null,
            'is_active' => true,
        ]);

        $ceoDepartment->financials()->create([
            'budget' => 1000000.00,
            'salary_fund' => 250000.00,
            'max_employees' => 5,
        ]);

        $ceoDepartment->settings()->create([
            'short_name' => 'Руководство',
            'type' => 'management',
            'icon' => 'fas fa-crown',
            'additional_info' => json_encode([
                'responsibilities' => 'Стратегическое планирование, управление компанией',
                'meeting_schedule' => 'Понедельник 10:00',
                'report_level' => 'board'
            ])
        ]);

        // 2. ТЕХНИЧЕСКИЙ ДЕПАРТАМЕНТ
        $techDepartment = Department::create([
            'name' => 'Технический департамент',
            'code' => 'TECH',
            'description' => 'Разработка и поддержка IT-продуктов компании',
            'color' => '#3B82F6',
            'manager_id' => null,
            'parent_id' => $ceoDepartment->id,
            'is_active' => true,
        ]);

        $techDepartment->financials()->create([
            'budget' => 500000.00,
            'salary_fund' => 300000.00,
            'max_employees' => 25,
        ]);

        $techDepartment->settings()->create([
            'short_name' => 'Техдеп',
            'type' => 'development',
            'icon' => 'fas fa-code',
            'additional_info' => json_encode([
                'tech_stack' => 'PHP, Laravel, Vue.js, MySQL, Docker',
                'slack_channel' => '#tech-department',
                'github_org' => 'company-dev'
            ])
        ]);

        // 3. Backend-разработка
        $backendDev = Department::create([
            'name' => 'Backend-разработка',
            'code' => 'BACKEND',
            'description' => 'Разработка серверной части приложений, API, базы данных',
            'color' => '#2563EB',
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        $backendDev->financials()->create([
            'budget' => 200000.00,
            'salary_fund' => 140000.00,
            'max_employees' => 10,
        ]);

        $backendDev->settings()->create([
            'short_name' => 'Бэкенд',
            'type' => 'development',
            'icon' => 'fas fa-server',
            'additional_info' => json_encode([
                'tech_stack' => 'PHP 8.2, Laravel 11, MySQL, Redis, Docker',
                'deploy_tools' => 'GitLab CI/CD, Jenkins',
                'code_standards' => 'PSR-12'
            ])
        ]);

        $this->command->info('Структура подразделений создана успешно!');
        $this->command->info('Всего создано: ' . Department::count() . ' подразделений');
    }
}
