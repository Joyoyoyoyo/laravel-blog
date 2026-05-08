<?php

namespace App\Http\Requests\Post;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;

class PostIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ];
    }

    /**
     * Applique les filtres valides a la query Post fournie.
     *
     * @template TModel of \App\Models\Post
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function applyFiltersTo(Builder $query): Builder
    {
        $categoryId = $this->validated('category_id');
        $dateFrom = $this->validated('date_from');
        $dateTo = $this->validated('date_to');

        return $query
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when($dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('created_at', '<=', $dateTo));
    }

    /**
     * Retourne les filtres formates pour l'UI (toujours definis, jamais null).
     *
     * @return array{category_id: int|string, date_from: string, date_to: string}
     */
    public function filtersForFront(): array
    {
        $categoryId = $this->validated('category_id');

        return [
            'category_id' => $categoryId !== null ? (int) $categoryId : '',
            'date_from' => $this->validated('date_from') ?? '',
            'date_to' => $this->validated('date_to') ?? '',
        ];
    }
}
