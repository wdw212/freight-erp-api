<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CompanyHeaderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required|string',
            'company_type_id' => 'required|integer|exists:company_types,id',
            'company_type' => 'required'
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
            'company_type' => '公司类型'
        ];
    }
}
