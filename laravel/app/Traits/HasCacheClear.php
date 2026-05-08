<?php
// app/Traits/HasCacheClear.php

namespace App\Traits;

use App\Services\CacheService;

trait HasCacheClear
{
    protected static function bootHasCacheClear()
    {
        static::saved(function ($model) {
            $model->clearRelatedCache();
        });

        static::deleted(function ($model) {
            $model->clearRelatedCache();
        });
    }

    protected function clearRelatedCache(): void
    {
        // По умолчанию очищаем всё
        CacheService::flushAll();
    }
}
