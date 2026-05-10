<?php

namespace App\Support;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;

final class CategoriesCache
{
    public const KEY = 'categories.post_filters';

    /**
     * @return list<array{id: int, name: string}>
     */
    public static function all(): array
    {
        return Cache::rememberForever(self::KEY, function (): array {
            return Category::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        });
    }

    public static function forget(): void
    {
        Cache::forget(self::KEY);
    }
}
