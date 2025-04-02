<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UsdExchangeRateRequest extends FormRequest
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
            'exchange_rate' => 'required|numeric',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'month_code' => '月份',
            'exchange_rate' => '美金汇率',
        ];
    }
}
