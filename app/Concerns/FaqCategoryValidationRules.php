<?php

namespace App\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @mixin FormRequest
 */
trait FaqCategoryValidationRules
{
    protected function faqCategoryRules(?int $faqCategoryId = null): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('faq_categories', 'slug')->ignore($faqCategoryId),
            ],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
