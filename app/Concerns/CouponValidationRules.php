<?php

namespace App\Concerns;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

trait CouponValidationRules
{
    protected function couponRules(?int $couponId = null): array
    {
        return [

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'code' => [
                'required',
                'string',
                'max:100',
                Rule::unique('coupons', 'code')->ignore($couponId),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                Rule::in([
                    'fixed',
                    'percentage',
                ]),
            ],

            'value' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'minimum_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'maximum_discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'usage_limit' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'usage_per_user' => [
                'required',
                'integer',
                'min:1',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
        ];
    }

    protected function couponMessages(): array
    {
        return [
            'code.unique' => 'Coupon code already exists.',
            'expires_at.after_or_equal' => 'Expiry date must be after start date.',
            'value.min' => 'Discount value must be greater than zero.',
            'usage_per_user.min' => 'Usage per user must be at least 1.',
        ];
    }

    protected function normalizeCouponCode(): void
    {
        $this->merge([
            'code' => strtoupper(trim((string) $this->code)),
        ]);
    }

    protected function validateCouponBusinessRules(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {

            if (
                $this->type === 'percentage' &&
                (float) $this->value > 100
            ) {
                $validator->errors()->add(
                    'value',
                    'Percentage cannot exceed 100.'
                );
            }

            if (
                $this->type === 'fixed' &&
                !empty($this->maximum_discount)
            ) {
                $validator->errors()->add(
                    'maximum_discount',
                    'Maximum discount is only available for percentage coupons.'
                );
            }
        });
    }
}
