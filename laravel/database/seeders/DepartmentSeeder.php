<?php

namespace Database\Seeders;

use App\Models\Department\Department;
use App\Models\Department\DepartmentContact;
use App\Models\Department\DepartmentFinancial;
use App\Models\Department\DepartmentSetting;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // 1. РУКОВОДСТВО КОМПАНИИ
        // ============================================

        $ceoDepartment = Department::create([
            'name' => 'Руководство компании',
            'code' => 'CEO',
            'description' => 'Высшее руководство компании, стратегическое управление, определение целей и задач компании',
            'color' => '#1E3A8A',
            'manager_id' => null,
            'parent_id' => null,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $ceoDepartment->id,
            'budget' => 1200000.00,
            'salary_fund' => 300000.00,
            'max_employees' => 8,
        ]);

        DepartmentSetting::create([
            'department_id' => $ceoDepartment->id,
            'short_name' => 'Руководство',
            'type' => 'management',
            'icon' => 'fas fa-crown',
            'additional_info' => json_encode([
                'responsibilities' => ['Стратегическое планирование', 'Управление компанией', 'Принятие ключевых решений'],
                'meeting_schedule' => 'Понедельник 10:00',
                'slack_channel' => '#ceo-office'
            ])
        ]);

        DepartmentContact::create([
            'department_id' => $ceoDepartment->id,
            'phone' => '+375 (17) 123-00-01',
            'email' => 'ceo@salarycalc.by',
            'address' => 'г. Минск, ул. Компьютерная, 15, 5-й этаж',
            'location' => 'Бизнес-центр "IT Park"',
        ]);

        // ============================================
        // 2. АДМИНИСТРАТИВНЫЙ ДЕПАРТАМЕНТ
        // ============================================

        $adminDepartment = Department::create([
            'name' => 'Административный департамент',
            'code' => 'ADMIN',
            'description' => 'Общее администрирование, документооборот, юридическая поддержка, корпоративная культура',
            'color' => '#4F46E5',
            'manager_id' => null,
            'parent_id' => null,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $adminDepartment->id,
            'budget' => 300000.00,
            'salary_fund' => 120000.00,
            'max_employees' => 10,
        ]);

        DepartmentSetting::create([
            'department_id' => $adminDepartment->id,
            'short_name' => 'Администрация',
            'type' => 'administration',
            'icon' => 'fas fa-building',
            'additional_info' => json_encode([
                'functions' => ['Документооборот', 'Юридическое сопровождение', 'Корпоративные мероприятия'],
                'office_hours' => '09:00 - 18:00'
            ])
        ]);

        DepartmentContact::create([
            'department_id' => $adminDepartment->id,
            'phone' => '+375 (17) 123-00-02',
            'email' => 'admin@salarycalc.by',
            'address' => 'г. Минск, ул. Компьютерная, 15, 2-й этаж',
            'location' => 'Бизнес-центр "IT Park"',
        ]);

        // ============================================
        // 3. HR-ДЕПАРТАМЕНТ
        // ============================================

        $hrDepartment = Department::create([
            'name' => 'HR-департамент',
            'code' => 'HR',
            'description' => 'Подбор персонала, адаптация сотрудников, обучение и развитие, кадровое делопроизводство',
            'color' => '#EC4899',
            'manager_id' => null,
            'parent_id' => $adminDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $hrDepartment->id,
            'budget' => 200000.00,
            'salary_fund' => 80000.00,
            'max_employees' => 8,
        ]);

        DepartmentSetting::create([
            'department_id' => $hrDepartment->id,
            'short_name' => 'HR',
            'type' => 'hr',
            'icon' => 'fas fa-users',
            'additional_info' => json_encode([
                'functions' => ['Рекрутинг', 'Адаптация', 'Обучение', 'Оценка персонала'],
                'recruitment_channels' => ['LinkedIn', 'HH.ru', 'Telegram'],
                'onboarding_duration' => '2 недели'
            ])
        ]);

        DepartmentContact::create([
            'department_id' => $hrDepartment->id,
            'phone' => '+375 (17) 123-00-04',
            'email' => 'hr@salarycalc.by',
            'address' => 'г. Минск, ул. Компьютерная, 15, 2-й этаж',
            'location' => 'Бизнес-центр "IT Park"',
        ]);

        // ============================================
        // 4. ФИНАНСОВЫЙ ДЕПАРТАМЕНТ
        // ============================================

        $financeDepartment = Department::create([
            'name' => 'Финансовый департамент',
            'code' => 'FIN',
            'description' => 'Финансовое планирование, бухгалтерский учет, налоговый учет, расчет зарплаты, финансовый анализ',
            'color' => '#F59E0B',
            'manager_id' => null,
            'parent_id' => $adminDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $financeDepartment->id,
            'budget' => 250000.00,
            'salary_fund' => 100000.00,
            'max_employees' => 7,
        ]);

        DepartmentSetting::create([
            'department_id' => $financeDepartment->id,
            'short_name' => 'Финансы',
            'type' => 'finance',
            'icon' => 'fas fa-chart-line',
            'additional_info' => json_encode([
                'functions' => ['Бухгалтерский учет', 'Налоговый учет', 'Расчет зарплаты', 'Финансовый анализ'],
                'accounting_system' => '1С:Предприятие',
                'tax_system' => 'ОСН'
            ])
        ]);

        DepartmentContact::create([
            'department_id' => $financeDepartment->id,
            'phone' => '+375 (17) 123-00-05',
            'email' => 'finance@salarycalc.by',
            'address' => 'г. Минск, ул. Компьютерная, 15, 2-й этаж',
            'location' => 'Бизнес-центр "IT Park"',
        ]);

        // ============================================
        // 5. ТЕХНИЧЕСКИЙ ДЕПАРТАМЕНТ
        // ============================================

        $techDepartment = Department::create([
            'name' => 'Технический департамент',
            'code' => 'TECH',
            'description' => 'Разработка и поддержка IT-продуктов компании, техническое архитектурирование',
            'color' => '#3B82F6',
            'manager_id' => null,
            'parent_id' => $ceoDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $techDepartment->id,
            'budget' => 800000.00,
            'salary_fund' => 500000.00,
            'max_employees' => 50,
        ]);

        DepartmentSetting::create([
            'department_id' => $techDepartment->id,
            'short_name' => 'Техдеп',
            'type' => 'development',
            'icon' => 'fas fa-laptop-code',
            'additional_info' => json_encode([
                'tech_stack' => ['PHP 8.2/Laravel', 'Vue.js/React', 'MySQL/PostgreSQL', 'Docker/K8s'],
                'development_methodology' => 'Scrum/Agile',
                'sprint_duration' => '2 недели'
            ])
        ]);

        DepartmentContact::create([
            'department_id' => $techDepartment->id,
            'phone' => '+375 (17) 123-00-03',
            'email' => 'cto@salarycalc.by',
            'address' => 'г. Минск, ул. Компьютерная, 15, 4-й этаж',
            'location' => 'Бизнес-центр "IT Park"',
        ]);

        // ============================================
        // 6. BACKEND-РАЗРАБОТКА
        // ============================================

        $backendDepartment = Department::create([
            'name' => 'Backend-разработка',
            'code' => 'BACKEND',
            'description' => 'Разработка серверной части приложений, проектирование API, работа с базами данных',
            'color' => '#2563EB',
            'manager_id' => null,
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $backendDepartment->id,
            'budget' => 250000.00,
            'salary_fund' => 180000.00,
            'max_employees' => 12,
        ]);

        DepartmentSetting::create([
            'department_id' => $backendDepartment->id,
            'short_name' => 'Бэкенд',
            'type' => 'development',
            'icon' => 'fas fa-server',
            'additional_info' => json_encode([
                'tech_stack' => ['PHP 8.2/Laravel', 'Node.js', 'MySQL/PostgreSQL', 'Redis'],
                'api_standards' => 'REST, GraphQL',
                'testing' => 'PHPUnit, Pest'
            ])
        ]);

        // ============================================
        // 7. FRONTEND-РАЗРАБОТКА
        // ============================================

        $frontendDepartment = Department::create([
            'name' => 'Frontend-разработка',
            'code' => 'FRONTEND',
            'description' => 'Разработка пользовательских интерфейсов, создание компонентов, оптимизация производительности',
            'color' => '#10B981',
            'manager_id' => null,
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $frontendDepartment->id,
            'budget' => 200000.00,
            'salary_fund' => 150000.00,
            'max_employees' => 10,
        ]);

        DepartmentSetting::create([
            'department_id' => $frontendDepartment->id,
            'short_name' => 'Фронтенд',
            'type' => 'development',
            'icon' => 'fas fa-palette',
            'additional_info' => json_encode([
                'tech_stack' => ['Vue.js 3/Nuxt', 'React/Next.js', 'Tailwind CSS', 'Pinia/Redux'],
                'responsive_design' => 'Desktop + Mobile',
                'accessibility' => 'WCAG 2.1 AA'
            ])
        ]);

        // ============================================
        // 8. MOBILE-РАЗРАБОТКА
        // ============================================

        $mobileDepartment = Department::create([
            'name' => 'Mobile-разработка',
            'code' => 'MOBILE',
            'description' => 'Разработка мобильных приложений для iOS и Android',
            'color' => '#8B5CF6',
            'manager_id' => null,
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $mobileDepartment->id,
            'budget' => 180000.00,
            'salary_fund' => 130000.00,
            'max_employees' => 8,
        ]);

        DepartmentSetting::create([
            'department_id' => $mobileDepartment->id,
            'short_name' => 'Мобайл',
            'type' => 'development',
            'icon' => 'fas fa-mobile-alt',
            'additional_info' => json_encode([
                'tech_stack' => ['Flutter', 'React Native', 'Swift (iOS)', 'Kotlin (Android)'],
                'target_platforms' => ['iOS 15+', 'Android 11+']
            ])
        ]);

        // ============================================
        // 9. DEVOPS И ИНФРАСТРУКТУРА
        // ============================================

        $devopsDepartment = Department::create([
            'name' => 'DevOps и инфраструктура',
            'code' => 'DEVOPS',
            'description' => 'Управление серверной инфраструктурой, CI/CD пайплайны, мониторинг, безопасность',
            'color' => '#06B6D4',
            'manager_id' => null,
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $devopsDepartment->id,
            'budget' => 150000.00,
            'salary_fund' => 120000.00,
            'max_employees' => 6,
        ]);

        DepartmentSetting::create([
            'department_id' => $devopsDepartment->id,
            'short_name' => 'DevOps',
            'type' => 'operations',
            'icon' => 'fas fa-cloud',
            'additional_info' => json_encode([
                'infrastructure' => ['AWS (EC2, RDS, S3)', 'Docker/Kubernetes', 'GitLab CI/CD'],
                'monitoring' => 'Prometheus, Grafana, ELK Stack',
                'backup_policy' => 'Ежедневно'
            ])
        ]);

        // ============================================
        // 10. QA ТЕСТИРОВАНИЕ
        // ============================================

        $qaDepartment = Department::create([
            'name' => 'Quality Assurance (QA)',
            'code' => 'QA',
            'description' => 'Тестирование ПО, автоматизация тестов, контроль качества',
            'color' => '#EF4444',
            'manager_id' => null,
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $qaDepartment->id,
            'budget' => 120000.00,
            'salary_fund' => 90000.00,
            'max_employees' => 6,
        ]);

        DepartmentSetting::create([
            'department_id' => $qaDepartment->id,
            'short_name' => 'QA',
            'type' => 'qa',
            'icon' => 'fas fa-bug',
            'additional_info' => json_encode([
                'testing_types' => ['Manual Testing', 'Automated Testing', 'Performance Testing'],
                'automation_tools' => 'Selenium, Cypress, Postman',
                'bug_tracking' => 'Jira'
            ])
        ]);

        // ============================================
        // 11. ПРОДУКТ-МЕНЕДЖМЕНТ
        // ============================================

        $productDepartment = Department::create([
            'name' => 'Продукт-менеджмент',
            'code' => 'PM',
            'description' => 'Управление продуктом, анализ рынка, разработка требований, управление бэклогом',
            'color' => '#14B8A6',
            'manager_id' => null,
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $productDepartment->id,
            'budget' => 180000.00,
            'salary_fund' => 110000.00,
            'max_employees' => 6,
        ]);

        DepartmentSetting::create([
            'department_id' => $productDepartment->id,
            'short_name' => 'Продукты',
            'type' => 'management',
            'icon' => 'fas fa-rocket',
            'additional_info' => json_encode([
                'products' => ['SalaryCalc BY', 'HR Portal', 'Analytics Dashboard'],
                'methodology' => 'Scrum/Kanban',
                'tools' => 'Jira, Confluence, Figma'
            ])
        ]);

        // ============================================
        // 12. ТЕХНИЧЕСКАЯ ПОДДЕРЖКА
        // ============================================

        $supportDepartment = Department::create([
            'name' => 'Техническая поддержка',
            'code' => 'SUPPORT',
            'description' => 'Поддержка пользователей, обработка обращений, решение технических проблем',
            'color' => '#6B7280',
            'manager_id' => null,
            'parent_id' => $techDepartment->id,
            'is_active' => true,
        ]);

        DepartmentFinancial::create([
            'department_id' => $supportDepartment->id,
            'budget' => 100000.00,
            'salary_fund' => 80000.00,
            'max_employees' => 8,
        ]);

        DepartmentSetting::create([
            'department_id' => $supportDepartment->id,
            'short_name' => 'Поддержка',
            'type' => 'support',
            'icon' => 'fas fa-headset',
            'additional_info' => json_encode([
                'channels' => ['Email', 'Chat', 'Phone', 'Telegram'],
                'working_hours' => '09:00 - 21:00 (Пн-Пт), 10:00-18:00 (Сб)',
                'response_time' => '< 1 час'
            ])
        ]);
    }
}
