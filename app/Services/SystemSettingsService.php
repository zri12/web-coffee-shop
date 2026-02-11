<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SystemSettingsService
{
    private const CACHE_KEY = 'system_settings';

    /**
     * Default values when settings are missing.
     */
    private array $defaults = [
        'cafe_name' => 'Bean & Brew',
        'address' => 'Jl. Kopi Nusantara No. 123, Jakarta',
        'phone' => '+62 812-3456-7890',
        'logo_path' => '',
        'opening_time' => '08:00',
        'closing_time' => '22:00',
        'closed_days' => [],
        'instagram' => '',
        'facebook' => '',
        'whatsapp' => '',
    ];

    /**
     * Get all system settings with defaults applied.
     */
    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, now()->addMinutes(30), function () {
            try {
                if (!Schema::hasTable('settings')) {
                    return $this->defaults;
                }

                $dbSettings = Setting::query()->pluck('value', 'key')->toArray();
                return $this->applyDefaults($dbSettings);
            } catch (\Throwable $e) {
                Log::warning('System settings fallback to defaults', ['error' => $e->getMessage()]);
                return $this->defaults;
            }
        });
    }

    /**
     * Get single setting value.
     */
    public function get(string $key, $default = null)
    {
        $settings = $this->all();
        return $settings[$key] ?? ($default ?? $this->defaults[$key] ?? null);
    }

    /**
     * Persist settings and refresh cache.
     */
    public function update(array $payload): array
    {
        if (!Schema::hasTable('settings')) {
            throw new \RuntimeException('Tabel settings belum tersedia. Jalankan php artisan migrate.');
        }

        foreach ($payload as $key => $value) {
            // Normalize arrays to simple arrays (cast to JSON via model)
            if (is_array($value)) {
                $value = array_values($value);
            }

            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->flushCache();
        return $this->all();
    }

    public function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Store uploaded logo and return public path.
     */
    public function storeLogo($uploadedFile): string
    {
        $filename = 'cafe/' . time() . '_' . $uploadedFile->getClientOriginalName();
        $uploadedFile->storeAs('public', $filename);
        return '/storage/' . $filename;
    }

    private function applyDefaults(array $settings): array
    {
        return array_merge($this->defaults, $settings);
    }
}
