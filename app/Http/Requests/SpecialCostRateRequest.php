<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SpecialCostRateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'month_code' => 'required',
            'k_value' => 'required|numeric',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'month_code' => '月份'
        ];
    }
}
