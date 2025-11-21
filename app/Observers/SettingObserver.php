<?php

namespace App\Observers;

use App\Models\Setting;
use App\Traits\CachesApiResponses;

class SettingObserver
{
    use CachesApiResponses;

    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Setting "deleted" event.
     */
    public function deleted(Setting $setting): void
    {
        $this->clearCache();
    }

    /**
     * Clear relevant caches when setting changes
     */
    protected function clearCache(): void
    {
        // Increment cache version for settings
        self::incrementCacheVersion('settings');
    }
}
