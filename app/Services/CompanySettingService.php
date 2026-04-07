<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CompanySettingService
{
    /**
     * Get a company setting value by key.
     * Static reference data → 24 hour TTL (set() invalidates on changes).
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("company_setting_{$key}", now()->addHours(24), function () use ($key, $default) {
            $setting = DB::table('company_settings')->where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'integer' => (int) $setting->value,
                'decimal' => (float) $setting->value,
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }

    /**
     * Set a company setting value.
     */
    public static function set(string $key, $value, string $type = 'string', string $description = ''): void
    {
        DB::table('company_settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
                'description' => $description ?: null,
                'updated_at' => now(),
            ]
        );

        Cache::forget("company_setting_{$key}");
    }
}
