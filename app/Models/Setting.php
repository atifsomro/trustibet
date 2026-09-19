<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * A single dynamic configuration row (e.g. site_facebook => https://...).
 *
 * All rows are loaded once per request by SettingsServiceProvider and merged
 * into config('settings'), so anywhere in the app you can simply call:
 *
 *     config('settings.site_facebook')
 *     config('settings.site_facebook', 'https://facebook.com') // with default
 *
 * New fields do not require a migration — add them from the admin
 * Settings page (Admin > Settings) and they become available immediately.
 */
class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'label',
        'value',
        'type',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('group')->orderBy('sort_order')->orderBy('id');
    }

    /**
     * All settings as a flat "key => value" array, ready to be merged into
     * the config repository. Used by SettingsServiceProvider on boot.
     *
     * @return array<string, mixed>
     */
    public static function allAsArray(): array
    {
        return static::query()->pluck('value', 'key')->all();
    }

    /**
     * Convenience accessor for reading a single setting directly from the
     * database (bypasses config, always fresh). Prefer config('settings.*')
     * for normal use; this is here for callers that need a guaranteed
     * up-to-date value within the same request that just wrote it.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    /**
     * Field types available on the admin form. Presentational only — every
     * value is still stored as text in the `value` column.
     *
     * @return array<string, string>
     */
    public static function availableTypes(): array
    {
        return [
            'text' => 'Text',
            'textarea' => 'Textarea',
            'url' => 'URL',
            'email' => 'Email',
            'image' => 'Image URL',
            'boolean' => 'Yes / No',
        ];
    }
}
