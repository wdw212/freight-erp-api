<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SpecialTaxRateRequest extends FormRequest
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
            'one_amount' => 'required',
            'one_tax_rate' => 'required',
            'one_handling_fee' => 'required',
            'two_amount' => 'required',
            'two_tax_rate' => 'required',
            'two_handling_fee' => 'required',
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
