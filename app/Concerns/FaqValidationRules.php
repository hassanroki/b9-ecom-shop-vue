<?php

namespace App\Concerns;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @mixin FormRequest
 */
trait FaqValidationRules
{
    protected function faqRules(?int $faqId = null): array
    {
        return [
            'faq_category_id' => ['required', 'integer', 'exists:faq_categories,id'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
