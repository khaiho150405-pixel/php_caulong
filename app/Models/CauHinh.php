<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CauHinh extends Model
{
    protected $table = 'CauHinh';

    protected $fillable = [
        'Khoa',
        'GiaTri',
        'MoTa',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        return Cache::remember("cau_hinh_{$key}", 3600, function () use ($key, $default) {
            $record = static::where('Khoa', $key)->first();

            return $record?->GiaTri ?? $default;
        });
    }

    public static function set(string $key, ?string $value, ?string $mota = null): static
    {
        Cache::forget("cau_hinh_{$key}");

        return static::updateOrCreate(
            ['Khoa' => $key],
            [
                'GiaTri' => $value,
                'MoTa' => $mota,
            ]
        );
    }

    public static function getLogoUrl(): string
    {
        $logo = static::get('logo_url', 'images/HUIT.jpg');

        if (! empty($logo)) {
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                return $logo;
            }

            $normalized = ltrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $logo), DIRECTORY_SEPARATOR);
            if (file_exists(public_path($normalized))) {
                return asset($logo);
            }
            if (file_exists(storage_path('app'.DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.$normalized))) {
                return asset('storage/'.$logo);
            }

            return asset($logo);
        }

        return asset('images/HUIT.jpg');
    }
}
