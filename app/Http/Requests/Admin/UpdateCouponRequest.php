<?php

namespace App\Http\Requests\Admin;

use App\Concerns\CouponValidationRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCouponRequest extends FormRequest
{
    use CouponValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return $this->couponRules(
            $this->route('coupon')?->id
        );
    }

    public function messages(): array
    {
        return $this->couponMessages();
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeCouponCode();
    }

    public function withValidator(Validator $validator): void
    {
        $this->validateCouponBusinessRules($validator);
    }
}
