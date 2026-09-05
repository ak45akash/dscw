<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    private const CACHE_KEY = 'dscw.settings';

    private const CACHE_TTL = 3600;

    public function get(string $group, string $key, mixed $default = null): mixed
    {
        $settings = $this->all();

        return data_get($settings, "{$group}.{$key}", $default);
    }

    public function getGroup(string $group): array
    {
        return $this->all()->get($group, []);
    }

    public function publicSettings(): array
    {
        $cached = Cache::remember('dscw.settings.public', self::CACHE_TTL, fn () => $this->buildPublicArray());

        if (! is_array($cached)) {
            Cache::forget('dscw.settings.public');
            $cached = $this->buildPublicArray();
        }

        return $cached;
    }

    private function buildPublicArray(): array
    {
        return Setting::query()
            ->where('is_public', true)
            ->get()
            ->groupBy('group')
            ->map(fn (Collection $items) => $items->mapWithKeys(
                fn (Setting $setting) => [$setting->key => $this->castValue($setting)]
            )->all())
            ->all();
    }

    public function set(string $group, string $key, mixed $value, string $type = 'string', bool $isPublic = false): Setting
    {
        $setting = Setting::query()->updateOrCreate(
            ['group' => $group, 'key' => $key],
            [
                'value' => $this->encodeValue($value, $type),
                'type' => $type,
                'is_public' => $isPublic,
            ]
        );

        $this->clearCache();

        return $setting;
    }

    public function setMany(string $group, array $values, array $publicKeys = []): void
    {
        foreach ($values as $key => $payload) {
            if (is_array($payload)) {
                $this->set(
                    $group,
                    $key,
                    $payload['value'] ?? null,
                    $payload['type'] ?? 'string',
                    $payload['is_public'] ?? in_array($key, $publicKeys, true)
                );

                continue;
            }

            $this->set($group, $key, $payload, 'string', in_array($key, $publicKeys, true));
        }
    }

    public function all(): Collection
    {
        $cached = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, fn () => $this->buildAllArray());

        if (! is_array($cached)) {
            Cache::forget(self::CACHE_KEY);
            $cached = $this->buildAllArray();
        }

        return collect($cached);
    }

    private function buildAllArray(): array
    {
        return Setting::query()
            ->orderBy('group')
            ->orderBy('key')
            ->get()
            ->groupBy('group')
            ->map(fn (Collection $items) => $items->mapWithKeys(
                fn (Setting $setting) => [$setting->key => $this->castValue($setting)]
            )->all())
            ->all();
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('dscw.settings.public');
    }

    private function castValue(Setting $setting): mixed
    {
        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $setting->value,
            'float' => (float) $setting->value,
            'json' => json_decode($setting->value ?? 'null', true),
            default => $setting->value,
        };
    }

    private function encodeValue(mixed $value, string $type): ?string
    {
        if ($value === null) {
            return null;
        }

        return match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };
    }
}
