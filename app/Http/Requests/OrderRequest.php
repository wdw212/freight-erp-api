<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'payment_method' => 'required'
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'payment_method' => '付款方式'
        ];
    }
}
