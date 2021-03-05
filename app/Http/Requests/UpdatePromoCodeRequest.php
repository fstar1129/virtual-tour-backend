<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePromoCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'promo_code' => 'required|string|max:6|min:4',
            'discount' => 'required|numeric|min:0|max:100',
            'quantity' => 'required|numeric|min:1|max:999',
            'promo_code_limit' => 'required|numeric|min:0',
        ];
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'promo_code.max' => 'Promo code must be up to 6 characters.',
            'promo_code.min' => 'Promo code must be at least 4 characters.',
            'promo_code.*' => 'Promo code is required.',
            'discount.max' => 'Discount must be a value between 0 and 100.',
            'discount.min' => 'Discount must be a value between 0 and 100.',
            'discount.*' => 'Discount is required.',
            'quantity.max' => 'Quantity must be a value between 0 and 999.',
            'quantity.min' => 'Quantity must be a value between 0 and 999.',
            'quantity.*' => 'Quantity is required.',
            'promo_code_limit.min' => 'You have reached your maximum number of Promo codes.',
        ];
    }
}
