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
//            'payment_method' => 'required;',
//            'cutoff_status' => 'required',
//            'bl_status' => 'required',
            'seller_id' => 'required',
        ];
    }

    /**
     * @return string[]
     */
    public function attributes(): array
    {
        return [
//            'payment_method' => '支付方式',
//            'cutoff_status' => '截单状态',
//            'bl_status' => '提单状态',
            'seller_id' => '销货单位'
        ];
    }
}
