<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

/**
 * Loads all dynamic Setting rows into config('settings') on every boot,
 * so the rest of the app can simply call config('settings.some_key').
 *
 * New fields added from the admin Settings page require no code change
 * here — they appear in config('settings') automatically on the next
 * request because we read the whole table, not a fixed list of keys.
 */
class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        try {
            // Guard against the table not existing yet (fresh clone,
            // `composer install` before `artisan migrate`, etc.) so this
            // provider never breaks artisan commands like migrate itself.
            if (Schema::hasTable('settings')) {
                Config::set('settings', Setting::allAsArray());
            }
        } catch (Throwable $e) {
            // No DB connection yet (e.g. during initial setup/build steps).
            // Fail silently — config('settings') simply stays empty.
        }
    }
}
