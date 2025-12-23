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
            'name' => 'required|string',
            'code' => 'required|string',
            'country' => 'required',
            'route' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'name' => '港口名称',
            'code' => '港口代码',
            'country' => '所在国家',
            'route' => '航线'
        ];
    }
}
