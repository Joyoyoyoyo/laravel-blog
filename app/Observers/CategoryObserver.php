<?php

namespace App\Observers;

use App\Models\Category;
use App\Support\CategoriesCache;

class CategoryObserver
{
    public function saved(Category $category): void
    {
        CategoriesCache::forget();
    }

    public function deleted(Category $category): void
    {
        CategoriesCache::forget();
    }
}
