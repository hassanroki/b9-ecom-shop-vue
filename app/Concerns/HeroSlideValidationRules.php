<?php

namespace App\Concerns;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @mixin FormRequest
 */
trait HeroSlideValidationRules
{
    protected function heroSlideRules(?int $heroSlideId = null): array
    {
        $imageSource = $this->input('image_source');

        return [
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'image_source' => ['required', Rule::in(['url', 'upload'])],
            'image' => [
                'nullable',
                'string',
                'max:2048',
                Rule::when($imageSource === 'url', ['url']),
            ],
            'image_file' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'link' => ['nullable', 'string', 'max:2048', 'url'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
