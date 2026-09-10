<?php

namespace App\Http\Requests\Storefront;

use App\Models\Review;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'rating'     => ['required', 'integer', 'between:1,5'],
            'comment'    => ['required', 'string', 'max:1000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $productId = $this->input('product_id');

            if (! $productId) {
                return;
            }

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // ইউজারের এমন একটা delivered order আছে কিনা যেখানে এই প্রোডাক্ট কেনা হয়েছে
            $eligibleOrder = $user->orders()
                ->where('status', 'delivered')
                ->whereHas('items', fn($q) => $q->where('product_id', $productId))
                ->first();

            if (! $eligibleOrder) {
                $validator->errors()->add('product_id', 'You can only review products you have purchased and received.');
                return;
            }

            $alreadyReviewed = Review::where('user_id', $user->id)
                ->where('product_id', $productId)
                ->exists();

            if ($alreadyReviewed) {
                $validator->errors()->add('product_id', 'You have already reviewed this product.');
                return;
            }

            // controller-এ ব্যবহারের জন্য request-এ inject করে দিচ্ছি
            $this->merge(['order_id' => $eligibleOrder->id]);
        });
    }
}
