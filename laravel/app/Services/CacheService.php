<?php
// app/Services/CacheService.php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Массив ключей кеша для очистки
     */
    private static $cacheKeys = [
        // Dashboard
        'dashboard_stats',
        'users_list',
        'departments_list',
        'employees_list',

        // Departments
        'departments_tree',
        'departments_stats',
        'departments_create_data',
        'departments_for_filter',

        // Employees
        'employees_stats',
        'department_for_filter',

        // Salary
        'salary_stats',
        'tax_rates_active',

        // Reports
        'reports_stats',

        // Tax
        'tax_stats',

        // Settings
        'company_settings',
    ];

    /**
     * Паттерны для очистки
     */
    private static $patterns = [
        'employees_index_*',      // Все списки сотрудников с фильтрами
        'users_paginated_*',       // Все пагинированные списки пользователей
    ];

    /**
     * Очистить конкретный ключ
     */
    public static function forget(string $key): void
    {
        if (Cache::has($key)) {
            Cache::forget($key);
            \Log::info("Cache cleared: {$key}");
        }
    }

    /**
     * Очистить несколько ключей
     */
    public static function forgetMany(array $keys): void
    {
        foreach ($keys as $key) {
            self::forget($key);
        }
    }

    /**
     * Очистить все основные ключи
     */
    public static function flushAll(): void
    {
        foreach (self::$cacheKeys as $key) {
            self::forget($key);
        }

        foreach (self::$patterns as $pattern) {
            self::forgetPattern($pattern);
        }

        \Log::info('All application cache cleared');
    }

    /**
     * Очистить кеш по паттерну
     */
    public static function forgetPattern(string $pattern): void
    {
        $redis = Cache::store('redis')->getRedis();
        $keys = $redis->keys($pattern);

        foreach ($keys as $key) {
            $redis->del($key);
            \Log::info("Cache cleared by pattern: {$key}");
        }
    }

    /**
     * Очистить кеш связанный с подразделениями
     */
    public static function flushDepartments(): void
    {
        self::forgetMany([
            'departments_tree',
            'departments_stats',
            'departments_create_data',
            'departments_list',
            'department_for_filter',
        ]);
        self::forgetPattern('employees_index_*');
    }

    /**
     * Очистить кеш связанный с сотрудниками
     */
    public static function flushEmployees(): void
    {
        self::forgetMany([
            'employees_stats',
            'employees_list',
            'departments_stats',
            'dashboard_stats',
            'salary_stats',
        ]);
        self::forgetPattern('employees_index_*');
    }

    /**
     * Очистить кеш связанный с зарплатой
     */
    public static function flushSalary(): void
    {
        self::forgetMany([
            'salary_stats',
            'tax_rates_active',
            'dashboard_stats',
        ]);
    }

    /**
     * Очистить кеш связанный с настройками
     */
    public static function flushSettings(): void
    {
        self::forgetMany([
            'company_settings',
            'tax_rates_active',
        ]);
    }

    /**
     * Получить значение из кеша или сохранить
     */
    public static function remember(string $key, $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    /**
     * Получить значение из кеша навсегда (пока не очистят)
     */
    public static function rememberForever(string $key, $callback)
    {
        if (Cache::has($key)) {
            return Cache::get($key);
        }

        $value = $callback();
        Cache::forever($key, $value);
        return $value;
    }
}
