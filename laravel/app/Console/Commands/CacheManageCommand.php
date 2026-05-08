<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CacheService;

class CacheManageCommand extends Command
{
    /**
     * Имя и описание команд.
     *
     * @var string
     */
    protected $signature = 'cache:manage {action : clear-all|clear-departments|clear-employees|clear-salary|clear-settings}';

    /**
     * @var string
     */
    protected $description = 'Управление кешем приложения';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        match ($action) {
            'clear-all' => CacheService::flushAll(),
            'clear-departments' => CacheService::flushDepartments(),
            'clear-employees' => CacheService::flushEmployees(),
            'clear-salary' => CacheService::flushSalary(),
            'clear-settings' => CacheService::flushSettings(),
            default => $this->error("Неизвестное действие: {$action}")
        };

        $this->info("Кеш очищен: {$action}");
    }
}
