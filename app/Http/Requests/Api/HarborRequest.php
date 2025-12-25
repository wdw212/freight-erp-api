<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class HarborRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|string',
            'name' => 'required|string',
            'en_name' => 'required|string',
            'country' => 'required',
            'en_country' => 'required|string',
            'route' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'code' => '代码',
            'name' => '港口（中文）',
            'en_name' => '港口（英文）',
            'country' => '国家（中文）',
            'en_country' => '国家（英文）',
            'route' => '航线'
        ];
    }
}
