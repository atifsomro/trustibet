<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::query()->ordered()->get()->groupBy('group');

        return view('admin.settings.index', [
            'settingGroups' => $settings,
            'fieldTypes' => Setting::availableTypes(),
            'active' => 'settings',
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'settings' => ['nullable', 'array'],
            'settings.*.label' => ['nullable', 'string', 'max:255'],
            'settings.*.value' => ['nullable', 'string'],
            'settings.*.type' => ['nullable', 'string', 'max:50'],

            'new' => ['nullable', 'array'],
            'new.*.key' => ['nullable', 'string', 'max:255'],
            'new.*.label' => ['nullable', 'string', 'max:255'],
            'new.*.group' => ['nullable', 'string', 'max:255'],
            'new.*.type' => ['nullable', 'string', 'max:50'],
            'new.*.value' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated['settings'] ?? [] as $id => $row) {
                Setting::whereKey($id)->update([
                    'label' => $row['label'] ?? null,
                    'value' => $row['value'] ?? null,
                    'type' => $row['type'] ?? 'text',
                ]);
            }

            foreach ($validated['new'] ?? [] as $row) {
                $key = Str::slug(trim((string) ($row['key'] ?? '')), '_');

                if ($key === '') {
                    continue;
                }

                Setting::updateOrCreate(
                    ['key' => $key],
                    [
                        'group' => trim((string) ($row['group'] ?? '')) ?: 'general',
                        'label' => trim((string) ($row['label'] ?? '')) ?: Str::headline($key),
                        'type' => $row['type'] ?? 'text',
                        'value' => $row['value'] ?? null,
                    ]
                );
            }

            DB::commit();

            return back()->with('success', 'Settings updated successfully.');
        } catch (Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->withInput()->with('error', 'Unable to update settings.');
        }
    }

    public function destroy(Setting $setting): RedirectResponse
    {
        try {
            $setting->delete();

            return back()->with('success', 'Field removed.');
        } catch (Throwable $e) {
            report($e);

            return back()->with('error', 'Unable to remove this field.');
        }
    }
}
