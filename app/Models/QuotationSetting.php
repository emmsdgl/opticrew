<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, $default = null): ?string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Check if auto-send is enabled.
     */
    public static function isAutoSendEnabled(): bool
    {
        return static::getValue('auto_send_enabled', '1') === '1';
    }

    /**
     * Get the PDF file path for a given service key.
     */
    public static function getPdfPath(string $serviceKey): ?string
    {
        return static::getValue('pdf_' . $serviceKey);
    }
}
