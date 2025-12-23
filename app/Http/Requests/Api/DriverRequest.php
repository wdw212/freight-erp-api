<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DriverRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'plate_number' => 'required|string',
            'name' => 'required|string',
            'phone' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes()
    {
        return [
            'plate_number' => '车牌号',
            'name' => '司机名称',
            'phone' => '电话',
        ];
    }
}
