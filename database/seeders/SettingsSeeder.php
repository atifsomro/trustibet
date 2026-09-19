<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Optional starter defaults for the Settings page. Not called from
 * DatabaseSeeder automatically — run it on demand:
 *
 *     php artisan db:seed --class=Database\\Seeders\\SettingsSeeder
 */
class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['group' => 'general', 'key' => 'site_name', 'label' => 'Site Name', 'type' => 'text', 'value' => 'Trustibet', 'sort_order' => 1],
            ['group' => 'general', 'key' => 'support_email', 'label' => 'Support Email', 'type' => 'email', 'value' => null, 'sort_order' => 2],
            ['group' => 'general', 'key' => 'support_phone', 'label' => 'Support Phone', 'type' => 'text', 'value' => null, 'sort_order' => 3],
            ['group' => 'social', 'key' => 'site_facebook', 'label' => 'Facebook URL', 'type' => 'url', 'value' => null, 'sort_order' => 1],
            ['group' => 'social', 'key' => 'site_twitter', 'label' => 'Twitter / X URL', 'type' => 'url', 'value' => null, 'sort_order' => 2],
            ['group' => 'social', 'key' => 'site_instagram', 'label' => 'Instagram URL', 'type' => 'url', 'value' => null, 'sort_order' => 3],
            ['group' => 'social', 'key' => 'site_telegram', 'label' => 'Telegram URL', 'type' => 'url', 'value' => null, 'sort_order' => 4],
        ];

        foreach ($defaults as $row) {
            Setting::updateOrCreate(['key' => $row['key']], $row);
        }
    }
}
